import { Controller } from "@hotwired/stimulus";
import {
    evaluatePasswordStrength,
    updateEntropy,
    bindPasswordGenerator
} from "../js/modules/passwordUtils.js";

export default class extends Controller {
    static targets = ["passwordInput", "entropy", "generateBtn", "submitBtn"];

    connect() {
        // L'event sur le champ de mot de passe
        if (this.hasPasswordInputTarget) {
            this.passwordInputTarget.addEventListener("input", this.checkPass.bind(this));
        }
        // Génération du mot de passe
        if (this.hasGenerateBtnTarget && this.hasPasswordInputTarget) {
            bindPasswordGenerator(this.generateBtnTarget, this.passwordInputTarget);
        }
        // Désactivation initiale du bouton
        if (this.hasSubmitBtnTarget) {
            this.submitBtnTarget.disabled = true;
        }
    }

    checkPass() {
        const value = this.passwordInputTarget.value;
        const entropy = evaluatePasswordStrength(value);
        const isStrong = updateEntropy(this.entropyTarget, entropy);

        // Désactivation/activation du bouton submit
        if (this.hasSubmitBtnTarget) {
            this.submitBtnTarget.disabled = !isStrong;
        }
    }
}