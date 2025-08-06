import { Controller } from "@hotwired/stimulus"

export default class extends Controller {
    static targets = [
        "form", "results", "departureCity", "arrivalCity", "date",
        "filters", "sort", "priceMax", "eco", "smoking", "pets"
    ]

    connect() {
        // Rafraîchir les résultats au submit principal
        if (this.hasFormTarget) {
            this.formTarget.addEventListener("submit", this.submit.bind(this))
        }

        // Rafraîchir au changement d'un filtre
        if (this.hasFiltersTarget) {
            this.filtersTarget.addEventListener("change", () => this.formTarget.requestSubmit())
            this.filtersTarget.addEventListener("reset", this.onFilterReset.bind(this))
        }

        // Rafraîchir aussi au changement des selects principaux (ville, date)
        if (this.hasDepartureCityTarget) this.departureCityTarget.addEventListener("change", () => this.formTarget.requestSubmit())
        if (this.hasArrivalCityTarget) this.arrivalCityTarget.addEventListener("change", () => this.formTarget.requestSubmit())
        if (this.hasDateTarget) this.dateTarget.addEventListener("change", () => this.formTarget.requestSubmit())

        // Optionnel : déclenche une première recherche si des valeurs sont déjà remplies
        // this.formTarget.requestSubmit();
    }

    submit(event) {
        event.preventDefault()

        const params = new URLSearchParams({
            departureCity: this.departureCityTarget.value,
            arrivalCity: this.arrivalCityTarget.value,
            date: this.dateTarget.value,
            // Filtres
            sort: this._getRadioValue(this.sortTargets),
            priceMax: this.hasPriceMaxTarget ? this.priceMaxTarget.value : "",
            eco: this.hasEcoTarget ? this.ecoTarget.checked : false,
            smoking: this.hasSmokingTarget ? this.smokingTarget.checked : false,
            pets: this.hasPetsTarget ? this.petsTarget.checked : false
        })

        fetch(`/search/ajax?${params.toString()}`, {
            headers: {
                "X-Requested-With": "XMLHttpRequest"
            }
        })
        .then(res => res.json())
        .then(data => this.renderResults(data))
        .catch(err => this.renderError())
    }

    _getRadioValue(radioTargets) {
        if (!radioTargets) return null;
        const checked = radioTargets.find(input => input.checked);
        return checked ? checked.value : null;
    }

    renderResults(data) {
        if (data.trips && data.trips.length > 0) {
            this.resultsTarget.innerHTML = `
                <h2>${data.trips.length} résultat${data.trips.length > 1 ? 's' : ''} trouvé${data.trips.length > 1 ? 's' : ''}</h2>
                <div class="row mt-4">
                ${data.trips.map(trip => `
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm fgfump ${trip.isFull ? 'opacity-50 pointer-events-none' : ''}">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex flex-row justify-content-between align-items-start flex-nowrap mb-3 w-100 sjlkpn">
                                    <span class="nhzxx0">
                                        <div class="lb096f">                                    
                                            <img src="${trip.driver.avatar || '/images/avatars/default.png'}" alt="Photo de profil" class="rounded-circle me-3" width="50" height="50">
                                            <div>
                                                <h5 class="mb-0">${trip.driver.pseudo}</h5>
                                                <small class="text-muted">Note : ⭐</small>
                                            </div>
                                        </div>                                    
                                    </span>
                                        ${trip.isFull ? `<span class="ht20ro"><p class="k2086f mb-0">Complet</p></span>` : ''}
                                </div>
                                <p>
                                    <strong>Départ :</strong> ${trip.departureDate} à ${trip.departureTime}<br>
                                    <strong>Arrivée :</strong> ${trip.arrivalDate} à ${trip.arrivalTime}
                                </p>
                                <p>
                                    <strong>De :</strong> ${trip.departureAddress}<br>
                                    <strong>À :</strong> ${trip.arrivalAddress}
                                </p>
                                <p>
                                    🚗 <strong>${trip.seatsAvailable}</strong> place${trip.seatsAvailable > 1 ? 's' : ''} restante${trip.seatsAvailable > 1 ? 's' : ''}<br>
                                    💰 <strong>${trip.pricePerPerson} €</strong> par personne<br>
                                    ${trip.isEco ? `<span class="text-success fw-bold">🌱 Voyage écologique</span>` : `<span class="text-muted">🚘 Classique</span>`}
                                </p>
                                ${trip.isFull
                                    ? ''
                                    : `<a href="#" class="btn btn-outline-primary mt-auto">Détail</a>`
                                }
                            </div>
                        </div>
                    </div>
                `).join('')}
                </div>
            `
        } else if (data.nextAvailableDate) {
            this.resultsTarget.innerHTML = `
                <div class="alert alert-info mt-4">
                    Aucun trajet trouvé pour cette date, mais un trajet est disponible le
                    <strong>${this.formatDateFr(data.nextAvailableDate)}</strong>.<br>
                    <button type="button" class="btn btn-sm btn-primary mt-2" data-action="click->trip-search#nextDate" data-date="${data.nextAvailableDate}">Voir ce jour</button>
                </div>
            `
        } else {
            this.resultsTarget.innerHTML = `
                <div class="alert alert-warning mt-4">
                    Aucun covoiturage ne correspond à votre recherche.
                </div>
            `
        }
    }

    renderError() {
        this.resultsTarget.innerHTML = `<div class="alert alert-danger mt-4">Une erreur est survenue.</div>`
    }

    nextDate(event) {
        this.dateTarget.value = event.target.dataset.date
        this.formTarget.requestSubmit()
    }

    formatDateFr(isoDate) {
        const [year, month, day] = isoDate.split('-')
        return `${day}/${month}/${year}`
    }

    onFilterReset(event) {
        // Laisse le navigateur réinitialiser les champs, puis relance la recherche (petit délai)
        setTimeout(() => {
            this.submit(new Event('submit'))
        }, 10)
    }
}