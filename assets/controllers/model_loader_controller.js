import { Controller } from '@hotwired/stimulus'

export default class extends Controller {
    static targets = ['brand', 'model']

    connect() {
        console.log('Stimulus model-loader connecté')

        if (this.hasBrandTarget) {
            this.brandTarget.addEventListener('change', () => this.loadModels())
        }

        this.toggleModelField()
    }

    loadModels() {
        const brandId = this.brandTarget.value

        // Remet à zéro le champ modèle, même s'il n'y a pas de marque sélectionnée
        this.modelTarget.innerHTML = ''
        const placeholderOption = document.createElement('option')
        placeholderOption.value = ''
        placeholderOption.textContent = 'Sélectionner un modèle'
        this.modelTarget.appendChild(placeholderOption)

        if (!brandId) {
            this.toggleModelField()
            return
        }

        fetch(`/models/by-brand/${brandId}`)
            .then(response => response.json())
            .then(data => {
                data.forEach(model => {
                    const option = document.createElement('option')
                    option.value = model.id
                    option.textContent = model.name
                    this.modelTarget.appendChild(option)
                })
                this.toggleModelField()
            })
    }

    toggleModelField() {
        this.modelTarget.disabled = this.brandTarget.value === ''
    }
}