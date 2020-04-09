import Comment from "../../elements/blog/Comment";
import FloatingAlert from "../../elements/Alert";
import {cloudinaryAvatar} from "../../project";
import axios from 'axios';

document.querySelectorAll('form.comment-js').forEach(function (link) {
    link.addEventListener('submit', onSubmitComment);
});

function onSubmitComment(event) {
    event.preventDefault();

    const form = this;
    const url = form.action;
    const textarea = document.getElementById('comment_text');
    const token = document.getElementById('csrf_comment');
    const nbrComment = document.querySelector('.nbr-comment-js');

    axios.post(url, {
        content: textarea.value,
        csrf: token.value
    }).then(function (response) {
        nbrComment.textContent = response.data.nbrComment;
        textarea.value = '';
        appendComment(response.data.comment)
        alert('ok')
        const successAlert = new FloatingAlert('success', 'Well done, your comment has been sent', 'dark')
        document.querySelector('.container').insertBefore(successAlert, document.querySelector('.post-banner'))
    }).catch(function (error) {
        const errorAlert = new FloatingAlert('danger', error.response.data.message, 'dark')
        document.querySelector('.container').insertBefore(errorAlert, document.querySelector('.post-banner'))
    })
}

/**
 * @param {Array} data
 */
export function onSubmitReply(data) {
    axios.post(data[0], {
        content: data[1]
    }).then(function (response) {
        data[3].textContent = response.data.nbrComment;
        data[1] = ''
        appendNewReply(response.data.reply)
        const successAlert = new FloatingAlert('success', 'Well done, your comment has been sent')
        document.querySelector('.container').insertBefore(successAlert, document.querySelector('.post-banner'))
    }).catch(function (error) {
        const errorAlert = new FloatingAlert('danger', error.response.data.message, 'dark')
        document.querySelector('.container').insertBefore(errorAlert, document.querySelector('.post-banner'))
    })
}

/**
 * @param {Array} commentData
 */
function appendComment(commentData) {
    const user = commentData.author.user;
    const avatar = cloudinaryAvatar('mael', 'symfony-project-blog', 'user/avatar', commentData.author.avatar);
    const userProfile = '/profil/' + commentData.author.id;
    const date = commentData.date;
    const content = commentData.content;
    const commentId = commentData.id;
    const newComment = new Comment(user, avatar, userProfile, 'Répondre', date, false, content, commentId);
    const parent = document.querySelector('.comment-block');
    const beforeTag = parent.firstChild;
    parent.insertBefore(newComment, beforeTag);
}

/**
 * @param {Array} replyData
 */
function appendNewReply(replyData) {
    const user = replyData.author.user
    const avatar = cloudinaryAvatar('mael', 'symfony-project-blog', 'user/avatar', replyData.author.avatar)
    const userProfile = '/profil/' + replyData.author.id
    const date = replyData.date
    const content = replyData.content
    const parentId = replyData.parent
    const newReply = new Comment(user, avatar, userProfile, 'Répondre', date, true, content, '', parentId)
    const parent = document.getElementById('reply-' + parentId)
    parent.appendChild(newReply)
}