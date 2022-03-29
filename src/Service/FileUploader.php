<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    private $targetProfile;
    private $targetTricks;

    public function __construct($targetTricks, $targetProfile)
    {
        $this->targetProfile = $targetProfile;
        $this->targetTricks = $targetTricks;
    }

    public function upload(UploadedFile $file, string $target = 'profile')
    {
        switch ($target) {
            case 'profile':
                $targetPath = $this->getTargetProfile();
                break;
            case 'tricks':
                $targetPath = $this->getTargetTricks();
                break;
            default:
                $targetPath = $this->getTargetTricks();
                break;
        }

        $target = $target.'-'.date("Ymd-His");
        $fileName = $target . '-' . uniqid() . '.' . $file->guessExtension();

        try {
            $file->move($targetPath, $fileName);
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }

        return $fileName;
    }

    public function getTargetProfile()
    {
        return $this->targetProfile;
    }

    public function getTargetTricks()
    {
        return $this->targetTricks;
    }
}
