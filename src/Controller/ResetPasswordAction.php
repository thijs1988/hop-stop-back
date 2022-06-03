<?php


namespace App\Controller;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ResetPasswordAction
{
    private $passwordEncoder;
    private $entityManager;
    private $tokenManager;

    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        EntityManagerInterface $entityManager,
        JWTTokenManagerInterface $tokenManager
    )
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->entityManager = $entityManager;
        $this->tokenManager = $tokenManager;
    }

    public function __invoke(User $data)
    {
        if ($data->getNewPassword() === null || $data->getNewPassword() === ""){
            throw new Exception('The new password should not be blank');
        }
        if ($data->getNewRetypedPassword() === null || $data->getNewRetypedPassword() === ""){
            throw new Exception('The retyped password should not be blank');
        }
        if ($data->getOldPassword() === null || $data->getOldPassword() === ""){
            throw new Exception('The retyped password should not be blank');
        }

        if(preg_match("/(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{7,}/", $data->getNewPassword()) === 0){
            throw new Exception('Password must be seven characters long and contain at least one digit, one upper case letter and one lower case letter');
        }

        if($data->getNewPassword() !== $data->getNewRetypedPassword()){
            throw new Exception('Passwords do not match');
        }

        if(password_verify($data->getOldPassword() ,$data->getPassword())){
            $data->setPassword($this->passwordEncoder->encodePassword(
                $data, $data->getNewPassword()
            ));
            $data->setPasswordChangeDate(time());

            $this->entityManager->flush();

            $token = $this->tokenManager->create($data);
            return new JsonResponse(['token' =>  $token]);
        }else{
            throw new Exception('This value should be the user\'s current password.');
        }
    }
}