<?php

namespace League\ColorExtractor;

/**
 * Class Client
 * @package League\ColorExtractor
 */
class Client
{
    /**
     * @param resource $resource
     *
     * @return Image
     */
    public function loadResource($resource)
    {
        if (!is_resource($resource) || get_resource_type($resource) != 'gd') {
            throw new \InvalidArgumentException('Image must be a gd resource');
        }

        return new Image($resource);
    }

    /**
     * @param string $imagePath
     *
     * @return Image
     */
    public function loadFile($imagePath)
    {
        return new Image(imagecreatefromstring(file_get_contents($imagePath)));
    }
    
    /**
     * @param string $imagePath
     *
     * @return Image
     */
    public function loadJpeg($imagePath)
    {
        return new Image(imagecreatefromjpeg($imagePath));
    }

    /**
     * @param string $imagePath
     *
     * @return Image
     */
    public function loadPng($imagePath)
    {
        return new Image(imagecreatefrompng($imagePath));
    }

    /**
     * @param string $imagePath
     *
     * @return Image
     */
    public function loadGif($imagePath)
    {
        return new Image(imagecreatefromgif($imagePath));
    }
}
