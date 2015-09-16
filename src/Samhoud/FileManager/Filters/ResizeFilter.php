<?php


namespace Samhoud\FileManager\Filters;


/**
 * Class ResizeFilter
 * @package Samhoud\FileManager\Filters
 */
class ResizeFilter extends ImageFilter
{
    /**
     * @var int
     */
    public $width;
    /**
     * @var int
     */
    public $height;
    /**
     * @var bool
     */
    public $constrainAspectRatio;
    /**
     * @var bool
     */
    public $preventUpsizing;

    /**
     * ResizeFilter constructor.
     * @param int  $width
     * @param int  $height
     * @param bool $constrainAspectRatio
     * @param bool $preventUpsizing
     */
    public function __construct($width = null, $height = null, $constrainAspectRatio = true, $preventUpsizing = true)
    {
        $this->width = $width;
        $this->height = $height;
        $this->constrainAspectRatio = $constrainAspectRatio;
        $this->preventUpsizing = $preventUpsizing;
    }


    /**
     * Applies filter to given image
     *
     * @param  \Intervention\Image\Image $image
     * @return \Intervention\Image\Image
     */
    public function applyFilter(\Intervention\Image\Image $image)
    {
        return $image->resize($this->width, $this->height, $this->constraint());
    }

    /**
     * @return \Closure|null
     */
    protected function constraint(){
        if($this->constrainAspectRatio or $this->preventUpsizing){
            return function ($constraint)
            {
                if($this->constrainAspectRatio) {
                    $constraint->aspectRatio();
                }
                if($this->preventUpsizing) {
                    $constraint->upsize();
                }
            };
        }
        return null;
    }
}