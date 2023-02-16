// Récupération de tous les éléments de contenu
var contentElements = document.querySelectorAll(".content");

// Fonction qui affiche les éléments progressivement
function showContent() {
    contentElements.forEach(function(element, index) {
        // Calcule le pourcentage de la page qui a été défilé
        let scrollPercentage = window.scrollY / (document.body.scrollHeight - window.innerHeight);

        // Calcule l'opacité à appliquer à l'élément
        let opacity = Math.min(1, scrollPercentage * 3 - (index * 0.5));

        // Applique l'opacité à l'élément
        element.style.opacity = opacity;
    });
}

// Attache un gestionnaire d'événements pour appeler la fonction showContent() lorsque l'utilisateur fait défiler la page
window.addEventListener("scroll", showContent);
