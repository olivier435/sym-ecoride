import { Controller } from '@hotwired/stimulus'

export default class extends Controller {
    static targets = ['input']

    connect() {
        this.inputTarget.addEventListener('input', this.format.bind(this))
    }

    format() {
        // Supprime tout sauf les lettres et chiffres
        let value = this.inputTarget.value.toUpperCase().replace(/[^A-Z0-9]/g, '')

        // Coupe au maximum 7 caractères valides : 2 lettres + 3 chiffres + 2 lettres
        value = value.slice(0, 7)

        // Formate dynamiquement en AA-123-AA
        const match = value.match(/^([A-Z]{0,2})(\d{0,3})([A-Z]{0,2})$/)
        if (!match) {
            this.inputTarget.value = value
            return
        }

        const [, part1, part2, part3] = match
        let formatted = part1
        if (part2) formatted += '-' + part2
        if (part3) formatted += '-' + part3

        this.inputTarget.value = formatted
    }
}