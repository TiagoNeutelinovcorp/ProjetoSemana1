import './bootstrap';
import '../css/app.css';

// Sem Vue, sem Inertia - apenas CSS e bootstrap
console.log('App carregado com sucesso!');

import './bootstrap';
import '../css/app.css';

// Efeito 3D para os cards de imagem
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.hover-3d').forEach(card => {
        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            const centerX = rect.width / 2;
            const centerY = rect.height / 2;

            const rotateX = (y - centerY) / 15;
            const rotateY = (centerX - x) / 15;

            card.style.setProperty('--_x', rotateX + 'deg');
            card.style.setProperty('--_y', rotateY + 'deg');
        });

        card.addEventListener('mouseleave', () => {
            card.style.setProperty('--_x', '0deg');
            card.style.setProperty('--_y', '0deg');
        });
    });
});
