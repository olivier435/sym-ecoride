// VERSION DU CONTROLLER STIMULUS POUR DES PRIX EN EUROS
// import { Controller} from '@hotwired/stimulus';

// export default class extends Controller {
//     static targets = ['input'];

//     connect() {
//         console.log('[Stimulus] price-controller connecté');
//     }

//     increment() {
//         let value = this.parseValue(this.inputTarget.value);
//         value += 1;
//         this.inputTarget.value = value.toFixed(2).replace('.', ',');
//     }

//     decrement() {
//         let value = this.parseValue(this.inputTarget.value);
//         if (value > 1) {
//             value -= 1;
//             this.inputTarget.value = value.toFixed(2).replace('.', ',');
//         }
//     }

//     parseValue(val) {
//         // Convertit "10,00" → 10.00
//         const normalized = val.replace(',', '.');
//         const parsed = parseFloat(normalized);
//         return isNaN(parsed) ? 0 : parsed;
//     }
// }

import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['input'];

    connect() {
        console.log('[Stimulus] price-controller connecté (mode crédits)');
    }

    increment() {
        let value = this.parseValue(this.inputTarget.value);
        value += 1;
        this.inputTarget.value = value;
    }

    decrement() {
        let value = this.parseValue(this.inputTarget.value);
        if (value > 1) {
            value -= 1;
            this.inputTarget.value = value;
        }
    }

    parseValue(val) {
        // On force l'entier (parseInt) et on filtre les caractères non numériques
        const normalized = val.replace(/[^\d]/g, '');
        const parsed = parseInt(normalized, 10);
        return isNaN(parsed) ? 1 : parsed;
    }
}