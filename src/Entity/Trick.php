<?php

namespace App\Entity;

use App\Repository\TrickRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=TrickRepository::class)
 * @UniqueEntity(
 *     fields={"title"},
 *     errorPath="title",
 *     message="This title is already in use."
 * )
 */
class Trick
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedTo;

    /**
     * @ORM\Column(type="string", length=70, unique=true)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $featuredPicture;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="trick", orphanRemoval=true)
     * @ORM\OrderBy({"createdAt" = "DESC"})
     */
    private $comments;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="tricks")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=TypeTrick::class, inversedBy="tricks")
     */
    private $type;

    /**
     * @ORM\OneToMany(targetEntity=MediaPicture::class, mappedBy="trick", orphanRemoval=true)
     */
    private $mediasPicture;

    /**
     * @ORM\OneToMany(targetEntity=MediaVideo::class, mappedBy="trick", orphanRemoval=true)
     */
    private $mediasVideos;


    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->mediasPicture = new ArrayCollection();
        $this->mediasVideos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedTo(): ?\DateTimeInterface
    {
        return $this->updatedTo;
    }

    public function setUpdatedTo(?\DateTimeInterface $updatedTo): self
    {
        $this->updatedTo = $updatedTo;

        return $this;
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

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

    public function getFeaturedPicture(): ?string
    {
        return $this->featuredPicture;
    }

    public function setFeaturedPicture(?string $featuredPicture): self
    {
        $this->featuredPicture = $featuredPicture;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setTrick($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getTrick() === $this) {
                $comment->setTrick(null);
            }
        }

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

    public function getType(): ?TypeTrick
    {
        return $this->type;
    }

    public function setType(?TypeTrick $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection<int, MediaPicture>
     */
    public function getMediasPicture(): Collection
    {
        return $this->mediasPicture;
    }

    public function addMediasPicture(MediaPicture $mediaPicture): self
    {
        if (!$this->mediasPicture->contains($mediaPicture)) {
            $this->mediasPicture[] = $mediaPicture;
            $mediaPicture->setTrick($this);
        }

        return $this;
    }

    public function removeMediasPicture(MediaPicture $mediaPicture): self
    {
        if ($this->mediasPicture->removeElement($mediaPicture)) {
            // set the owning side to null (unless already changed)
            if ($mediaPicture->getTrick() === $this) {
                $mediaPicture->setTrick(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, MediaVideo>
     */
    public function getMediasVideos(): Collection
    {
        return $this->mediasVideos;
    }

    public function addMediasVideo(MediaVideo $mediaVideo): self
    {
        if (!$this->mediasVideos->contains($mediaVideo)) {
            $this->mediasVideos[] = $mediaVideo;
            $mediaVideo->setTrick($this);
        }

        return $this;
    }

    public function removeMediasVideo(MediaVideo $mediaVideo): self
    {
        if ($this->mediasVideos->removeElement($mediaVideo)) {
            // set the owning side to null (unless already changed)
            if ($mediaVideo->getTrick() === $this) {
                $mediaVideo->setTrick(null);
            }
        }

        return $this;
    }
}
