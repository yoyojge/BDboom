<?php

namespace App\Entity;

use App\Repository\WishlistRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WishlistRepository::class)]
class Wishlist
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'wishlists')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $collector = null;

    #[ORM\Column(length: 255)]
    private ?string $wishlistName = null;

    #[ORM\ManyToMany(targetEntity: Album::class, inversedBy: 'wishlists')]
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

    public function getWishlistName(): ?string
    {
        return $this->wishlistName;
    }

    public function setWishlistName(string $wishlistName): self
    {
        $this->wishlistName = $wishlistName;

        return $this;
    }

    /**
     * @return Collection<int, AlbumWishlist>
     */
    // public function getAlbumWishlists(): Collection
    // {
    //     return $this->albumWishlists;
    // }

    // public function addAlbumWishlist(AlbumWishlist $albumWishlist): self
    // {
    //     if (!$this->albumWishlists->contains($albumWishlist)) {
    //         $this->albumWishlists->add($albumWishlist);
    //         $albumWishlist->setWishlist($this);
    //     }

    //     return $this;
    // }

    // public function removeAlbumWishlist(AlbumWishlist $albumWishlist): self
    // {
    //     if ($this->albumWishlists->removeElement($albumWishlist)) {
    //         // set the owning side to null (unless already changed)
    //         if ($albumWishlist->getWishlist() === $this) {
    //             $albumWishlist->setWishlist(null);
    //         }
    //     }

    //     return $this;
    // }

    /**
     * @return Collection<int, Album>
     */
    public function getAlbum(): Collection
    {
        return $this->albums;
    }

    public function addAlbum(Album $album): self
    {
        if (!$this->albums->contains($album)) {
            $this->albums->add($album);
        }

        return $this;
    }

    public function removeAlbum(Album $album): self
    {
        $this->albums->removeElement($album);

        return $this;
    }
}
