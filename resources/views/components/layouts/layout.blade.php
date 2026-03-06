<!doctype html>
<html lang="pt" data-theme="dim">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-base-100">
<x-layouts.nav />
<main class="max-w-7xl mx-auto mt-6 px-4">
    {{ $slot }}
</main>
</body>
</html>
