import FormReply from "../elements/blog/FormReply";

const btnReplies = document.querySelectorAll('.comment-action-reply');
btnReplies.forEach((btnReply) => {
    btnReply.addEventListener('click', function (e) {
        e.preventDefault();
        const parent = this.parentNode.parentNode.parentNode;
        const getParentId = this.parentNode.parentNode;
        const commentId = getParentId.getAttribute('id');
        const action = window.location.href + '/reply/' + commentId
        const form = new FormReply(commentId, action, '{{ csrf_token(\'reply_comment\' ~ post.id) }}', 'btn btn-outline-success btn-reply')
        parent.appendChild(form)
    })
});