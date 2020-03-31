<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserActivityRepository")
 */
class UserActivity
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="userActivities")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Blog", inversedBy="userActivities")
     */
    private $blog;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\BlogComment", inversedBy="userActivities")
     */
    private $blog_comment;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\BlogLike", inversedBy="userActivities")
     */
    private $blog_like;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    public function __construct() {
        $this->created_at = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getBlog(): ?Blog
    {
        return $this->blog;
    }

    public function setBlog(?Blog $blog): self
    {
        $this->blog = $blog;

        return $this;
    }

    public function getBlogComment(): ?BlogComment
    {
        return $this->blog_comment;
    }

    public function setBlogComment(?BlogComment $blog_comment): self
    {
        $this->blog_comment = $blog_comment;

        return $this;
    }

    public function getBlogLike(): ?BlogLike
    {
        return $this->blog_like;
    }

    public function setBlogLike(?BlogLike $blog_like): self
    {
        $this->blog_like = $blog_like;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }
}
