document.addEventListener('DOMContentLoaded', function() {

    // Placeholder barre de recherche fixe
    const searchPlaceholder = document.querySelector('.search-placeholder');
    if (searchPlaceholder) {
        searchPlaceholder.textContent = 'Paris, FR';
    }

    const resultsCounter = document.querySelector('.results-counter .results-content');

    if (resultsCounter) {
        const parentEl = resultsCounter.parentElement;
        const nasaText = parentEl.dataset.nasaText;
        const apolloText = parentEl.dataset.apolloText;

        function animateResults() {
            // Phase NASA (0% - 35.7%)
            setTimeout(() => {
                resultsCounter.innerHTML = '<strong class="text-blue-600">30</strong> ' + nasaText.replace('30 ', '');
            }, 0);

            // Phase Apollo (43% - 85.7%)
            setTimeout(() => {
                resultsCounter.innerHTML = '<strong class="text-purple-600">31</strong> ' + apolloText.replace('31 ', '');
            }, 2580); // 43% de 6000ms

            // Retour à NASA (92.8%)
            setTimeout(() => {
                resultsCounter.innerHTML = '<strong class="text-blue-600">30</strong> ' + nasaText.replace('30 ', '');
            }, 5570); // 92.8% de 6000ms
        }

        // Démarrer l'animation
        animateResults();

        // Répéter l'animation toutes les 6 secondes
        setInterval(animateResults, 6000);
    }

});
