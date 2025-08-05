import {
    Controller
} from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ["input"];

    connect() {
        if (this.hasInputTarget) {
            this.inputTarget.addEventListener("blur", () => this.formatAddress());
            this.element
                .closest("form")
                ?.addEventListener("submit", () => this.formatAddress());
        }
    }

    formatAddress() {
        let value = this.inputTarget.value.trim();
        if (value === "") return;

        let street = "";
        let postalAndCity = "";

        // Cas 1 : format avec code postal identifiable
        const match = value.match(/^(.*?)(\d{5}\s+[A-Za-zÀ-ÿ \-']+)$/u);
        if (match) {
            street = this.cleanAndCapitalizeStreet(match[1]);
            postalAndCity = match[2].trim().toUpperCase();
        } else {
            // Cas 2 : au moins une virgule
            const parts = value.split(",").map(p => p.trim()).filter(Boolean);
            if (parts.length >= 2) {
                postalAndCity = parts.pop().toUpperCase();
                street = this.cleanAndCapitalizeStreet(parts.join(" "));
            } else {
                // Cas 3 : fallback
                street = this.cleanAndCapitalizeStreet(value);
            }
        }

        const finalValue = [street, postalAndCity].filter(Boolean).join(", ");
        this.inputTarget.value = finalValue;
    }

    cleanAndCapitalizeStreet(str) {
        return str
            .replace(/,+/g, "") // retire toutes les virgules
            .replace(/\s+/g, " ") // espace propre
            .trim()
            .toLocaleLowerCase("fr-FR")
            .replace(/([\p{L}]+)/gu, (word) =>
                word.charAt(0).toLocaleUpperCase("fr-FR") + word.slice(1)
            );
    }
}