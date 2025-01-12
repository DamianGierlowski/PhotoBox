<?php

namespace App\Entity;

use App\Repository\GalleryRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GalleryRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Gallery
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'galleries')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Commission $commission = null;


    #[ORM\Column(type: Types::GUID)]
    private ?string $guid = null;

    /**
     * @var Collection<int, File>
     */
    #[ORM\ManyToMany(targetEntity: File::class)]
    private Collection $files;

    #[ORM\Column]
    private ?bool $deleted = false;

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $deletedAt = null;

    #[ORM\Column]
    private ?bool $public = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $createdBy = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->files = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCommission(): ?Commission
    {
        return $this->commission;
    }

    public function setCommission(?Commission $commission): static
    {
        $this->commission = $commission;

        return $this;
    }

    public function getGuid(): ?string
    {
        return $this->guid;
    }

    public function setGuid(string $guid): static
    {
        $this->guid = $guid;

        return $this;
    }

    /**
     * @return Collection<int, File>
     */
    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function addFile(File $file): static
    {
        if (!$this->files->contains($file)) {
            $this->files->add($file);
        }

        return $this;
    }

    public function removeFile(File $file): static
    {
        $this->files->removeElement($file);

        return $this;
    }

    public function isDeleted(): ?bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): static
    {
        $this->deleted = $deleted;

        return $this;
    }

    public function getDeletedAt(): ?DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?DateTimeImmutable $deletedAt): static
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        if ($this->deleted && $this->deletedAt === null) {
            $this->deletedAt = new DateTimeImmutable();
        }
    }

    public function isPublic(): ?bool
    {
        return $this->public;
    }

    public function setPublic(bool $public): static
    {
        $this->public = $public;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): static
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    #[ORM\PrePersist]
    public function setCreatedAt(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function setUpdatedAt(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }
}
