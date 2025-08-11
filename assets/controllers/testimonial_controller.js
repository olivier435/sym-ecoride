import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = [
        "ratingInput",
        "starRating",
        "textarea",
        "countSpan"
    ];

    connect() {
        this.maxCount = 300;

        // Initialisation des deux fonctionnalités
        this.initStarRating();
        this.initTextAreaWatcher();
    }

    // --- Gestion des étoiles ---
    initStarRating() {
        if (!this.hasStarRatingTarget || !this.hasRatingInputTarget) return;

        // Valeur par défaut
        if (!this.ratingInputTarget.value) {
            this.ratingInputTarget.value = 5;
        }

        this.updateStars(this.ratingInputTarget.value);

        this.starRatingTarget.addEventListener('mouseover', (event) => {
            const target = event.target.closest('i');
            if (!target) return;
            const ratingValue = target.getAttribute('data-rating');
            this.updateStars(ratingValue);
        });

        this.starRatingTarget.addEventListener('click', (event) => {
            const target = event.target.closest('i');
            if (!target) return;
            const ratingValue = target.getAttribute('data-rating');
            this.ratingInputTarget.value = ratingValue;
            this.updateStars(ratingValue);
        });

        this.starRatingTarget.addEventListener('mouseleave', () => {
            // Quand la souris quitte les étoiles, on restaure l'état selon la note enregistrée
            this.updateStars(this.ratingInputTarget.value);
        });
    }

    updateStars(value) {
        this.starRatingTarget.querySelectorAll('i').forEach(star => {
            const starValue = star.getAttribute('data-rating');
            if (parseInt(starValue) <= parseInt(value)) {
                star.classList.remove('far');
                star.classList.add('fas');
            } else {
                star.classList.remove('fas');
                star.classList.add('far');
            }
        });
    }

    // --- Gestion du compteur de caractères ---
    initTextAreaWatcher() {
        if (!this.hasTextareaTarget || !this.hasCountSpanTarget) return;

        this.updateCount();

        this.textareaTarget.addEventListener('input', () => {
            this.updateCount();
        });

        const form = this.textareaTarget.closest('form');
        if (form) {
            form.addEventListener('submit', () => {
                setTimeout(() => this.updateCount(), 0);
            });
        }
    }

    updateCount() {
        const count = this.textareaTarget.value.length;
        this.countSpanTarget.textContent = this.maxCount - count;

        if (count >= this.maxCount) {
            this.textareaTarget.classList.add('textarea-name-error');
        } else {
            this.textareaTarget.classList.remove('textarea-name-error');
        }
    }
}