import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
  static targets = ["container"];

  /**
   * Ouvre le formulaire de réclamation en AJAX
   */
  async edit(event) {
    event.preventDefault();
    const url = event.currentTarget.dataset.url;
    if (!url) return;

    this.containerTarget.innerHTML = `<div class="text-center my-4"><div class="spinner-border" role="status"><span class="visually-hidden">Chargement…</span></div></div>`;

    try {
      const response = await fetch(url, {
        headers: { "X-Requested-With": "XMLHttpRequest" },
      });
      if (!response.ok) throw new Error("Erreur serveur");
      const html = await response.text();
      this.containerTarget.innerHTML = html;
    } catch (e) {
      this.containerTarget.innerHTML = `<div class="alert alert-danger">Impossible de charger le formulaire.</div>`;
    }
  }

  /**
   * Recharge le récapitulatif (après annulation ou soumission)
   */
  async reloadRecap(event) {
    event.preventDefault();
    const url = window.location.href; // la page index (GET)

    this.containerTarget.innerHTML = `<div class="text-center my-4"><div class="spinner-border" role="status"><span class="visually-hidden">Chargement…</span></div></div>`;

    try {
      const response = await fetch(url, {
        headers: { "X-Requested-With": "XMLHttpRequest" },
      });
      if (!response.ok) throw new Error("Erreur serveur");
      const html = await response.text();
      this.containerTarget.innerHTML = html;
    } catch (e) {
      this.containerTarget.innerHTML = `<div class="alert alert-danger">Impossible de recharger le récapitulatif.</div>`;
    }
  }

  goBack(event) {
    event.preventDefault();
    const url = event.currentTarget.dataset.url;
    if (url) {
      window.location.href = url;
    } else if (document.referrer) {
      window.location.href = document.referrer;
    } else {
      window.history.back();
    }
  }
}