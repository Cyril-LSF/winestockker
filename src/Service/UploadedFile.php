<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile as FileUploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class UploadedFile {

    private $slugger;
    private $params;

    public function __construct(SluggerInterface $slugger, ParameterBagInterface $params)
    {
        $this->slugger = $slugger;
        $this->params = $params;
    }

    public function upload(FileUploadedFile $file) {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        try {
            $file->move(
                $this->params->get('app.uploaded_route'),
                $newFilename
            );
        } catch (FileException $e) {
            dd("Le fichier n'a pas pu être téléchargé");
        }

        return $newFilename;
    }

}