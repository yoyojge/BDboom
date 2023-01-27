<?php

namespace App\Entity;

use App\Repository\AlbumCollectionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AlbumCollectionRepository::class)]
class AlbumCollection
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    // #[ORM\ManyToOne]
    // #[ORM\JoinColumn(nullable: false)]
    // private ?Album $album = null;

    #[ORM\OneToMany(mappedBy: 'albumCollections', targetEntity: Album::class)]
    private Collection $album;


    #[ORM\ManyToOne(inversedBy: 'albumCollections')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Collectionn $collection = null;

    #[ORM\ManyToMany(targetEntity: Collectionn::class, mappedBy: 'AlbumCollections')]
    private Collection $collectionns;


    //ajout johann 2023-01-23
    public function __construct()
    {
        $this->Album = new ArrayCollection();
        $this->collectionns = new ArrayCollection();

    }




    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAlbum(): ?Album
    {
        return $this->album;
    }

    public function setAlbum(?Album $album): self
    {
        $this->album = $album;

        return $this;
    }

    public function getCollection(): ?Collectionn
    {
        return $this->collection;
    }

    public function setCollection(?Collectionn $collection): self
    {
        $this->collection = $collection;

        return $this;
    }

    /**
     * @return Collection<int, Collectionn>
     */
    public function getCollectionns(): Collection
    {
        return $this->collectionns;
    }

    public function addCollectionn(Collectionn $collectionn): self
    {
        if (!$this->collectionns->contains($collectionn)) {
            $this->collectionns->add($collectionn);
            $collectionn->addAlbumCollection($this);
        }

        return $this;
    }

    public function removeCollectionn(Collectionn $collectionn): self
    {
        if ($this->collectionns->removeElement($collectionn)) {
            $collectionn->removeAlbumCollection($this);
        }

        return $this;
    }
}
