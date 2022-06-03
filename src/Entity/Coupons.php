<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\CouponsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;

/**
 * @ApiResource(
 *     collectionOperations={
 *          "get",
 *          "post"={
 *              "security"="is_granted('ROLE_ADMIN')",
 *          },
 *     },
 *     itemOperations={
 *          "get" = {
 *                  "security"="is_granted('ROLE_ADMIN')"
 *              },
 *          "put"={
 *              "security"="is_granted('ROLE_ADMIN') and object == user",
 *              },
 *          "patch"={
 *              "input_formats"={"json"="application/merge-patch+json"},
 *              "security"="is_granted('ROLE_ADMIN') and object == user"
 *          },
 *          "delete"={"security"="is_granted('ROLE_ADMIN')"},
 *     },
 *     shortName="coupon",
 * )
 * @ORM\Entity(repositoryClass=CouponsRepository::class)
 * @ApiFilter(SearchFilter::class, properties={
 *     "coupon":"exact",
 *     "valid":"exact",
 * })
 * @ApiFilter(DateFilter::class, properties={
 *          "expireDate"
 *     })
 */
class Coupons
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"coupon:read", "admin:write"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"coupon:read", "admin:write"})
     * @Assert\NotBlank()
     * @Assert\Length(min="5", max="255")
     */
    private $coupon;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"coupon:read", "admin:write"})
     * @Assert\NotBlank()
     * @Assert\Length(min="5", max="255")
     */
    private $purpose;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"coupon:read", "admin:write"})
     * @Assert\Type(type="boolean")
     */
    private $exclusive;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"coupon:read", "admin:write"})
     * @Assert\NotBlank()
     * @Assert\Length(min="5", max="255")
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"coupon:read", "admin:write"})
     * @Assert\NotBlank()
     * @Assert\Choice({"percent", "euro"})
     */
    private $discountType;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"coupon:read", "admin:write"})
     * @Assert\NotBlank()
     * @Assert\Type(type="integer")
     */
    private $value;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"coupon:read", "admin:write"})
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @Assert\Type(type="datetime")
     */
    private $expireDate;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"coupon:read", "admin:write"})
     * @Assert\Type(type="boolean")
     */
    private $valid = true;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCoupon(): ?string
    {
        return $this->coupon;
    }

    public function setCoupon(string $coupon): self
    {
        $this->coupon = $coupon;

        return $this;
    }

    public function getPurpose(): ?string
    {
        return $this->purpose;
    }

    public function setPurpose(string $purpose): self
    {
        $this->purpose = $purpose;

        return $this;
    }

    public function getExclusive(): ?bool
    {
        return $this->exclusive;
    }

    public function setExclusive(bool $exclusive): self
    {
        $this->exclusive = $exclusive;

        return $this;
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

    public function getDiscountType(): ?string
    {
        return $this->discountType;
    }

    public function setDiscountType(string $discountType): self
    {
        $this->discountType = $discountType;

        return $this;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(int $value): self
    {
        $this->value = $value;

        return $this;
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

    public function getValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(bool $valid): self
    {
        $this->valid = $valid;

        return $this;
    }
}
