import { Controller } from '@hotwired/stimulus'

export default class extends Controller {
    static targets = ['input', 'display']

    connect() {
        this.updateDisplay()
    }

    increment() {
        const value = Math.min(4, this.value + 1)
        this.setValue(value)
    }

    decrement() {
        const value = Math.max(1, this.value - 1)
        this.setValue(value)
    }

    setValue(value) {
        this.inputTarget.value = value
        this.updateDisplay()
    }

    updateDisplay() {
        this.displayTarget.textContent = this.inputTarget.value
    }

    get value() {
        return parseInt(this.inputTarget.value) || 1
    }
}