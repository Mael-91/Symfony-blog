import {slideUp, slideDown} from "../../modules/animations";
import {onSubmitReply} from "../../functions/blog/AjaxComment";

export default class CommentForm extends HTMLElement {
    /**
     * @param {Number} commentId
     * @param {String} action
     * @param {String} inputValue
     * @param {String} btnClass
     * @param {String} method
     * @param {String} labelContent
     * @param {String} inputName
     * @param {String} btnContent
     */
    constructor(commentId, action, inputValue, btnClass, method, labelContent,
                inputName, btnContent) {
        super();
        this.commentId = commentId;
        this.method = method;
        this.action = action;
        this.labelContent = labelContent;
        this.inputName = inputName;
        this.inputValue = inputValue;
        this.btnClass = btnClass;
        this.btnContent = btnContent;
        if (method === undefined || method === null || method === '') {
            this.method = 'post'
        }
        if (action === undefined) {
            alert('Attribute action must be defined');
            throw new Error('Attribute action must be defined')
        }
        if (labelContent === undefined || labelContent === null || labelContent === '') {
            this.labelContent = 'Votre commentaire';
        }
        if (inputName === undefined || inputName === null || inputName === '') {
            this.inputName = '_token';
        }
        if (inputValue === undefined) {
            alert('Attribute inputValue must be defined');
            throw new Error('Attribute inputValue must be defined');
        }
        if (btnClass === undefined || btnClass === null || btnClass === '') {
            this.btnClass = 'btn btn-outline-success'
        }
        if (btnContent === undefined || btnContent === null || btnContent === '') {
            this.btnContent = 'Envoyer'
        }
        this.deleteAttribute();
    }

    connectedCallback() {
        this.alreadyExist();
        this.innerHTML = `
        <div class="comment-form">
            <form method="${this.method}" action="${this.action}" class="reply-js" id="form-reply">
                <div class="form-group">
                    <label for="comment_zone">${this.labelContent}</label>
                    <textarea class="form-control reply-zone" id="reply_zone"></textarea>
                    <input type="hidden" name="${this.inputName}" value="${this.inputValue}">
                    <button type="submit" class="${this.btnClass}" comment-id="${this.commentId}" id="btn-reply">${this.btnContent}</button>
                </div>
            </form>
        </div>
        `;
        this.div = document.querySelector('.comment-form');
        this.div.style.display = 'none';
        window.setTimeout(async () => {
            await slideDown(this.div);
        });
        document.querySelector('form.reply-js').addEventListener('submit', function (e) {
            e.preventDefault();
            const url = this.action;
            const textarea = document.querySelector('#reply_zone').value;
            const commentID = document.querySelector('#btn-reply').getAttribute('comment-id')
            const nbrComment = document.querySelector('.nbr-comment-js')
            onSubmitReply([url, textarea, commentID, nbrComment])
        })
    }

    deleteAttribute() {
        this.removeAttribute('comment-id');
        this.removeAttribute('method');
        this.removeAttribute('action');
        this.removeAttribute('label-content');
        this.removeAttribute('input-name');
        this.removeAttribute('input-value');
        this.removeAttribute('btn-class');
        this.removeAttribute('btn-content');
    }

    alreadyExist() {
        const forms = document.querySelectorAll('.comment-form');
        forms.forEach((form) => {
            form.parentElement.classList.add('remove-form');
            const del = document.querySelectorAll('.remove-form');
            for (let i = 0; i < del.length; i++) {
                window.setTimeout(async () => {
                    del[i].parentElement.removeChild(del[i]);
                    await slideUp(del[i]);
                }, 500)
            }
        })
    }
}

customElements.define('comment-form', CommentForm);