<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Repository\CategoriesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *      itemOperations={
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
 *          "post"={
 *              "security"="is_granted('ROLE_ADMIN')"
 *          }
 *     },
 *     shortName="category",
 *     attributes={
 *          "pagination_items_per_page"=1000,
 *          "formats"={"jsonld", "json", "html", "jsonhal", "csv"={"text/csv"}}
 *     }
 * )
 * @ORM\Entity(repositoryClass=CategoriesRepository::class)
 * @ApiFilter(SearchFilter::class, properties={"parent":"exact"})
 * @ApiFilter(PropertyFilter::class)
 * @ApiFilter(OrderFilter::class, properties={"name", "parent"})
 * @UniqueEntity(fields={"name"}, message="This category already exists")
 */
class Categories
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"category:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"category:read", "product:read","cart:read", "category:write"})
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity=Products::class, mappedBy="categories")
     * @Groups({"category:read", "category:write"})
     */
    private $products;

    /**
     * @ORM\ManyToOne(targetEntity=Categories::class, inversedBy="children", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     * @Groups({"category:read", "category:write"})
     * @ApiProperty(readableLink=false)
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity=Categories::class, mappedBy="parent")
     * @Groups({"category:read","cart:read", "category:write"})
     */
    private $children;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->children = new ArrayCollection();
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

    /**
     * @return Collection|Products[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Products $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->addCategory($this);
        }

        return $this;
    }

    public function removeProduct(Products $product): self
    {
        if ($this->products->removeElement($product)) {
            $product->removeCategory($this);
        }

        return $this;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function setParent(Categories $parent): void
    {
        $parent->addChild($this);

        $this->parent = $parent;
    }

    /**
     * @return Collection|self[]
     */
    public function getChild(): Collection
    {
        return $this->children;
    }

    public function addChild(Categories $child)
    {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
        }
    }

    public function removeChild(self $child): self
    {
        if ($this->children->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
