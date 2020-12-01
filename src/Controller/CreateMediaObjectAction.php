<?php


namespace App\Controller;


use App\Entity\Picture;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CreateMediaObjectAction
{
    public function __invoke(Request $request): Picture
    {
        $uploadedFile = $request->files->get('file');
        if (!$uploadedFile) {
            throw new BadRequestHttpException('"file" is required');
        }

        $mediaObject = new Picture();
        $mediaObject->setFile($uploadedFile);

        return $mediaObject;
    }
}