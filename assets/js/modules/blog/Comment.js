const nl2br = require('../../functions/nl2br')
const moment = require('moment')

function appendNewComment(comment) {

    let id = comment.id
    let author = comment.author
    let datetime = comment.date
    let content = comment.content

    let parent = document.getElementById('block-comment')
    let firstChild = parent.firstChild
    let commentDiv = document.createElement('div')
    commentDiv.classList.add('comment')
    commentDiv.setAttribute('style', 'margin-top: 20px;')
    commentDiv.setAttribute('id', id)
    parent.insertBefore(commentDiv, firstChild)

    let card = document.createElement('div')
    card.classList.add('comment-card', 'card')
    commentDiv.appendChild(card)

    let cardBody = document.createElement('div')
    cardBody.classList.add('card-body')
    card.appendChild(cardBody)

    let cardTitle = document.createElement('span')
    cardTitle.classList.add('card-title')
    cardBody.appendChild(cardTitle)

    let aAuthor = document.createElement('a')
    aAuthor.classList.add('text-decoration-none', 'text-muted')
    aAuthor.setAttribute('style', 'font-size: 1rem; font-weight: 700')
    aAuthor.textContent = author
    cardTitle.appendChild(aAuthor)

    let abbrDate = document.createElement('abbr')
    abbrDate.classList.add('datetime')
    cardTitle.appendChild(abbrDate)

    let spanAgo = document.createElement('span')
    spanAgo.classList.add('timeago', 'text-black-50')
    spanAgo.setAttribute('dateTime', datetime)
    spanAgo.textContent = ' - ' + moment().locale('fr').fromNow() + ' - '
    abbrDate.appendChild(spanAgo)

    let buttonReply = document.createElement('button')
    buttonReply.classList.add('show-reply-zone', 'text-muted')
    buttonReply.setAttribute('style', 'font-size: .7rem; text-decoration:  underline;')
    buttonReply.setAttribute('onclick', 'showReplyZone()')
    buttonReply.textContent = 'Répondre'
    cardTitle.appendChild(buttonReply)

    let pContent = document.createElement('p')
    pContent.classList.add('card-text', 'mt-2')
    pContent.textContent = nl2br(content, false)
    cardBody.appendChild(pContent)
}

function appendNewReply(reply) {
    let parentID = reply.parent
    let author = reply.author
    let datetime = reply.date
    let content = reply.content
    let parent = document.getElementById(parentID)

    let replyDiv = document.createElement('div')
    replyDiv.classList.add('reply')
    parent.appendChild(replyDiv)

    let replyCard = document.createElement('div')
    replyCard.classList.add('reply-card', 'card', 'card-color')
    replyDiv.appendChild(replyCard)

    let replyBody = document.createElement('div')
    replyBody.classList.add('card-body')
    replyCard.appendChild(replyBody)

    let spanTitle = document.createElement('span')
    spanTitle.classList.add('card-title')
    replyBody.appendChild(spanTitle)

    let aAuthor = document.createElement('a')
    aAuthor.setAttribute('href', '')
    aAuthor.classList.add('text-decoration-none', 'text-muted')
    aAuthor.setAttribute('style', 'font-size: 1rem; font-weight: 700')
    aAuthor.textContent = author
    spanTitle.appendChild(aAuthor)

    let abbr = document.createElement('abbr')
    abbr.classList.add('datetime')
    spanTitle.appendChild(abbr)

    let spanAgo = document.createElement('span')
    spanAgo.classList.add('timeago', 'text-black-50')
    spanAgo.setAttribute('dateTime', datetime)
    spanAgo.textContent = ' - ' + moment().locale('fr').fromNow() + ' - '
    abbr.appendChild(spanAgo)

    let button = document.createElement('button')
    button.classList.add('show-reply-zone', 'text-muted')
    button.setAttribute('style', 'font-size: .7rem; text-decoration:  underline;')
    button.textContent = 'Répondre'
    spanTitle.appendChild(button)

    let pContent = document.createElement('p')
    pContent.classList.add('card-text', 'mt-2')
    pContent.textContent = nl2br(content, false)
    replyBody.appendChild(pContent)
}

export {appendNewComment, appendNewReply}