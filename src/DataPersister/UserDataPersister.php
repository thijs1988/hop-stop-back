<?php


namespace App\DataPersister;


use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\User;
use App\Mailer\Mailer;
use App\Security\TokenGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserDataPersister implements DataPersisterInterface
{

    private $entityManager;
    private $passwordEncoder;
    private $tokenGenerator;
    private $mailer;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder,
        TokenGenerator $tokenGenerator,
        Mailer $mailer
    )
    {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->tokenGenerator = $tokenGenerator;
        $this->mailer = $mailer;
    }

    public function supports($data): bool
    {
        return $data instanceof User;
    }

    /**
     * @param User $data
     */
    public function persist($data)
    {
        if ($data->getPlainPassword()){
            $data->setPassword(
                $this->passwordEncoder->encodePassword($data, $data->getPlainPassword())
            );

            $data->eraseCredentials();
        }

        $data->setConfirmationToken($this->tokenGenerator->getRandomSecureToken());
        $this->entityManager->persist($data);
        $this->entityManager->flush();
        $this->mailer->sendConfirmationEmail($data);
    }

    public function remove($data)
    {
        $this->entityManager->remove($data);
        $this->entityManager->flush();
    }
}