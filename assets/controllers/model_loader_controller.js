import { Controller } from '@hotwired/stimulus'

export default class extends Controller {
    static targets = ['brand', 'modelSelect', 'modelInput']

    connect() {
        console.log('Stimulus model-loader connecté')

        if (this.hasBrandTarget && this.hasModelSelectTarget) {
            this.brandTarget.addEventListener('change', () => this.loadModels())
            this.modelSelectTarget.addEventListener('change', () => {
                this.modelInputTarget.value = this.modelSelectTarget.value
            })
        }

        this.toggleModelField()
    }

    loadModels() {
        const brandId = this.brandTarget.value

        // Réinitialise les champs
        this.modelSelectTarget.innerHTML = ''
        const placeholderOption = document.createElement('option')
        placeholderOption.value = ''
        placeholderOption.textContent = 'Sélectionner un modèle'
        this.modelSelectTarget.appendChild(placeholderOption)
        this.modelInputTarget.value = ''

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
                    this.modelSelectTarget.appendChild(option)
                })
                this.toggleModelField()
            })
    }

    toggleModelField() {
        this.modelSelectTarget.disabled = this.brandTarget.value === ''
    }
}