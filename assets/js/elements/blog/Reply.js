class Reply extends HTMLElement {

    constructor() {
        super();
        this.div = document.createElement('div')
        this.div.classList.add('reply-action')
        this.div.setAttribute('id','reply_form')
        this.appendChild(this.div)
        this.form = document.createElement('form')
        this.form.setAttribute('action', window.location.href + '/reply/' + 'ee')
        this.form.setAttribute('method', 'post')
        this.setAttribute('name', 'reply_form')
        this.setAttribute('id', 'reply-form')
        this.classList.add('form-group', 'reply-form')
        this.div.appendChild(this.form)
        this.label = document.createElement('label')
        this.label.setAttribute('for', 'reply_comment')
        this.label.classList.add('required')
        this.label.textContent = 'Vote réponse'
        this.form.appendChild(this.label)
        this.textarea = document.createElement('textarea')
        this.textarea.classList.add('form-control', 'autoExpand', 'reply-zone')
        this.textarea.setAttribute('id', 'reply_comment')
        this.textarea.setAttribute('name', 'reply_zone')
        this.form.appendChild(this.textarea)
        this.input = document.createElement('input')
        this.input.setAttribute('type', 'hidden')
        this.input.setAttribute('name', '_csrf_token_reply')
        this.input.setAttribute('value', '{{ csrf_token(\'reply_comment\' ~ comment.id) }}')
        this.input.setAttribute('id', 'reply_csrf')
        this.form.appendChild(this.input)
        this.button = document.createElement('button')
        this.button.classList.add('btn', 'btn-success', 'reply-btn')
        this.button.setAttribute('type', 'submit')
        this.button.setAttribute('id', 'send-reply')
        this.button.textContent = 'Répondre'
        this.form.appendChild(this.button)
        console.log(this.getID())
    }

    getID() {
        this.idComment = document.querySelectorAll('.comment')
        for (let e = 0; e < this.idComment; e++) {
            console.log(elem.item(e))
        }
    }
}

customElements.define('reply-form', Reply)

const elem = document.querySelectorAll('button.show-reply-zone')
for (let i = 0; i < elem.length; i++) {
    elem[i].addEventListener('click', function () {
        const comment = elem.item(i).parentNode.parentNode.parentNode.parentNode
        comment.appendChild(new Reply())
    })
}