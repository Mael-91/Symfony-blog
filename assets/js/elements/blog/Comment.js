class Comment extends HTMLElement {

    /**
     * @param {String} author
     * @param {String} authorAvatar
     * @param {String} authorLink
     * @param {String} btnReplyName
     * @param {Date} date
     * @param {String} dateClass
     * @param {Boolean} isReply
     * @param {String} commentContent
     * @param {Number} commentId
     * @param {Number} parentId
     */
    constructor(author, authorAvatar, authorLink, btnReplyName, date, isReply, commentContent, commentId, parentId) {
        super();
        this.author = author;
        this.avatar = authorAvatar;
        this.authorLink = authorLink;
        this.btn = btnReplyName;
        this.date = date;
        this.isReply = isReply;
        this.commentContent = commentContent;
        this.commentId = '';
        this.parentId = ''; // L'id du commentaire pour l'apparition de la form
        if (author === undefined) {
            this.author = this.getAttribute('author');
        } else {
            this.author = author;
        }
        if (authorAvatar === undefined) {
            this.avatar = this.getAttribute('author-avatar');
        } else {
            this.avatar = authorAvatar;
        }
        if (authorLink === undefined) {
            this.authorLink = this.getAttribute('author-link');
        } else {
            this.authorLink = authorLink;
        }
        if (btnReplyName === undefined) {
            if (this.getAttribute('btn-reply-name')) {
                this.btn = this.getAttribute('btn-reply-name');
            } else {
                this.btn = 'Répondre'
            }
        } else {
            this.btn = btnReplyName;
        }
        if (date === undefined) {
            this.date = this.getAttribute('datetime')
        } else {
            this.date = date;
        }
        if (isReply === false) {
            this.isReply = 'comment';
        } else {
            this.isReply = 'comment comment-reply';
        }
        if (this.getAttribute('isReply') === 'true') {
            this.isReply = 'comment comment-reply';
        } else {
            this.isReply = 'comment';
        }
        if (commentContent === undefined) {
            if (this.getAttribute('comment-content')) {
                this.commentContent = this.getAttribute('content')
            } else {
                this.commentContent = this.innerHTML;
            }
        }
        if (commentId !== undefined || this.getAttribute('comment-id')) {
            if (commentId) {
                this.commentId = 'id="' + commentId + '"';
            } else {
                const cId = this.getAttribute('comment-id');
                this.commentId = 'id="' + cId + '"';
            }
        } else {
            if (parentId) {
                this.parentId = 'id="' + parentId + '"';
            } else {
                const pId = this.getAttribute('parent-id');
                this.parentId = 'id="' + pId + '"';
            }
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
        `
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