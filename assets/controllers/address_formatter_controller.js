import { Controller } from '@hotwired/stimulus'

export default class extends Controller {
    static targets = ['input']

    connect() {
        if (this.hasInputTarget) {
            this.inputTarget.addEventListener('blur', () => this.formatAddress())
            this.element.closest('form')?.addEventListener('submit', () => this.formatAddress())
        }
    }

    formatAddress() {
        let value = this.inputTarget.value.trim()
        if (value === '') return

        // Si l'utilisateur a déjà saisi une virgule, on split sur la première
        let street = ''
        let postalAndCity = ''

        if (value.includes(',')) {
            const parts = value.split(',')
            street = this.capitalizeWords(parts[0].trim())
            postalAndCity = parts[1]?.trim().toUpperCase() ?? ''
        } else {
            // Sinon on tente de séparer à partir du code postal
            const match = value.match(/(.*?)(\d{5}\s+[A-Za-zÀ-ÿ -]+)$/u)
            if (match) {
                street = this.capitalizeWords(match[1].trim())
                postalAndCity = match[2].trim().toUpperCase()
            } else {
                // Fallback si format inconnu
                street = this.capitalizeWords(value)
            }
        }

        const finalValue = [street, postalAndCity].filter(Boolean).join(', ')
        this.inputTarget.value = finalValue
    }

    capitalizeWords(str) {
        return str
            .toLowerCase()
            .replace(/\b\w/g, c => c.toUpperCase())
    }
}