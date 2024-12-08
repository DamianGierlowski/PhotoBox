// assets/controllers/modal_controller.js
import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['content'];

    connect() {
        console.log('2');
        this.contentTarget.style.display = 'none';
    }

    open(event) {
        console.log('Opening modal'); // Debugging log
        event.preventDefault();
        this.contentTarget.style.display = 'block';
        document.body.classList.add('modal-open');
    }

    close(event) {
        console.log('Closing modal'); // Debugging log
        event.preventDefault();
        this.contentTarget.style.display = 'none';
        document.body.classList.remove('modal-open');
    }

    submit(event) {
        event.preventDefault();
        const form = this.element.querySelector('form');

        fetch(form.action, {
            method: form.method,
            body: new FormData(form),
        })
            .then((response) => response.text())
            .then((html) => {
                // Handle response
                this.close();
                // Optionally update content on the page
                document.querySelector('#assignments-list').innerHTML = html;
            });
    }
}
