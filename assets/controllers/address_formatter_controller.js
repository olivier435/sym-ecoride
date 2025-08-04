import { Controller } from '@hotwired/stimulus'

export default class extends Controller {
    static targets = ['input']

    connect() {
        if (this.hasInputTarget) {
            // Format lors du blur (perte de focus)
            this.inputTarget.addEventListener('blur', () => this.formatAddress())

            // Format même si l'utilisateur clique directement sur "Continuer"
            this.element.closest('form')?.addEventListener('submit', () => this.formatAddress())
        }
    }

    formatAddress() {
        let value = this.inputTarget.value.trim()

        if (value === '') return

        const parts = value.split(',')
        const street = parts[0] ? this.capitalizeWords(parts[0].trim()) : ''
        const postalAndCity = parts[1] ? parts[1].trim().toUpperCase() : ''

        const finalValue = [street, postalAndCity].filter(Boolean).join(', ')
        this.inputTarget.value = finalValue
    }

    capitalizeWords(str) {
        return str
            .toLowerCase()
            .replace(/\b\w/g, c => c.toUpperCase()) // première lettre de chaque mot
    }
}