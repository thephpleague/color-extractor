<?php

namespace League\ColorExtractor;

/**
 * Class Client
 * @package League\ColorExtractor
 */
class Client
{
    /**
     * @param $imagePath
     *
     * @return Image
     */
    public function loadJpeg($imagePath)
    {
        return new Image(\imagecreatefromjpeg($imagePath));
    }

    /**
     * @param $imagePath
     *
     * @return Image
     */
    public function loadPng($imagePath)
    {
        return new Image(\imagecreatefrompng($imagePath));
    }

    /**
     * @param $imagePath
     *
     * @return Image
     */
    public function loadGif($imagePath)
    {
        return new Image(\imagecreatefromgif($imagePath));
    }
}
