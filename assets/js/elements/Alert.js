import {slideUp, slideDown} from "../modules/animations";

export class FloatingAlert extends HTMLElement {

    constructor() {
        super();
        this.type = this.getAttribute('type');
        let darkAlert = this.getAttribute('dark');
        let message = this.getAttribute('message');
        let btnClose = this.getAttribute('btn-close');
        this.alertStyle = 'light';
        if (!message) {
            this.message = this.innerHTML;
        } else {
            this.message = message;
        }
        if (darkAlert === 'true') {
            this.alertStyle = 'dark';
        }
        if (btnClose === 'true') {
            this.btnClose = '<button class="close">&times;</button>';
        } else {
            this.btnClose = ''
        }
        this.removeAttribute('type');
        this.removeAttribute('dark');
        this.removeAttribute('message');
        this.removeAttribute('btn-close');
    }

    connectedCallback() {
        this.innerHTML = `
        <div class="alert-element alert-${this.type} ${this.alertStyle} alert-dismiss" id="alert-message" role="alert">
            <span class="alert-text">${this.message}</span>
            ${this.btnClose}
        </div>`;
        this.querySelector('.alert').addEventListener('click', (e) => {
            e.preventDefault();
            this.closeOnClick()
        });
        this.close()
    }

    closeOnClick() {
        const element = this.querySelector('#alert-message');
        window.setTimeout(async () => {
            await slideDown(element);
            this.parentElement.removeChild(this)
        }, 150)
    }

    close() {
        const element = this.querySelector('#alert-message');
        window.setTimeout(async () => {
            await slideUp(element);
            this.parentElement.removeChild(this);
        }, 5000)
    }
}

customElements.define('alert-message', FloatingAlert);