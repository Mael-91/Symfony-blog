<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BlogLikeRepository")
 */
class BlogLike
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Blog", inversedBy="likes")
     */
    private $post;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="blogLikes")
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPost(): ?Blog
    {
        return $this->post;
    }

    public function setPost(?Blog $post): self
    {
        $this->post = $post;

        return $this;
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
}
