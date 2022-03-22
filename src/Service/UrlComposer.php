<?php

namespace App\Service;

use Doctrine\Common\Collections\Collection;

class UrlComposer
{
    private $targetProfileRelative;
    private $targetTricksRelative;

    public function __construct($targetTricksRelative, $targetProfileRelative)
    {
        $this->targetProfile = $targetProfileRelative;
        $this->targetTricks = $targetTricksRelative;
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
            case strpos($url, "youtu.be"):
                $url = "https://www.youtube.com/embed" . parse_url($url, PHP_URL_PATH);
                break;
            case strpos($url, "youtube.com"):
                parse_str(parse_url($url, PHP_URL_QUERY), $my_array_of_vars);
                $url = "https://www.youtube.com/embed/" . $my_array_of_vars['v'];
                break;
            case strpos($url, "dai.ly"):
                $url = "https://www.dailymotion.com/embed/video" . parse_url($url, PHP_URL_PATH);
                break;
            case strpos($url, "dailymotion.com"):
                $url = "https://www.dailymotion.com/embed" . parse_url($url, PHP_URL_PATH);
                break;
            case strpos($url, "vimeo.com"):
                $url = "https://player.vimeo.com/video/" . str_replace('-', '', filter_var(parse_url($url, PHP_URL_PATH), FILTER_SANITIZE_NUMBER_INT));
                break;
                https: //vimeo.com/fr/stock/clip-345430482-flying-over-a-misty-autumn-forest-at-sunset-               
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
