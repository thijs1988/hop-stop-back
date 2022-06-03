<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use App\Entity\Media;
use App\Service\FileUploader;

#[AsController]
final class MediaController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(Request $request, FileUploader $fileUploader)
    {
        $uploadedFile = $request->files->get('file');
        $name = $request->get('name');
        if (!$uploadedFile) {
            throw new BadRequestHttpException('"file" is required');
        }

        $mediaObject = new Media();
        $mediaObject->filePath = $fileUploader->upload($uploadedFile);
        $mediaObject->setName($name);
        $this->entityManager->flush();

        return $mediaObject;
    }
}