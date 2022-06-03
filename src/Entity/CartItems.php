<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Repository\CartItemsRepository;
use App\Validator\IsValidOwner;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;

/**
 * @ApiResource(
 *     itemOperations={
 *          "get"={"security"="is_granted('ROLE_USER')"},
 *          "put" = {
 *          "security"="is_granted('EDIT', object)",
 *          "security_message" = "Only the creator can edit a cart"
 *          },
 *          "patch"={
 *              "security"="is_granted('ROLE_USER')"
 *          },
 *          "delete" = { "security" = "is_granted('ROLE_ADMIN')" }
 *     },
 *      collectionOperations={
 *          "get" = {"security" = "is_granted('ROLE_USER')" },
 *          "post" = {
 *              "security" = "is_granted('ROLE_USER')"
 *      }
 *     },
 *     shortName="cart",
 *     attributes={
 *          "pagination_items_per_page"=10,
 *          "formats"={"jsonld", "json", "html", "jsonhal", "csv"={"text/csv"}}
 *     }
 * )
 * @ORM\Entity(repositoryClass=CartItemsRepository::class)
 * @ORM\EntityListeners({"App\Doctrine\CartSetOwnerListener"})
 * @UniqueEntity(fields={"cartOwner"})
 * @ApiFilter(DateFilter::class, properties={"createdAt", "expireDate"})
 * @ApiFilter(SearchFilter::class, properties={
 *     "cartOwner":"exact",
 *     "paid":"exact",
 *     "shipped":"exact"
 * })
 * @ApiFilter(PropertyFilter::class)
 * @ApiFilter(OrderFilter::class, properties={"expireDate", "createdAt"}, arguments={"orderParameterName":"order"})
 */
class CartItems
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"cart:read", "admin:write"})
     */
    private $expireDate;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"cart:read", "admin:write"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"cart:read", "admin:write"})
     */
    private $paid = false;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"cart:read", "admin:write"})
     */
    private $shipped = false;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="cartItems", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"cart:read", "cart:collection:post"})
     * @IsValidOwner()
     */
    private $cartOwner;

    /**
     * @ORM\OneToMany(targetEntity="CartProducts", mappedBy="cart")
     * @Groups({"cart:read", "cart:write"})
     * @ApiSubresource()
     */
    private $cartProducts;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $dt = new \DateTimeImmutable();
        $this->expireDate = $dt->add(new \DateInterval('P10D'));
        $this->cartProducts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getExpireDate(): ?\DateTimeInterface
    {
        return $this->expireDate;
    }

    public function setExpireDate(\DateTimeInterface $expireDate): self
    {
        $this->expireDate = $expireDate;

        return $this;
    }

    public function getPaid(): ?bool
    {
        return $this->paid;
    }

    public function setPaid(int $paid): self
    {
        $this->paid = $paid;

        return $this;
    }

    public function getShipped(): ?bool
    {
        return $this->shipped;
    }

    public function setShipped(int $shipped): self
    {
        $this->shipped = $shipped;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getCartOwner(): ?User
    {
        return $this->cartOwner;
    }

    public function setCartOwner(User $cartOwner): self
    {
        $this->cartOwner = $cartOwner;

        return $this;
    }

    /**
     * @return Collection|Products[]
     */
    public function getCartProducts(): Collection
    {
        return $this->cartProducts;
    }

    public function __toString(): string
    {
        return $this->cartOwner;
    }
}
