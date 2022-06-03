<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Controller\MediaController;

/**
 * @ORM\Entity
 * @ORM\Table(name="media")
 * @ApiResource(
 *   iri="http://schema.org/ImageObject",
 *   collectionOperations={
 *     "get",
 *     "post" = {
 *       "controller" = MediaController::class,
 *       "deserialize" = false,
 *       "openapi_context" = {
 *         "requestBody" = {
 *           "description" = "File Upload",
 *           "required" = true,
 *           "content" = {
 *             "multipart/form-data" = {
 *               "schema" = {
 *                 "type" = "object",
 *                 "properties" = {
 *                   "file" = {
 *                     "type" = "string",
 *                     "format" = "binary",
 *                     "description" = "File to be uploaded",
 *                   },
 *                  "name" = {
 *                      "type" = "string",
 *                  },
 *                 },
 *               },
 *             },
 *           },
 *         },
 *       },
 *     },
 *   },
 *   itemOperations={
 *     "get",
 *     "delete" = { "security" = "is_granted('ROLE_ADMIN')" }
 *   }
 * )
 * @UniqueEntity(fields={"name"}, message="The name of this image already exists")
 */

class Media
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @ORM\Id
     * @Groups({"media:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @ApiProperty(iri="http://schema.org/contentUrl")
     * @Groups({"media:read", "media:write", "cartproduct:read", "product:read"})
     */
    public $filePath;

    /**
     * @ORM\ManyToMany(targetEntity=Products::class, mappedBy="images", cascade={"persist", "remove"})
     * @ApiProperty(iri="http://schema.org/products")
     * @Groups({"media:read", "media:write"})
     */
    private $productImages;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @ApiProperty(iri="http://schema.org/name")
     * @Groups({"media:read", "media:write"})
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Products::class, mappedBy="logo", cascade={"persist", "remove"})
     * @ApiProperty(iri="http://schema.org/logos")
     * @Groups({"media:read", "media:write"})
     */
    private $productLogo;

    public function __construct()
    {
        $this->productImages = new ArrayCollection();
        $this->productLogo = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getFilePath()
    {
        return $this->filePath;
    }

    public function setFilePath($filePath): void
    {
        $this->filePath = $filePath;
    }

    /**
     * @return Collection|Products[]
     */
    public function getProductImages(): Collection
    {
        return $this->productImages;
    }

    public function addProductImage(Products $productImage): self
    {
        if (!$this->productImages->contains($productImage)) {
            $this->productImages[] = $productImage;
            $productImage->addImage($this);
        }

        return $this;
    }

    public function removeProductImage(Products $productImage): self
    {
        if ($this->productImages->removeElement($productImage)) {
            $productImage->removeImage($this);
        }

        return $this;
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

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @return Collection|Products[]
     */
    public function getProductLogo(): Collection
    {
        return $this->productLogo;
    }

    public function addProductLogo(Products $productLogo): self
    {
        if (!$this->productLogo->contains($productLogo)) {
            $this->productLogo[] = $productLogo;
            $productLogo->setLogo($this);
        }

        return $this;
    }

    public function removeProductLogo(Products $productLogo): self
    {
        if ($this->productLogo->removeElement($productLogo)) {
            // set the owning side to null (unless already changed)
            if ($productLogo->getLogo() === $this) {
                $productLogo->setLogo(null);
            }
        }

        return $this;
    }
}