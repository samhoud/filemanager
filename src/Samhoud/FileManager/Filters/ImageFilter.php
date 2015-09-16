<?php


namespace Samhoud\FileManager\Filters;

use Samhoud\FileManager\Filters\Contracts\ImageFilterInterface;

abstract class ImageFilter implements ImageFilterInterface
{
    /**
     * Applies filter to given image
     *
     * @param  \Intervention\Image\Image $image
     * @return \Intervention\Image\Image
     */
    abstract public function applyFilter(\Intervention\Image\Image $image);
}