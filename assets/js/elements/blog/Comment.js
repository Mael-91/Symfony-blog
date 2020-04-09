import moment from "moment"

export default class Comment extends HTMLElement {

    /**
     * @param {String} author
     * @param {String} authorAvatar
     * @param {String} authorLink
     * @param {String} btnReplyName
     * @param {Date} date
     * @param {Boolean} isReply
     * @param {String} commentContent
     * @param {Number} commentId
     * @param {Number} parentId
     */
    constructor(author, authorAvatar, authorLink, btnReplyName, date, isReply, commentContent, commentId, parentId) {
        super();
        if (author !== undefined) {
            this.author = author
        } else {
            this.author = this.getAttribute('author')
        }
        if (authorAvatar !== undefined) {
            this.avatar = authorAvatar
        } else {
            this.avatar = this.getAttribute('author-avatar')
        }
        if (authorLink !== undefined) {
            this.authorLink = authorLink
        } else {
            this.authorLink = this.getAttribute('author-link')
        }
        if (btnReplyName !== undefined) {
            this.btn = btnReplyName
        } else {
            this.btn = this.getAttribute('btn-reply-name')
        }
        if (date !== undefined) {
            this.date = `<span class="comment-date moment" datetime="${new Date(date)}">${moment(new Date(date)).locale('fr').fromNow()}</span>`
        } else {
            this.date = this.getAttribute('datetime')
        }

        if (isReply !== undefined && isReply === true) {
            this.isReply = 'comment comment-reply'
        } else if (this.getAttribute('isReply')) {
            this.isReply = 'comment comment-reply'
        } else {
            this.isReply = 'comment'
        }
        if (commentContent !== undefined) {
            this.commentContent = commentContent
        } else {
            this.commentContent = this.getAttribute('content')
        }
        if (commentId !== undefined) {
            this.commentId = 'id="' + commentId + '"'
        } else if (this.getAttribute('comment-id')) {
            this.commentId = 'id="' + this.getAttribute('comment-id') + '"'
        } else {
            this.commentId = ''
        }
        if (parentId !== undefined) {
            this.parentId = 'id="' + parentId + '"'
        } else if (this.getAttribute('parent-id')) {
            this.parentId = 'id="' + this.getAttribute('parent-id') + '"'
        } else {
            this.parentId = ''
        }
        this.deleteAttribute()
    }

    connectedCallback() {
        this.innerHTML = `
        <div class="${this.isReply}"${this.commentId}${this.parentId}>
            <img src="${this.avatar}" class="comment-avatar">
            <div class="comment-body">
                <a href="${this.authorLink}" class="comment-author">${this.author}</a>
                ${this.date}
                <span class="comment-action-reply">${this.btn}</span>
                <div class="comment-content">
                    <p class="comment-text">${this.commentContent}</p>
                </div>
            </div>
        </div>
        `;
    }

    deleteAttribute() {
        this.removeAttribute('author');
        this.removeAttribute('author-avatar');
        this.removeAttribute('author-link');
        this.removeAttribute('btn-reply-name');
        this.removeAttribute('datetime');
        this.removeAttribute('date-class');
        this.removeAttribute('isReply');
        this.removeAttribute('content');
        this.removeAttribute('comment-id');
        this.removeAttribute('parent-id');
    }
}

customElements.define('comment-block', Comment);