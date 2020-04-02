<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @Vich\Uploadable()
 */
class User implements UserInterface, \Serializable
{

    public const Sexe = [
        '1' => 'Man',
        '2' => 'Woman'
    ];

    public const Role = [
        'Super Administrator' => 'ROLE_SUPER_ADMIN',
        'Administrator' => 'ROLE_ADMIN',
        'Moderator' => 'ROLE_MODERATEUR',
        'User' => 'ROLE_USER'
    ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="array")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\NotCompromisedPassword()
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\Email(message="The email '{{ value }}' is not valid email.")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $first_name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $last_name;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $birthday;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $sexe;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", columnDefinition="DATETIME on update CURRENT_TIMESTAMP", nullable=true)
     */
    private $edited_at;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $confirmation_token;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $requested_token_at;

    /**
     * @ORM\Column(type="boolean")
     */
    private $enabled;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $password_token;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $requested_pw_token_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $last_login;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Blog", mappedBy="author")
     */
    private $author;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\BlogLike", mappedBy="user")
     */
    private $blogLikes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\BlogComment", mappedBy="author")
     */
    private $blogComments;

    /**
     * @ORM\Column(type="boolean")
     */
    private $oauth;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\LoginAttempt", mappedBy="user")
     */
    private $loginAttempts;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserActivity", mappedBy="user", orphanRemoval=true)
     */
    private $userActivities;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $avatarFilename;

    /**
     * @var File|null
     * @Vich\UploadableField(mapping="user_avatar", fileNameProperty="avatarFilename")
     * @Assert\Image(mimeTypes="image/jpeg", mimeTypesMessage="The file must be in JPG format")
     */
    private $avatarFile;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $bannerFilename;

    /**
     * @var File|null
     * @Vich\UploadableField(mapping="user_banner", fileNameProperty="bannerFilename")
     * @Assert\Image(mimeTypes="image/jpeg", mimeTypesMessage="The file must be in JPG format")
     */
    private $bannerFile;

    /**
     * @ORM\OneToMany(targetEntity="PasswordToken", mappedBy="user")
     */
    private $requestPasswordToken;

    public function __construct() {
        $this->author = new ArrayCollection();
        $this->blogLikes = new ArrayCollection();
        $this->blogComments = new ArrayCollection();
        $this->loginAttempts = new ArrayCollection();
        $this->userActivities = new ArrayCollection();
        $this->requestPasswordToken = new ArrayCollection();
        $this->created_at = new \DateTime();
        $this->edited_at = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): self
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(string $last_name): self
    {
        $this->last_name = $last_name;

        return $this;
    }

    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    public function setBirthday(\DateTimeInterface $birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

    public function getSexe(): ?int
    {
        return $this->sexe;
    }

    public function setSexe(int $sexe): self
    {
        $this->sexe = $sexe;

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

    public function getConfirmationToken(): ?string
    {
        return $this->confirmation_token;
    }

    public function setConfirmationToken(?string $confirmation_token): self
    {
        $this->confirmation_token = $confirmation_token;

        return $this;
    }

    public function getRequestedTokenAt(): ?\DateTimeInterface
    {
        return $this->requested_token_at;
    }

    public function setRequestedTokenAt(?\DateTimeInterface $requested_token_at): self
    {
        $this->requested_token_at = $requested_token_at;

        return $this;
    }

    public function getEnabled(): ?int
    {
        return $this->enabled;
    }

    public function setEnabled(int $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getPasswordToken(): ?string
    {
        return $this->password_token;
    }

    public function setPasswordToken(?string $password_token): self
    {
        $this->password_token = $password_token;

        return $this;
    }

    public function getRequestedPwTokenAt(): ?\DateTimeInterface
    {
        return $this->requested_pw_token_at;
    }

    public function setRequestedPwTokenAt(?\DateTimeInterface $requested_pw_token_at): self
    {
        $this->requested_pw_token_at = $requested_pw_token_at;

        return $this;
    }

    public function getLastLogin(): ?\DateTimeInterface
    {
        return $this->last_login;
    }

    public function setLastLogin(?\DateTimeInterface $last_login): self
    {
        $this->last_login = $last_login;

        return $this;
    }

    public function getOauth(): ?bool
    {
        return $this->oauth;
    }

    public function setOauth(bool $oauth): self
    {
        $this->oauth = $oauth;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAvatarFilename(): ?string
    {
        return $this->avatarFilename;
    }

    /**
     * @param string|null $avatarFilename
     * @return User
     */
    public function setAvatarFilename(?string $avatarFilename): User
    {
        $this->avatarFilename = $avatarFilename;
        return $this;
    }

    /**
     * @return File|null
     */
    public function getAvatarFile(): ?File
    {
        return $this->avatarFile;
    }

    /**
     * @param File|null $avatarFile
     * @return User
     * @throws \Exception
     */
    public function setAvatarFile(?File $avatarFile): User
    {
        $this->avatarFile = $avatarFile;
        if ($this->avatarFile instanceof UploadedFile) {
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
     * @return User
     */
    public function setBannerFilename(?string $bannerFilename): User
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
     * @return User
     * @throws \Exception
     */
    public function setBannerFile(?File $bannerFile): User
    {
        $this->bannerFile = $bannerFile;
        if ($this->bannerFile instanceof UploadedFile) {
            $this->edited_at = new \DateTime();
        }
        return $this;
    }

    /**
     * @return Collection|Blog[]
     */
    public function getAuthor(): Collection
    {
        return $this->author;
    }

    public function addAuthor(Blog $author): self
    {
        if (!$this->author->contains($author)) {
            $this->author[] = $author;
            $author->setAuthor($this);
        }

        return $this;
    }

    public function removeAuthor(Blog $author): self
    {
        if ($this->author->contains($author)) {
            $this->author->removeElement($author);
            // set the owning side to null (unless already changed)
            if ($author->getAuthor() === $this) {
                $author->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|BlogLike[]
     */
    public function getBlogLikes(): Collection
    {
        return $this->blogLikes;
    }

    public function addBlogLike(BlogLike $blogLike): self
    {
        if (!$this->blogLikes->contains($blogLike)) {
            $this->blogLikes[] = $blogLike;
            $blogLike->setUser($this);
        }

        return $this;
    }

    public function removeBlogLike(BlogLike $blogLike): self
    {
        if ($this->blogLikes->contains($blogLike)) {
            $this->blogLikes->removeElement($blogLike);
            // set the owning side to null (unless already changed)
            if ($blogLike->getUser() === $this) {
                $blogLike->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|BlogComment[]
     */
    public function getBlogComments(): Collection
    {
        return $this->blogComments;
    }

    public function addBlogComment(BlogComment $blogComment): self
    {
        if (!$this->blogComments->contains($blogComment)) {
            $this->blogComments[] = $blogComment;
            $blogComment->setAuthor($this);
        }

        return $this;
    }

    public function removeBlogComment(BlogComment $blogComment): self
    {
        if ($this->blogComments->contains($blogComment)) {
            $this->blogComments->removeElement($blogComment);
            // set the owning side to null (unless already changed)
            if ($blogComment->getAuthor() === $this) {
                $blogComment->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|LoginAttempt[]
     */
    public function getLoginAttempts(): Collection
    {
        return $this->loginAttempts;
    }

    public function addLoginAttempt(LoginAttempt $loginAttempt): self
    {
        if (!$this->loginAttempts->contains($loginAttempt)) {
            $this->loginAttempts[] = $loginAttempt;
            $loginAttempt->setUser($this);
        }

        return $this;
    }

    public function removeLoginAttempt(LoginAttempt $loginAttempt): self
    {
        if ($this->loginAttempts->contains($loginAttempt)) {
            $this->loginAttempts->removeElement($loginAttempt);
            // set the owning side to null (unless already changed)
            if ($loginAttempt->getUser() === $this) {
                $loginAttempt->setUser(null);
            }
        }

        return $this;
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
            $userActivity->setUser($this);
        }

        return $this;
    }

    public function removeUserActivity(UserActivity $userActivity): self
    {
        if ($this->userActivities->contains($userActivity)) {
            $this->userActivities->removeElement($userActivity);
            // set the owning side to null (unless already changed)
            if ($userActivity->getUser() === $this) {
                $userActivity->setUser(null);
            }
        }

        return $this;
    }

    public function getRequestPasswordToken(): Collection
    {
        return $this->requestPasswordToken;
    }

    public function addRequestPasswordToken(PasswordToken $passwordToken): self {
        if (!$this->requestPasswordToken->contains($passwordToken)) {
            $this->requestPasswordToken[] = $passwordToken;
            $passwordToken->setUser($this);
        }

        return $this;
    }

    public function removeRequestPasswordToken(PasswordToken $requestPasswordToken): self
    {
        if ($this->requestPasswordToken->contains($requestPasswordToken)) {
            $this->requestPasswordToken->removeElement($requestPasswordToken);
            if ($requestPasswordToken->getUser() === $this) {
                $requestPasswordToken->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * String representation of object
     * @link https://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize()
    {
        return serialize([
            $this->id,
            $this->username,
            $this->password,
            $this->roles,
            $this->email,
            $this->birthday,
            $this->first_name,
            $this->last_name,
            $this->sexe,
            $this->created_at,
            $this->edited_at,
            $this->confirmation_token,
            $this->requested_token_at,
            $this->enabled,
            $this->password_token
        ]);
    }

    /**
     * Constructs the object
     * @link https://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     * @since 5.1.0
     */
    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->username,
            $this->password,
            $this->roles,
            $this->email,
            $this->birthday,
            $this->first_name,
            $this->last_name,
            $this->sexe,
            $this->created_at,
            $this->edited_at,
            $this->confirmation_token,
            $this->requested_token_at,
            $this->enabled,
            $this->password_token) = unserialize($serialized, ['allowed_classes' => false]);
    }

    public function __toString() {
        return (string)$this->username;
    }
}