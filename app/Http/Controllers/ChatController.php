<?php

namespace App\Http\Controllers;

use App\Models\ChatRoom;
use App\Models\ChatMessage;
use App\Models\User;
use App\Helpers\LogHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $salas = ChatRoom::whereHas('participantes', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->with(['ultimaMensagem', 'participantes'])->get();

        if ($user->isBibliotecario()) {
            $todasSalas = ChatRoom::with(['ultimaMensagem', 'criador'])->latest()->get();
        } else {
            $todasSalas = collect();
        }

        $utilizadores = User::where('id', '!=', $user->id)->get();

        return view('chat.index', compact('salas', 'todasSalas', 'utilizadores'));
    }

    public function show(ChatRoom $chatRoom)
    {
        $user = auth()->user();

        if (!$chatRoom->isParticipant($user) && !$user->isBibliotecario()) {
            abort(403);
        }

        $participant = $chatRoom->participantes()->where('user_id', $user->id)->first();
        if ($participant) {
            $participant->update(['ultima_visualizacao' => now()]);
        }

        ChatMessage::where('chat_room_id', $chatRoom->id)
            ->where('user_id', '!=', $user->id)
            ->where('lida', false)
            ->update(['lida' => true, 'lida_em' => now()]);

        $messages = $chatRoom->messages()->with('user')->paginate(50);
        $participantes = $chatRoom->users()->get();

        $todosUtilizadores = User::whereNotIn('id', $participantes->pluck('id'))
            ->where('id', '!=', $user->id)
            ->get();

        return view('chat.show', compact('chatRoom', 'messages', 'participantes', 'todosUtilizadores'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->isBibliotecario()) {
            abort(403);
        }

        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'participantes' => 'required|array|min:1',
            'participantes.*' => 'exists:users,id'
        ]);

        DB::transaction(function () use ($request) {
            $sala = ChatRoom::create([
                'nome' => $request->nome,
                'descricao' => $request->descricao,
                'created_by' => auth()->id(),
                'tipo' => 'privado',
            ]);

            $sala->participantes()->create([
                'user_id' => auth()->id(),
                'role' => 'admin'
            ]);

            foreach ($request->participantes as $participanteId) {
                if ($participanteId != auth()->id()) {
                    $sala->participantes()->create([
                        'user_id' => $participanteId,
                        'role' => 'member'
                    ]);
                }
            }

            LogHelper::registrar('chat', 'criar_sala', $sala->id, [
                'nome' => $sala->nome,
                'participantes' => count($request->participantes)
            ]);
        });

        return redirect()->route('chat.index')->with('sucesso', 'Sala criada com sucesso!');
    }

    public function sendMessage(Request $request, ChatRoom $chatRoom)
    {
        $user = auth()->user();

        if (!$chatRoom->isParticipant($user) && !$user->isBibliotecario()) {
            abort(403);
        }

        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $message = ChatMessage::create([
            'chat_room_id' => $chatRoom->id,
            'user_id' => $user->id,
            'message' => $request->message
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message->load('user')
            ]);
        }

        return redirect()->back()->with('sucesso', 'Mensagem enviada!');
    }

    public function addParticipant(Request $request, ChatRoom $chatRoom)
    {
        $user = auth()->user();

        if (!$chatRoom->isAdmin($user)) {
            abort(403);
        }

        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        $adicionados = 0;

        foreach ($request->user_ids as $userId) {
            if (!$chatRoom->isParticipant(User::find($userId))) {
                $chatRoom->participantes()->create([
                    'user_id' => $userId,
                    'role' => 'member'
                ]);
                $adicionados++;
            }
        }

        return redirect()->back()->with('sucesso', "$adicionados utilizador(es) adicionado(s) à sala.");
    }

    public function leave(ChatRoom $chatRoom)
    {
        $user = auth()->user();
        $chatRoom->participantes()->where('user_id', $user->id)->delete();

        return redirect()->route('chat.index')->with('sucesso', 'Saiste da sala.');
    }

    public function kickMember(ChatRoom $chatRoom, User $user)
    {
        $admin = auth()->user();

        if (!$chatRoom->isAdmin($admin)) {
            abort(403, 'Apenas administradores podem remover membros.');
        }

        if ($user->id === $admin->id) {
            return redirect()->back()->with('erro', 'Não podes remover-te a ti próprio.');
        }

        if (!$chatRoom->isParticipant($user)) {
            return redirect()->back()->with('erro', 'Este utilizador não está na sala.');
        }

        $chatRoom->participantes()->where('user_id', $user->id)->delete();

        LogHelper::registrar('chat', 'remover_membro', $chatRoom->id, [
            'sala' => $chatRoom->nome,
            'membro_removido' => $user->name
        ]);

        return redirect()->back()->with('sucesso', $user->name . ' foi removido da sala.');
    }

    public function searchUsers(Request $request)
    {
        $search = $request->get('q');

        $users = User::where('name', 'like', "%{$search}%")
            ->orWhere('email', 'like', "%{$search}%")
            ->limit(10)
            ->get(['id', 'name', 'email']);

        return response()->json($users);
    }
}
