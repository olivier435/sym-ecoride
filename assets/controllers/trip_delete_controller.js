import { Controller } from '@hotwired/stimulus'

export default class extends Controller {
    static targets = ['form']

    submit(event) {
        event.preventDefault()
        if (!confirm('Confirmez-vous la suppression de ce trajet ?')) return

        const form = this.formTarget
        const url = form.action
        const formData = new FormData(form)
        const tripId = this.element.dataset.tripId

        fetch(url, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
            .then(response => {
                if (!response.ok) throw new Error('Erreur serveur')
                // Supprime visuellement la carte
                const card = this.element.closest('.q3rtro')
                card.remove()
            })
            .catch(error => {
                alert("Une erreur est survenue lors de la suppression.")
                console.error(error)
            })
    }
}