import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    submit(event) {
        event.preventDefault();
        const form = event.target;
        const url = form.action;
        const data = new FormData(form);

        fetch(url, {
            method: "POST",
            body: data,
            headers: { "X-Requested-With": "XMLHttpRequest" }
        })
        .then(response => response.text())
        .then(html => {
            // On cherche le container parent pour remettre le recap dedans
            // On part du principe qu'on a toujours un paranet data-controller="travel-preference"
            const container = form.closest('[data-controller="travel-preference"]');
            if (container) {
                container.innerHTML = html;
            }
        });
    }
}