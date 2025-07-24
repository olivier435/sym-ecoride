import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';
import 'bootstrap/dist/css/bootstrap.min.css';
import '@fortawesome/fontawesome-free/css/all.css';
import 'bootstrap';

// Ferme les messages d'alerte après 5 secondes
const closeAlertMessage = () => {
    const alert = document.querySelector('.alert');
    if (alert) {
        setTimeout(() => {
            alert.classList.add('fade-out'); // Ajoute une classe CSS pour la transition
            setTimeout(() => alert.remove(), 1000); // Retire l'alerte après la transition
        }, 4000);
    }
}

const initPage = () => {
    closeAlertMessage();
}
console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');
document.addEventListener('load', initPage);
document.addEventListener('turbo:load', initPage);