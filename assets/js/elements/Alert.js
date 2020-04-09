import {slideUp, slideDown} from "../modules/animations";

export default class FloatingAlert extends HTMLElement {

    constructor(type, message, theme, btnClose) {
        super();
        this.message = message
        this.alertStyle = 'light';
        this.btnClose = '';
        if (type !== undefined) {
            this.type = type
        } else {
            this.type = this.getAttribute('type')
        }
        if (!message) {
            this.message = this.innerHTML
        }
        if (theme !== undefined) {
            if (theme === 'dark') {
                this.alertStyle = 'dark'
            }
        } else if (this.getAttribute('theme') === 'dark') {
            this.alertStyle = 'dark'
        }
        if (btnClose !== undefined && btnClose === true) {
            this.btnClose = '<button class="close">&times;</button>'
        } else if (this.getAttribute('btn-close') === 'true') {
            this.btnClose = '<button class="close">&times;</button>'
        } else {
            this.btnClose = ''
        }
        this.removeAttribute('type');
        this.removeAttribute('theme');
        this.removeAttribute('message');
        this.removeAttribute('btn-close');
    }

    connectedCallback() {
        this.innerHTML = `
        <div class="alert-element alert-${this.type} ${this.alertStyle} alert-dismiss" id="alert-message" role="alert">
            <span class="alert-text">${this.message}</span>
            ${this.btnClose}
        </div>`;
        this.querySelector('.alert-element').addEventListener('click', (e) => {
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