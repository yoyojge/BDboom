<?php

namespace App\Entity;

use App\Repository\CollectionnRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CollectionnRepository::class)]
class Collectionn
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'collectionns')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $collector = null;

    #[ORM\Column(length: 255)]
    private ?string $CollectionName = null;

    #[ORM\ManyToMany(targetEntity: Album::class, inversedBy: 'collectionns')]
    private Collection $albums;

    public function __construct()
    {
        $this->albums = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCollector(): ?User
    {
        return $this->collector;
    }

    public function setCollector(?User $collector): self
    {
        $this->collector = $collector;

        return $this;
    }

    public function getCollectionName(): ?string
    {
        return $this->CollectionName;
    }

    public function setCollectionName(string $CollectionName): self
    {
        $this->CollectionName = $CollectionName;

        return $this;
    }

    /**
     * @return Collection<int, Album>
     */
    public function getAlbums(): Collection
    {
        return $this->albums;
    }

    public function addAlbum(Album $album): self
    {
        if (!$this->albums->contains($album)) {
            $this->albums->add($album);
            $album->addCollectionn($this);
        }

        return $this;
    }

    public function removeAlbum(Album $album): self
    {
        if ($this->albums->removeElement($album)) {
            $album->removeCollectionn($this);
        }

        return $this;
    }
}
