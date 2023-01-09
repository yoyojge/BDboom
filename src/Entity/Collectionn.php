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

    #[ORM\OneToMany(mappedBy: 'collection', targetEntity: AlbumCollection::class, orphanRemoval: true)]
    private Collection $albumCollections;

    public function __construct()
    {
        $this->albumCollections = new ArrayCollection();
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
     * @return Collection<int, AlbumCollection>
     */
    public function getAlbumCollections(): Collection
    {
        return $this->albumCollections;
    }

    public function addAlbumCollection(AlbumCollection $albumCollection): self
    {
        if (!$this->albumCollections->contains($albumCollection)) {
            $this->albumCollections->add($albumCollection);
            $albumCollection->setCollection($this);
        }

        return $this;
    }

    public function removeAlbumCollection(AlbumCollection $albumCollection): self
    {
        if ($this->albumCollections->removeElement($albumCollection)) {
            // set the owning side to null (unless already changed)
            if ($albumCollection->getCollection() === $this) {
                $albumCollection->setCollection(null);
            }
        }

        return $this;
    }
}
