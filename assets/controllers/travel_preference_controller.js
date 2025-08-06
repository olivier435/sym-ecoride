import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ["container"];

    edit(event) {
        const url = event.currentTarget.dataset.url;
        if (!url) return;

        fetch(url, {
            headers: { "X-Requested-With": "XMLHttpRequest" }
        })
        .then(response => response.text())
        .then(html => {
            this.containerTarget.innerHTML = html;
        })
        .catch(() => {
            this.containerTarget.innerHTML = '<div class="alert alert-danger">Erreur de chargement du formulaire.</div>';
        });
    }

    reloadRecap() {
        fetch('/preferences', { headers: { "X-Requested-With": "XMLHttpRequest" } })
        .then(response => response.text())
        .then(html => {
            this.containerTarget.innerHTML = html;
        });
    }
}