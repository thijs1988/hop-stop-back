<?php


namespace App\Controller;

use App\Mailer\Mailer;
use App\Security\UserConfirmationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class EmailController extends AbstractController
{
    /**
     * @Route("/confirm-user/{token}", name="default_confirm_token")
     */
    public function confirmUser(
        string $token, UserConfirmationService $userConfirmationService, $reactHomeDirectory
    ){
        $userConfirmationService->confirmUser($token);

        return $this->redirect($reactHomeDirectory.'/login');
    }

//    /**
//     * @Route("/send", name="send")
//     */
//    public function send(
//        Mailer $mailer, Security $security
//    ){
//        $user = $security->getUser();
//        $mailer->sendConfirmationEmail($user);
//
//        return new Response('worked');
//
//    }
}
