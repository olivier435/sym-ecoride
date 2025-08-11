import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
  static targets = ["form", "spinner"];

  async submit(event) {
    event.preventDefault();
    if (!confirm("Confirmez-vous la suppression de ce trajet ?")) return;

    const form = this.formTarget;
    const spinner = this.hasSpinnerTarget ? this.spinnerTarget : null;

    if (spinner) spinner.classList.remove('d-none');

    const url = form.action;
    const formData = new FormData(form);

    try {
      const response = await fetch(url, {
        method: "POST",
        headers: {
          "X-Requested-With": "XMLHttpRequest",
        },
        body: formData,
      });

      if (!response.ok) throw new Error("Erreur serveur");

      const data = await response.json();
      if (data.redirect) {
        window.location.href = data.redirect;
        return;
      }
      // Fallback : on supprime la card visuellement (si pas de redirect)
      const card = this.element.closest(".fqpm9p");
      if (card) card.remove();
    } catch (error) {
      alert("Une erreur est survenue lors de la suppression.");
      console.error(error);
    } finally {
      if (spinner) spinner.classList.add('d-none');
    }
  }
}