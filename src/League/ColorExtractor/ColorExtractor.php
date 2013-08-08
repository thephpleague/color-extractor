<?php namespace League\ColorExtractor;

class ColorExtractor
{
    public function loadJpeg($imagePath)
    {
        return new Image(imagecreatefromjpeg($imagePath));
    }

    public function loadPng($imagePath)
    {
        return new Image(imagecreatefrompng($imagePath));
    }

    public function loadGif($imagePath)
    {
        return new Image(imagecreatefromgif($imagePath));
    }
}
