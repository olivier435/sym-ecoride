document.addEventListener('DOMContentLoaded', function () {
    const firstname = document.getElementById('User_firstname');
    const lastname = document.getElementById('User_lastname');
    const pseudo = document.getElementById('User_pseudo');

    function slugify(str) {
        return str
            .toLowerCase()
            .normalize('NFD').replace(/[\u0300-\u036f]/g, '') // accents
            .replace(/[^a-z0-9]/g, '-');
    }

    function generatePseudo() {
        if (firstname && lastname && pseudo) {
            let base = slugify(firstname.value) + '-' + slugify(lastname.value);
            let rand = Math.floor(Math.random() * 900 + 100); // 3 chiffres
            pseudo.value = base + rand;
        }
    }

    if (firstname) firstname.addEventListener('input', generatePseudo);
    if (lastname) lastname.addEventListener('input', generatePseudo);

    // Remplissage initial au chargement
    generatePseudo();
});