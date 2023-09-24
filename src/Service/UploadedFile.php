<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile as FileUploadedFile;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class UploadedFile {

    private SluggerInterface $slugger;
    private ParameterBagInterface $params;
    private KernelInterface $kernel;

    public function __construct(
        SluggerInterface $slugger,
        ParameterBagInterface $params,
        KernelInterface $kernel
    )
    {
        $this->slugger = $slugger;
        $this->params = $params;
        $this->kernel = $kernel;
    }

    public function upload(FileUploadedFile $file, User $user) {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
        $path = $this->kernel->getProjectDir() . $this->params->get('app.uploaded_route');

        try {
            $file->move(
                $path,
                $newFilename
            );
            if ($user->getPicture()) {
                unlink($path . '/' . $user->getPicture());
            }
        } catch (FileException $e) {
            //dd("Le fichier n'a pas pu être téléchargé");
            throw $e;
        }

        return $newFilename;
    }

}