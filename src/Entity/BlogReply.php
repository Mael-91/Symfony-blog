<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BlogReplyRepository")
 */
class BlogReply
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Blog", inversedBy="replies")
     * @ORM\JoinColumn(nullable=false)
     */
    private $post;

    /**
     * @Gedmo\TreeParent()
     * @ORM\ManyToOne(targetEntity="App\Entity\BlogComment", inversedBy="replies")
     * @ORM\JoinColumn(nullable=false)
     */
    private $comment;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="blogReply")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\Column(type="text")
     */
    private $reply_content;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $edited_at;

    /**
     * @ORM\Column(type="boolean")
     */
    private $visible;

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

    public function getComment(): ?BlogComment
    {
        return $this->comment;
    }

    public function setComment(?BlogComment $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getReplyContent(): ?string
    {
        return $this->reply_content;
    }

    public function setReplyContent(string $reply_content): self
    {
        $this->reply_content = $reply_content;

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

    public function getEditedAt(): ?\DateTimeInterface
    {
        return $this->edited_at;
    }

    public function setEditedAt(?\DateTimeInterface $edited_at): self
    {
        $this->edited_at = $edited_at;

        return $this;
    }

    public function getVisible(): ?bool
    {
        return $this->visible;
    }

    public function setVisible(bool $visible): self
    {
        $this->visible = $visible;

        return $this;
    }
}
