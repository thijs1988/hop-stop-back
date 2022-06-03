<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Repository\UserRepository;
use App\Controller\ResetPasswordAction;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     attributes={
 *     "security"="is_granted('ROLE_USER')"
 *      },
 *     collectionOperations={
 *          "get" = {"security"="is_granted('ROLE_ADMIN')"},
 *          "post"={
 *              "security"="is_granted('IS_AUTHENTICATED_ANONYMOUSLY')",
 *              "validation_groups" = { "Default", "create"}
 *          },
 *     },
 *     itemOperations={
 *          "get" = {"security"="is_granted('ROLE_USER')"},
 *          "put"={
 *     "security"="is_granted('ROLE_USER') and object == user",
 *                 "validation_groups" = { "Default", "update"}
 *              },
 *          "put-reset-password"={
 *             "access_control"="is_granted('IS_AUTHENTICATED_FULLY') and object == user",
 *             "method"="PATCH",
 *             "path"="/users/{id}/reset-password",
 *             "controller"=ResetPasswordAction::class,
 *             "denormalization_context"={
 *                 "groups"={"put-reset-password"}
 *             },
 *             "validation_groups"={"put-reset-password"}
 *         },
 *          "patch"={
 *              "input_formats"={"json"="application/merge-patch+json"},
 *              "security"="is_granted('ROLE_USER') and object == user"
 *          },
 *          "delete"={"security"="is_granted('ROLE_ADMIN')"},
 *     },
 * )
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ApiFilter(PropertyFilter::class)
 * @ApiFilter(SearchFilter::class, properties={
 *     "name":"partial",
 *     "street":"partial",
 *     "username":"partial",
 *     "place":"partial",
 *     "email":"partial"
 * })
 * @ApiFilter(DateFilter::class, properties={"createdAt"})
 * @ApiFilter(OrderFilter::class, properties={"name", "email", "dateOfBirth", "createdAt"}, arguments={"orderParameterName":"order"})
 * @UniqueEntity(fields={"username"}, message="Username is already in use.")
 * @UniqueEntity(fields={"email"}, message="Email is already in use.")
 */

class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\Email(groups={"create", "update"})
     * @Assert\NotBlank(groups={"create", "update"})
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups({"owner:read", "admin:read", "user:write"})
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     * @Groups({"owner:read", "admin:read", "admin:write"})
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @Groups({"user:write"})
     * @SerializedName("password")
     * @Assert\NotBlank(groups={"create"})
     * @Assert\Regex(
     *     pattern="/(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{7,}/",
     *     message="Password must be seven characters long and contain at least one digit, one upper case letter and one lower case letter",
     *     groups={"create"}
     * )
     */
    private $plainPassword;

    /**
     * @Groups({"create", "user:write"})
     * @Assert\Expression(
     *     "this.getPlainPassword() === this.getRetypedPassword()",
     *     message="Passwords do not match",
     *     groups={"create"}
     * )
     */
    private $retypedPassword;

    /**
     * @Groups({"put-reset-password"})
     */
    private $newPassword;

    /**
     * @Groups({"put-reset-password"})
     */
    private $newRetypedPassword;

    /**
     * @Groups({"put-reset-password"})
     */
    private $oldPassword;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Groups({"user:read", "user:write", "cart:item:get"})
     * @Assert\NotBlank(groups={"create", "update"})
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=50)
     * @Groups({"admin:read", "user:write", "owner:read"})
     * @Assert\NotBlank(groups={"create", "update"})
     * @Assert\Length(
     *     max="35",
     *     maxMessage="The value of the Phone Number cannot be longer then 35 digits",
     *     groups={"create", "update"}
     * )
     */
    private $phoneNumber;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user:read", "admin:read", "user:write"})
     * @Assert\NotBlank(groups={"create", "update"})
     */
    private $street;

    /**
     * @ORM\Column(type="string", length=10)
     * @Groups({"user:read", "user:write"})
     * @Assert\NotBlank(groups={"create", "update"})
     * @Assert\Length(
     *     min=4,
     *     max=10,
     *     maxMessage="The value of the Postal Code is too long. It should have 10 characters or less.",
     *     groups={"create", "update"}
     *     )
     */
    private $postbox;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user:read", "user:write"})
     * @Assert\NotBlank(groups={"create", "update"})
     */
    private $place;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"user:read", "user:write"})
     * @Assert\NotBlank(groups={"create", "update"})
     * @Assert\GreaterThanOrEqual(
     *     value = 18,
     *     message="You are too young to register, sorry!",
     *     groups={"create", "update"}
     * )
     */
    private $age;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"user:read"})
     */
    private $createdAt;

    /**
     * @ORM\OneToOne(targetEntity=CartItems::class, mappedBy="cartOwner", cascade={"persist", "remove"})
     * @Groups({"user:read"})
     * @ApiSubresource()
     */
    private $cartItems;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user:read", "user:write"})
     * @Assert\NotBlank(groups={"create", "update"})
     */
    private $name;

    /**
     * @ORM\Column(type="boolean")
     */
    private $enabled;

    /**
     * @ORM\Column(type="string", length=40, nullable=true)
     */
    private $confirmationToken;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $passwordChangeDate;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->enabled = false;
        $this->confirmationToken = null;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
         $this->plainPassword = null;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

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

    public function getPostbox(): ?string
    {
        return $this->postbox;
    }

    public function setPostbox(?string $postbox): self
    {
        $this->postbox = $postbox;

        return $this;
    }

    public function getPlace(): ?string
    {
        return $this->place;
    }

    public function setPlace(?string $place): self
    {
        $this->place = $place;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * @param mixed $street
     */
    public function setStreet($street): void
    {
        $this->street = $street;
    }

    /**
     * @return mixed
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * @param mixed $age
     */
    public function setAge($age): void
    {
        $this->age = $age;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCartItems(): ?CartItems
    {
        return $this->cartItems;
    }

    public function setCartItems(CartItems $cartItems): self
    {
        // set the owning side of the relation if necessary
        if ($cartItems->getCartOwner() !== $this) {
            $cartItems->setCartOwner($this);
        }

        $this->cartItems = $cartItems;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    public function getConfirmationToken()
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken($confirmationToken): void
    {
        $this->confirmationToken = $confirmationToken;
    }

    public function getRetypedPassword(): ?string
    {
        return $this->retypedPassword;
    }

    public function setRetypedPassword($retypedPassword): void
    {
        $this->retypedPassword = $retypedPassword;
    }

    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    public function setNewPassword($newPassword): void
    {
        $this->newPassword = $newPassword;
    }


    public function getNewRetypedPassword(): ?string
    {
        return $this->newRetypedPassword;
    }

    public function setNewRetypedPassword($newRetypedPassword): void
    {
        $this->newRetypedPassword = $newRetypedPassword;
    }

    public function getOldPassword()
    {
        return $this->oldPassword;
    }

    public function setOldPassword($oldPassword): void
    {
        $this->oldPassword = $oldPassword;
    }

    public function getPasswordChangeDate()
    {
        return $this->passwordChangeDate;
    }

    public function setPasswordChangeDate($passwordChangeDate): void
    {
        $this->passwordChangeDate = $passwordChangeDate;
    }

    public function __toString():string
    {
        return $this->email;
    }
}
