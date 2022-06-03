<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Repository\CartProductsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

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
 *          "delete",
 *     },
 *      collectionOperations={
 *          "get" = {"security" = "is_granted('ROLE_USER')",
 *      },
 *          "post",
 *     },
 *     shortName="cartproduct",
 *     attributes={
 *          "pagination_client_enabled"=true,
 *          "pagination_items_per_page"=10,
 *          "formats"={"jsonld", "json", "html", "jsonhal", "csv"={"text/csv"}}
 *     }
 * )
 * @ApiFilter(SearchFilter::class, properties={
 *     "cart":"exact",
 *     "id":"exact"
 * })
 * @ORM\Entity(repositoryClass=CartProductsRepository::class)
 */
class CartProducts
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotNull()
     * @Assert\NotBlank()
     * @Assert\Positive()
     * @ORM\Column(type="integer")
     * @Groups({"cartproduct:read", "cartproduct:write", "cart:read", "product:read"})
     */
    private $amount;

    /**
     * @ORM\Column(type="float")
     * @Groups({"cartproduct:read", "cartproduct:write", "cart:read", "product:read"})
     */
    private $price;

    /**
     * @ORM\ManyToOne(targetEntity="CartItems")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"cartproduct:read", "cartproduct:write", "cart:read", "product:read"})
     */
    private $cart;

    /**
     * @ORM\ManyToOne(targetEntity="Products")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"cartproduct:read", "cartproduct:write", "cart:read", "product:read"})
     */
    private $product;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

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

    /**
     * @return mixed
     */
    public function getCart()
    {
        return $this->cart;
    }

    /**
     * @param mixed $cart
     */
    public function setCart($cart): void
    {
        $this->cart = $cart;
    }

    /**
     * @return mixed
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param mixed $product
     */
    public function setProduct($product): void
    {
        $this->product = $product;
    }


}
