import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
  static targets = ["container"];
  static values = {
    url: String, // tu peux ne pas l'utiliser, on lit l'URL sur le bouton
    detailUrl: String, // optionnel, pour le bouton retour
    slug: String,
    tripId: Number,
  };

  // Méthode appelée par data-action="participation#participate"
  async participate(event) {
    event.preventDefault();

    // Récupère l'URL de la page récap depuis le bouton
    const url = event.currentTarget.dataset.participationUrlValue;
    if (!url) return;

    // Remplacement AJAX du contenu principal par le récap
    this.loadHtml(url);
  }

  // Méthode appelée par data-action="participation#showDetail"
  async showDetail(event) {
    event?.preventDefault();

    // Tu peux soit recharger la page, soit refaire un fetch AJAX vers la page détail (préférable !)
    // On suppose que tu as un data-participation-detail-url-value (passé depuis Twig), sinon window.location.reload()
    const detailUrl = this.detailUrlValue || window.location.pathname;
    this.loadHtml(detailUrl);
  }

  // Méthode générique pour charger du HTML dans le container cible
  async loadHtml(url) {
    this.containerTarget.innerHTML = `<div class="text-center p-4"><div class="spinner-border"></div></div>`;
    try {
      const response = await fetch(url, {
        headers: { "X-Requested-With": "XMLHttpRequest" },
      });

      if (!response.ok) {
        let message = "Erreur serveur.";
        try {
          const data = await response.json();
          message = data.error || message;
        } catch {}
        this.containerTarget.innerHTML = `<div class="alert alert-danger">${message}</div>`;
        return;
      }

      const html = await response.text();
      this.containerTarget.innerHTML = html;
      this.containerTarget.scrollIntoView({ behavior: "smooth" });
    } catch {
      this.containerTarget.innerHTML = `<div class="alert alert-danger">Erreur lors du chargement.</div>`;
    }
  }

  async showSecondConfirmation(event) {
    const tripId = this.tripIdValue;
    const slug = this.slugValue;
    event.preventDefault();
    const url = `/trip/detail/${tripId}-${slug}/reservation/confirm`;
    this.loadHtml(url);
  }

  async finalReservation(event) {
    event.preventDefault();
    const tripId = this.tripIdValue;
    const slug = this.slugValue;
    const url = `/trip/detail/${tripId}-${slug}/reservation/book`;

    const btn = event.currentTarget;
    btn.disabled = true;
    btn.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Réservation...`;

    try {
      const response = await fetch(url, {
        method: "POST",
        headers: {
          "X-Requested-With": "XMLHttpRequest",
          "Content-Type": "application/json",
          Accept: "application/json",
        },
      });

      if (!response.ok) {
        let message = "Erreur serveur.";
        try {
          const data = await response.json();
          message = data.error || message;
        } catch {}
        this.containerTarget.innerHTML = `<div class="alert alert-danger">${message}</div>`;
        btn.disabled = false;
        btn.innerHTML = `<span>Confirmer la réservation</span>`;
        return;
      }

      // Succès
      // window.location.reload();
      // Succès → redirection vers l'historique des trajets passager
      window.location.href = "/mes-trajets/passager/";
    } catch {
      this.containerTarget.innerHTML = `<div class="alert alert-danger">Erreur lors du chargement.</div>`;
      btn.disabled = false;
      btn.innerHTML = `<span>Confirmer la réservation</span>`;
    }
  }
}