import { Controller } from "@hotwired/stimulus"

export default class extends Controller {
    static targets = ["form", "results", "departureCity", "arrivalCity", "date"]

    connect() {
        this.formTarget.addEventListener("submit", this.submit.bind(this))
    }

    submit(event) {
        event.preventDefault()

        const params = new URLSearchParams({
            departureCity: this.departureCityTarget.value, // value = id de la City
            arrivalCity: this.arrivalCityTarget.value,
            date: this.dateTarget.value,
        })

        fetch(`/search/ajax?${params}`, {
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                }
            })
            .then(res => res.json())
            .then(data => this.renderResults(data))
            .catch(err => this.renderError())
    }

    renderResults(data) {
        if (data.trips.length > 0) {
            this.resultsTarget.innerHTML = `
                <h2>${data.trips.length} résultat${data.trips.length > 1 ? 's' : ''} trouvé${data.trips.length > 1 ? 's' : ''}</h2>
                <div class="row mt-4">
                ${data.trips.map(trip => `
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex align-items-center mb-3">
                                    <img src="${trip.driver.avatar}" alt="Photo de profil" class="rounded-circle me-3" width="50" height="50">
                                    <div>
                                        <h5 class="mb-0">${trip.driver.pseudo}</h5>
                                        <small class="text-muted">Note : ⭐</small>
                                    </div>
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
                                <a href="#" class="btn btn-outline-primary mt-auto">Détail</a>
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
}