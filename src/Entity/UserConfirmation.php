<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     collectionOperations={
 *         "post"={
 *             "path"="/users/confirm"
 *         }
 *     },
 *     itemOperations={
 *     },
 *     shortName="confirmation"
 * )
 */
class UserConfirmation
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=30, max=30)
     * @Groups({"confirmation:read", "confirmation:write"})
     */
    public $confirmationToken;
}