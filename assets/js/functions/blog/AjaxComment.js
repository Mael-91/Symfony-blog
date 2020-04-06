import {appendNewComment, appendNewReply} from '../../modules/blog/Comment.js'
const axios = require('axios');

document.querySelectorAll('form.comment-js').forEach(function (link) {
    link.addEventListener('submit', onSubmitComment);
})

function onSubmitComment(event) {
    event.preventDefault();

    const form = this
    const url = form.action
    const textarea = document.getElementById('comment_text')
    const token = document.getElementById('csrf')
    const nbrComment = document.querySelector('.nbr-comment-js')

    axios.post(url, {
        content: textarea.value,
        csrf: token.value
    }).then(function (response) {
        nbrComment.textContent = response.data.nbrComment
        textarea.value = ''
        appendNewComment(response.data.comment)
    }).catch(function (error) {
        if (error.response.status === 400) {
            window.alert('erreur 400')
        } else {
            window.alert('autre erreur')
        }
    })
}

document.querySelectorAll('form.reply-form').forEach(function (link) {
    link.addEventListener('submit', onSubmitReply)
})

function onSubmitReply(event) {
    event.preventDefault();

    const form = this
    const url = form.action
    const textarea = document.getElementById('reply_comment')
    const token = document.getElementById('reply_csrf')
    const nbrComment = document.querySelector('.nbr-comment-js')
    axios.post(url, {
        content: textarea.value,
        csrf: token.value
    }).then(function (response) {
        nbrComment.textContent = response.data.nbrComment
        textarea.value = ''
        appendNewReply(response.data.reply)
    }).catch(function (error) {
        if (error.response.status === 400) {
            window.alert('erreur 400')
        } else {
            window.alert('autre erreur')
        }
    })
}