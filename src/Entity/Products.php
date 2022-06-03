<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use App\Repository\ProductsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter;
use App\Filters\ProductFilter;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     itemOperations={
 *          "get",
 *          "put" = {
 *              "security"="is_granted('ROLE_ADMIN')"
 *          },
 *          "patch"={
 *              "security"="is_granted('ROLE_ADMIN')"
 *          },
 *          "delete" = { "security" = "is_granted('ROLE_ADMIN')" }
 *     },
 *      collectionOperations={
 *          "get",
 *          "post" = {
 *              "security" = "is_granted('ROLE_ADMIN')"
 *      }
 *     },
 *     shortName="product",
 *     attributes={
 *          "pagination_client_enabled"=true,
 *          "pagination_client_items_per_page"=true,
 *          "pagination_items_per_page"=24,
 *          "formats"={"jsonld", "json", "html", "jsonhal", "csv"={"text/csv"}}
 *     }
 * )
 * @ORM\Entity(repositoryClass=ProductsRepository::class)
 * @ApiFilter(ProductFilter::class)
 * @ApiFilter(SearchFilter::class, properties={
 *     "name":"partial",
 *     "description":"partial",
 *     "type":"partial",
 *     "brands.name":"partial",
 *     "categories.name":"partial",
 *     "categories.id":"exact",
 *     "comboDeal":"exact",
 *     "featured":"exact",
 *     "offer":"exact"
 *     })
 * @ApiFilter(RangeFilter::class, properties={"price", "inventory"})
 * @ApiFilter(OrderFilter::class, properties={"name", "price", "type"}, arguments={"orderParameterName":"order"})
 * @UniqueEntity(fields={"name"}, message="This product already exists")
 */
class Products
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"product:read", "category:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"product:read","cartproduct:read", "category:read", "cart:read", "admin:write"})
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"product:read", "admin:write", "category:read"})
     * @Assert\NotBlank()
     */
    private $type;

    /**
     * @ORM\Column(type="float")
     * @Groups({"product:read","cart:read", "cartproduct:read", "admin:write", "category:read"})
     * @Assert\NotBlank()
     * @Assert\Type(type="float", message="The price should be a float")
     */
    private $price;

    /**
     * @ORM\Column(type="text")
     * @Groups({"product:read", "admin:write", "category:read"})
     * @Assert\NotBlank()
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=Brands::class, inversedBy="product")
     * @Groups({"product:read", "admin:write", "category:read"})
     * @ApiSubresource()
     */
    private $brands;

    /**
     * @ORM\OneToMany(targetEntity="CartProducts", mappedBy="product")
     * @Groups({"product:read"})
     * @ApiSubresource()
     */
    private $cartItems;

    /**
     * @ORM\ManyToMany(targetEntity=Categories::class, inversedBy="products")
     * @Groups({"product:read","cart:read", "admin:write"})
     * @ApiSubresource()
     */
    private $categories;

    /**
     * @ORM\ManyToMany(targetEntity=Media::class, inversedBy="productImages", cascade={"persist", "remove"})
     * @Groups({"product:read", "cartproduct:read", "admin:write", "category:read"})
     * @ApiSubresource()
     * @Assert\NotBlank()
     */
    private $images;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"product:read","cart:read", "category:read", "admin:write", "cartproduct:read"})
     */
    private $inventory;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups({"product:read", "admin:write", "category:read"})
     */
    private $abv;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"product:read", "admin:write", "category:read"})
     */
    private $featured = false;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"product:read", "admin:write", "category:read"})
     */
    private $offer = false;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"product:read", "admin:write", "category:read"})
     */
    private $ibu;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Groups({"product:read", "admin:write", "category:read"})
     */
    private $content;

    /**
     * @ORM\ManyToOne(targetEntity=Media::class, inversedBy="productLogo", cascade={"persist", "remove"})
     * @Groups({"product:read", "admin:write", "category:read"})
     * @ApiSubresource()
     */
    private $logo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"product:read", "admin:write", "category:read"})
     */
    private $ingredients;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"product:read", "admin:write", "category:read"})
     */
    private $country;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     * @Groups({"product:read", "admin:write", "category:read"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"product:read", "admin:write", "category:read"})
     */
    private $comboDeal = false;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->cartItems = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->images = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getBrands(): ?Brands
    {
        return $this->brands;
    }

    public function setBrands(?Brands $brands): self
    {
        $this->brands = $brands;

        return $this;
    }

//    /**
//     * @return Collection|CartItems[]
//     */
//    public function getCartItems(): Collection
//    {
//        return $this->cartItems;
//    }
//
//    public function addCartItem(CartItems $cartItem): self
//    {
//        if (!$this->cartItems->contains($cartItem)) {
//            $this->cartItems[] = $cartItem;
//            $cartItem->addCartProduct($this);
//        }
//
//        return $this;
//    }
//
//    public function removeCartItem(CartItems $cartItem): self
//    {
//        if ($this->cartItems->removeElement($cartItem)) {
//            $cartItem->removeCartProduct($this);
//        }
//
//        return $this;
//    }

    /**
     * @return Collection|Categories[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Categories $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
        }

        return $this;
    }

    public function removeCategory(Categories $category): self
    {
        $this->categories->removeElement($category);

        return $this;
    }

    /**
     * @return Collection|Media[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Media $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
        }

        return $this;
    }

    public function removeImage(Media $image): self
    {
        $this->images->removeElement($image);

        return $this;
    }

    public function getInventory(): ?int
    {
        return $this->inventory;
    }

    public function setInventory(?int $inventory): self
    {
        $this->inventory = $inventory;

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getAbv(): ?float
    {
        return $this->abv;
    }

    public function setAbv(?float $abv): self
    {
        $this->abv = $abv;

        return $this;
    }

    public function getFeatured(): ?bool
    {
        return $this->featured;
    }

    public function setFeatured(bool $featured): self
    {
        $this->featured = $featured;

        return $this;
    }

    public function getOffer(): ?bool
    {
        return $this->offer;
    }

    public function setOffer(bool $offer): self
    {
        $this->offer = $offer;

        return $this;
    }

    public function getIbu(): ?int
    {
        return $this->ibu;
    }

    public function setIbu(?int $ibu): self
    {
        $this->ibu = $ibu;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getLogo(): ?Media
    {
        return $this->logo;
    }

    public function setLogo(?Media $logo): self
    {
        $this->logo = $logo;

        return $this;
    }

    public function getIngredients(): ?string
    {
        return $this->ingredients;
    }

    public function setIngredients(?string $ingredients): self
    {
        $this->ingredients = $ingredients;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getComboDeal(): ?bool
    {
        return $this->comboDeal;
    }

    public function setComboDeal(bool $comboDeal): self
    {
        $this->comboDeal = $comboDeal;

        return $this;
    }
}
