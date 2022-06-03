<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\BrandsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;

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
 *          "post" = {
 *              "security" = "is_granted('ROLE_ADMIN')"
 *          }
 *     },
 *     shortName="brand",
 *     attributes={
 *          "pagination_items_per_page"=10,
 *          "formats"={"jsonld", "json", "html", "jsonhal", "csv"={"text/csv"}}
 *     }
 * )
 * @ORM\Entity(repositoryClass=BrandsRepository::class)
 * @ApiFilter(SearchFilter::class, properties={
 *     "name":"partial"
 * })
 * @ApiFilter(OrderFilter::class, properties={"name"}, arguments={"orderParameterName":"order"})
 * @UniqueEntity(fields={"name"}, message="This brand already exists")
 */
class Brands
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"brand:read", "product:read", "admin:write", "category:read"})
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Products::class, mappedBy="brands", cascade={"persist", "remove"})
     * @Groups({"brand:read", "admin:write"})
     */
    private $product;

    public function __construct()
    {
        $this->product = new ArrayCollection();
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
    public function getProduct(): Collection
    {
        return $this->product;
    }

    public function addProduct(Products $product): self
    {
        if (!$this->product->contains($product)) {
            $this->product[] = $product;
            $product->setBrands($this);
        }

        return $this;
    }

    public function removeProduct(Products $product): self
    {
        if ($this->product->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getBrands() === $this) {
                $product->setBrands(null);
            }
        }

        return $this;
    }

    public function __toString():string
    {
        return $this->name;
    }
}
