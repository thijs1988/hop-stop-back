<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\TransactionsRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *          itemOperations={
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
 *          "get"={
 *              "security"="is_granted('ROLE_ADMIN')"
 *          },
 *          "post",
 *     },
 *     shortName="transaction",
 *     attributes={
 *          "pagination_items_per_page"=10,
 *          "formats"={"jsonld", "json", "html", "jsonhal", "csv"={"text/csv"}}
 *     }
 * )
 * @ORM\Entity(repositoryClass=TransactionsRepository::class)
 */
class Transactions
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @ApiProperty(identifier=false)
     */
    private $id;

    /**
     * @ORM\Column(type="uuid")
     * @ApiProperty(identifier=true)
     * @Groups({"transaction:write"})
     */
    private $orderId;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"transaction:write", "transaction:read"})
     */
    private $cartId;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"transaction:write", "transaction:read"})
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"transaction:write", "transaction:read"})
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"transaction:write", "transaction:read"})
     * @Assert\NotBlank()
     */
    private $street;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"transaction:write", "transaction:read"})
     * @Assert\NotBlank()
     */
    private $place;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"transaction:write", "transaction:read"})
     * @Assert\NotBlank()
     */
    private $postbox;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"transaction:write", "transaction:read"})
     * @Assert\NotBlank()
     */
    private $country;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"transaction:write", "transaction:read"})
     * @Assert\NotBlank()
     */
    private $amount;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     * @Groups({"transaction:write", "transaction:read"})
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $phoneNumber;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"transaction:write", "transaction:read"})
     */
    private $items;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"transaction:write", "admin:write"})
     */
    private $shipped = false;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     * @Groups({"transaction:read", "admin:write"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"transaction:read", "admin:write"})
     */
    private $coupon;

    public function __construct(UuidInterface $orderId = null)
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->orderId = $orderId ?: Uuid::uuid4();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderId(): UuidInterface
    {
        return $this->orderId;
    }

    /**
     * @param UuidInterface|null $orderId
     */
    public function setOrderId(?UuidInterface $orderId): void
    {
        $this->orderId = $orderId;
    }

    public function getCartId(): ?int
    {
        return $this->cartId;
    }

    public function setCartId(int $cartId): self
    {
        $this->cartId = $cartId;

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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function getPlace(): ?string
    {
        return $this->place;
    }

    public function setPlace(string $place): self
    {
        $this->place = $place;

        return $this;
    }

    public function getPostbox(): ?string
    {
        return $this->postbox;
    }

    public function setPostbox(string $postbox): self
    {
        $this->postbox = $postbox;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getItems(): ?string
    {
        return $this->items;
    }

    public function setItems(?string $items): self
    {
        $this->items = $items;

        return $this;
    }

    public function getShipped(): ?bool
    {
        return $this->shipped;
    }

    public function setShipped(bool $shipped): self
    {
        $this->shipped = $shipped;

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

    public function getCoupon(): ?int
    {
        return $this->coupon;
    }

    public function setCoupon(?int $coupon): self
    {
        $this->coupon = $coupon;

        return $this;
    }
}
