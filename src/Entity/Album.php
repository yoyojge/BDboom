<?php

namespace App\Entity;

use App\Repository\AlbumRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AlbumRepository::class)]
class Album
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $cover = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $refBDfugues = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $refAmazon = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $isbn = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $serie = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $origine = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $keyword = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $author = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $BDboomDate = null;

    #[ORM\ManyToMany(targetEntity: Collectionn::class, mappedBy: 'albums')]
    private Collection $collectionns;

    #[ORM\ManyToMany(targetEntity: Wishlist::class, mappedBy: 'album')]
    private Collection $wishlists;

    public function __construct()
    {
        $this->collectionns = new ArrayCollection();
        $this->wishlists = new ArrayCollection();
    }


    //add johann 2023-01-25
    // public function __toString() {
    //     return $this->title;
    // }


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

    public function getCover(): ?string
    {
        return $this->cover;
    }

    public function setCover(string $cover): self
    {
        $this->cover = $cover;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getRefBDfugues(): ?string
    {
        return $this->refBDfugues;
    }

    public function setRefBDfugues(?string $refBDfugues): self
    {
        $this->refBDfugues = $refBDfugues;

        return $this;
    }

    public function getRefAmazon(): ?string
    {
        return $this->refAmazon;
    }

    public function setRefAmazon(?string $refAmazon): self
    {
        $this->refAmazon = $refAmazon;

        return $this;
    }

    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function setIsbn(?string $isbn): self
    {
        $this->isbn = $isbn;

        return $this;
    }

    public function getSerie(): ?string
    {
        return $this->serie;
    }

    public function setSerie(?string $serie): self
    {
        $this->serie = $serie;

        return $this;
    }

    public function getOrigine(): ?string
    {
        return $this->origine;
    }

    public function setOrigine(?string $origine): self
    {
        $this->origine = $origine;

        return $this;
    }

    public function getKeyword(): ?string
    {
        return $this->keyword;
    }

    public function setKeyword(?string $keyword): self
    {
        $this->keyword = $keyword;

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(?string $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getBDboomDate(): ?\DateTimeInterface
    {
        return $this->BDboomDate;
    }

    public function setBDboomDate(?\DateTimeInterface $BDboomDate): self
    {
        $this->BDboomDate = $BDboomDate;

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
            $collectionn->addAlbum($this);
        }

        return $this;
    }

    public function removeCollectionn(Collectionn $collectionn): self
    {
        if ($this->collectionns->removeElement($collectionn)) {
            $collectionn->removeAlbum($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Wishlist>
     */
    public function getWishlists(): Collection
    {
        return $this->wishlists;
    }

    public function addWishlist(Wishlist $wishlist): self
    {
        if (!$this->wishlists->contains($wishlist)) {
            $this->wishlists->add($wishlist);
            $wishlist->addAlbum($this);
        }

        return $this;
    }

    public function removeWishlist(Wishlist $wishlist): self
    {
        if ($this->wishlists->removeElement($wishlist)) {
            $wishlist->removeAlbum($this);
        }

        return $this;
    }
}
