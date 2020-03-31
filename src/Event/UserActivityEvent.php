<?php

namespace App\Event;

use App\Entity\Blog;
use App\Entity\BlogComment;
use App\Entity\BlogLike;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class UserActivityEvent extends Event {

    /**
     * @var User
     */
    private $user;
    /**
     * @var Blog|null
     */
    private $blog;
    /**
     * @var BlogComment|null
     */
    private $blogComment;
    /**
     * @var BlogLike|null
     */
    private $blogLike;

    public function __construct(User $user, ?Blog $blog, ?BlogComment $blogComment, ?BlogLike $blogLike) {
        $this->user = $user;
        $this->blog = $blog;
        $this->blogComment = $blogComment;
        $this->blogLike = $blogLike;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return Blog|null
     */
    public function getBlog(): ?Blog
    {
        return $this->blog;
    }

    /**
     * @return BlogComment|null
     */
    public function getBlogComment(): ?BlogComment
    {
        return $this->blogComment;
    }

    /**
     * @return BlogLike|null
     */
    public function getBlogLike(): ?BlogLike
    {
        return $this->blogLike;
    }
}