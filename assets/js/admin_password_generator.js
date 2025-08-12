import { generateStrongPassword } from './modules/passwordUtils.js';

document.addEventListener('DOMContentLoaded', () => {
    // Sélectionne le champ password (id généré par EasyAdmin !)
    const passwordField = document.getElementById('User_plainPassword');
    if (!passwordField) return;

    // Ajoute le bouton juste après le champ
    const btn = document.createElement('button');
    btn.type = "button";
    btn.className = "btn btn-outline-secondary mt-2";
    btn.innerHTML = '<i class="bi bi-shuffle"></i> <span>Générer</span>';
    btn.id = "btn-generate-password";

    passwordField.parentNode.appendChild(btn);

    // Ajoute la logique
    btn.addEventListener('click', () => {
        const icon = btn.querySelector('i');
        const label = btn.querySelector('span');

        icon.classList.remove("bi-shuffle");
        icon.classList.add("bi-arrow-repeat", "spin");

        const newPassword = generateStrongPassword(24);
        passwordField.value = newPassword;
        passwordField.dispatchEvent(new Event('input'));

        navigator.clipboard.writeText(newPassword).then(() => {
            icon.className = "bi bi-clipboard-check text-success";
            label.textContent = "Copié !";
            setTimeout(() => {
                label.textContent = "Générer";
                icon.className = "bi bi-shuffle";
            }, 2000);
        }).catch(() => {
            icon.className = "bi bi-exclamation-triangle text-danger";
            label.textContent = "Erreur";
            setTimeout(() => {
                label.textContent = "Générer";
                icon.className = "bi bi-shuffle";
            }, 2000);
        });
    });
});