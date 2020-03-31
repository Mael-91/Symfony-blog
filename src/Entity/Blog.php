<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Cocur\Slugify\Slugify;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BlogRepository")
 * @Vich\Uploadable()
 */
class Blog
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     */
    private $content;

    /**
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255)
     */
    private $pictureFilename;

    /**
     * @var File|null
     * @Vich\UploadableField(mapping="blog_image", fileNameProperty="pictureFilename")
     * @Assert\Image(mimeTypes="image/jpeg", mimeTypesMessage="Le fichier doit être au format JPEG.")
     */
    private $pictureFile;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255)
     */
    private $bannerFilename;

    /**
     * @var File|null
     * @Vich\UploadableField(mapping="blog_image", fileNameProperty="bannerFilename")
     * @Assert\Image(mimeTypes="image/jpeg", mimeTypesMessage="Le fichier doit être au format JPEG.")
     */
    private $bannerFile;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", columnDefinition="DATETIME on update CURRENT_TIMESTAMP", nullable=true)
     */
    private $edited_at;

    /**
     * @ORM\Column(type="boolean", options={"default": true})
     */
    private $visible;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\BlogCategory", inversedBy="posts")
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\BlogComment", mappedBy="post", cascade={"remove"})
     */
    private $comments;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="author")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\BlogLike", mappedBy="post")
     */
    private $likes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserActivity", mappedBy="blog", orphanRemoval=true)
     */
    private $userActivities;

    public function __construct() {
        $this->created_at = new \DateTime();
        $this->comments = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->userActivities = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPictureFilename(): ?string
    {
        return $this->pictureFilename;
    }

    /**
     * @param string|null $pictureFilename
     * @return Blog
     */
    public function setPictureFilename(?string $pictureFilename): Blog
    {
        $this->pictureFilename = $pictureFilename;
        return $this;
    }

    /**
     * @return File|null
     */
    public function getPictureFile(): ?File
    {
        return $this->pictureFile;
    }

    /**
     * @param File|null $pictureFile
     * @return Blog
     * @throws \Exception
     */
    public function setPictureFile(?File $pictureFile): Blog
    {
        $this->pictureFile = $pictureFile;
        if ($this->pictureFile instanceof UploadedFile) {
            $this->edited_at = new \DateTime();
        }
        return $this;
    }

    /**
     * @return string|null
     */
    public function getBannerFilename(): ?string
    {
        return $this->bannerFilename;
    }

    /**
     * @param string|null $bannerFilename
     * @return Blog
     */
    public function setBannerFilename(?string $bannerFilename): Blog
    {
        $this->bannerFilename = $bannerFilename;
        return $this;
    }

    /**
     * @return File|null
     */
    public function getBannerFile(): ?File
    {
        return $this->bannerFile;
    }

    /**
     * @param File|null $bannerFile
     * @return Blog
     * @throws \Exception
     */
    public function setBannerFile(?File $bannerFile): Blog
    {
        $this->bannerFile = $bannerFile;
        if ($this->bannerFile instanceof UploadedFile) {
            $this->edited_at = new \DateTime();
        }
        return $this;
    }

    public function getSlug(): string {
        return (new Slugify())->slugify($this->title);
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

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

    public function getCategory(): ?BlogCategory
    {
        return $this->category;
    }

    public function setCategory(?BlogCategory $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection|BlogLike[]
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(BlogLike $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes[] = $like;
            $like->setPost($this);
        }

        return $this;
    }

    public function removeLike(BlogLike $like): self
    {
        if ($this->likes->contains($like)) {
            $this->likes->removeElement($like);
            // set the owning side to null (unless already changed)
            if ($like->getPost() === $this) {
                $like->setPost(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|BlogComment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(BlogComment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setPost($this);
        }

        return $this;
    }

    public function removeComment(BlogComment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getPost() === $this) {
                $comment->setPost(null);
            }
        }

        return $this;
    }

    /**
     * Permet de savoir si un article est liké par un utilisateur
     *
     * @param User $user
     * @return bool
     */
    public function isLikedByUser(User $user): bool {
        foreach ($this->likes as $like) {
            if ($like->getUser() === $user) return true;
        }

        return false;
    }

    public function __toString() {
        return $this->title;
    }

    /**
     * @return Collection|UserActivity[]
     */
    public function getUserActivities(): Collection
    {
        return $this->userActivities;
    }

    public function addUserActivity(UserActivity $userActivity): self
    {
        if (!$this->userActivities->contains($userActivity)) {
            $this->userActivities[] = $userActivity;
            $userActivity->setBlog($this);
        }

        return $this;
    }

    public function removeUserActivity(UserActivity $userActivity): self
    {
        if ($this->userActivities->contains($userActivity)) {
            $this->userActivities->removeElement($userActivity);
            // set the owning side to null (unless already changed)
            if ($userActivity->getBlog() === $this) {
                $userActivity->setBlog(null);
            }
        }

        return $this;
    }
}
