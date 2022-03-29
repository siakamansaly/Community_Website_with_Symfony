<?php

namespace App\Service;

class UrlComposer
{
    private $targetProfile;
    private $targetTricks;

    public function __construct($targetTricks, $targetProfile)
    {
        $this->targetProfile = $targetProfile;
        $this->targetTricks = $targetTricks;
    }

    public function url(string $target = 'profile', $picture)
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


        if (!filter_var($picture, FILTER_VALIDATE_URL) && $picture) {
            $picture = $targetPath . '/' . $picture;
        }
        return $picture;
    }

    public function urlArray(string $target = 'profile', $collection, $getname = 'getName'): array
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

        if ($collection) {
            $pictures = [];
            foreach ($collection as $picture) {
                switch (filter_var($picture->$getname(), FILTER_VALIDATE_URL)) {
                    case true:
                        $pictures[$picture->getId()] = $picture->$getname();
                        break;
                    default:
                        $pictures[$picture->getId()] = $targetPath . '/' . $picture->$getname();
                        break;
                }
            }
        }

        return $pictures;
    }

    public function urlEmbed(string $url): string
    {
        switch (true) {

            case strpos($url, "youtu.be") || strpos($url, "youtube.com"):
                $url=str_replace("=", "/", $url);
                $array = explode('/', $url);
                $url = "https://www.youtube.com/embed/" . end($array);
                break;

            case strpos($url, "dai.ly") || strpos($url, "dailymotion.com"):
                $array = explode('/', $url);
                $url = "https://www.dailymotion.com/embed/video/" . end($array);
                break;

            case strpos($url, "vimeo.com"):
                $array = explode('/', str_replace("-", "", $url));
                $url = "https://player.vimeo.com/video/" . filter_var(end($array), FILTER_SANITIZE_NUMBER_INT);
                break;
        }
        return $url;
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
