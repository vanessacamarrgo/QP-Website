document.querySelectorAll('.faq-question').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var answer = this.nextElementSibling;
        var isOpen = this.getAttribute('aria-expanded') === 'true';

        document.querySelectorAll('.faq-question').forEach(function(b) {
            b.setAttribute('aria-expanded', 'false');
            b.nextElementSibling.classList.remove('open');
        });

        if (!isOpen) {
            this.setAttribute('aria-expanded', 'true');
            answer.classList.add('open');
        }
    });
});

var btnShowMore = document.getElementById('btn-show-more');
var isExpanded = false;

btnShowMore.addEventListener('click', function() {
    var extras = document.querySelectorAll('.faq-extra');

    isExpanded = !isExpanded;
    extras.forEach(function(item) {
        item.classList.toggle('hidden');
    });

    btnShowMore.textContent = isExpanded ? 'Ver menos' : 'Ver mais perguntas';
});
document.addEventListener('DOMContentLoaded', () => {
    const btnSwap = document.querySelector('.btn-swap-cities');
    const inputOrigin = document.getElementById('origin');
    const inputDestination = document.getElementById('destination');

    if (btnSwap && inputOrigin && inputDestination) {
        btnSwap.addEventListener('click', () => {
            // 1. Guarda o valor da origem em uma variável temporária
            const tempValue = inputOrigin.value;

            // 2. Passa o valor do destino para a origem
            inputOrigin.value = inputDestination.value;

            // 3. Passa o valor guardado (antiga origem) para o destino
            inputDestination.value = tempValue;

            // Opcional: Adiciona uma pequena animação de rotação no botão ao clicar
            btnSwap.style.transform = btnSwap.style.transform === 'translateY(-50%) rotate(180deg)'
                ? 'translateY(-50%) rotate(0deg)'
                : 'translateY(-50%) rotate(180deg)';
        });
    }
});