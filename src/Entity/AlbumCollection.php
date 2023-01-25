<?php

namespace App\Entity;

use App\Repository\AlbumCollectionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AlbumCollectionRepository::class)]
class AlbumCollection
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Album $album = null;

    #[ORM\ManyToOne(inversedBy: 'albumCollections')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Collectionn $collection = null;


    //ajout johann 2023-01-23
    public function __construct()
    {
        $this->Album = new ArrayCollection();

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
}
