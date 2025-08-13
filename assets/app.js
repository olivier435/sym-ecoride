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
import 'bootstrap-icons/font/bootstrap-icons.min.css';
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

// Fonction permettant de sélectionner les éléments du DOM
const select = (el, all = false) => {
    el = el.trim()
    if (all) {
        return [...document.querySelectorAll(el)]
    } else {
        return document.querySelector(el)
    }
}

// Easy on scroll event listener
const onscroll = (el, listener) => {
    el.addEventListener('scroll', listener)
}

// Back to top button
const backToTop = () => {
    const backtotop = select('.back-to-top');
    if (backtotop) {
        const toggleBacktotop = () => {
            backtotop.classList.toggle('active', window.scrollY > 100);
        }
        window.addEventListener('load', toggleBacktotop);
        onscroll(document, toggleBacktotop);
    }
}

const bootstrapDropdownPatch = () => {
    document.querySelectorAll('.dropdown-toggle[data-bs-toggle="dropdown"]').forEach(dd => {
        // Pour éviter de dupliquer les event listeners, on commence par les retirer
        dd.replaceWith(dd.cloneNode(true));
    });
    document.querySelectorAll('.dropdown-toggle[data-bs-toggle="dropdown"]').forEach(dd => {
        dd.addEventListener('click', function(e) {
            e.preventDefault();
            const Dropdown = window.bootstrap?.Dropdown;
            if (!Dropdown) {
                return;
            }
            const instance = Dropdown.getOrCreateInstance(dd);
            instance.toggle();
        });
    });
};

const initPage = () => {
    closeAlertMessage();
    backToTop();
    bootstrapDropdownPatch();
}
console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');
document.addEventListener('load', initPage);
document.addEventListener('turbo:load', initPage);