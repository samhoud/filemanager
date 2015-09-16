<?php
namespace Samhoud\FileManager;

use Illuminate\Support\Collection;
use Samhoud\FileManager\Contracts\Filesystem;

/**
 * Class ImageManager
 * @package Samhoud\FileManager
 */
class ImageManager extends FileManager implements Contracts\ImageFileManager
{

    /**
     * @var bool
     */
    public $uploadNonImages = true;

    /**
     * @var \Intervention\Image\ImageManager
     */
    protected $imageHandler;

    /**
     * @param Filesystem $filesystem
     * @param \Intervention\Image\ImageManager $imageHandler
     * @param array $settings
     */
    public function __construct(
        Filesystem $filesystem,
        \Intervention\Image\ImageManager $imageHandler,
        Collection $settings = null
    ) {
        $this->imageHandler = $imageHandler;
        parent::__construct($filesystem, $settings);
    }


    /**
     *
     * upload an image. If not an image, upload as file
     *
     * @param mixed $file
     * @param null $arguments
     * @return bool
     */
    public function upload($file, array $arguments = null, FilterHandler $filterHandler = null)
    {
        $path = $this->path($arguments);
        $this->checkUploadLocation($path);
        if ($this->isImage($file)) {
            $image = $this->imageHandler->make($file);
            $image = $this->applyFilters($filterHandler, $image);
            return $this->writeFile($this->makeUploadFileName($file, $path), (string)$image->encode());
        }

        return ($this->uploadNonImages ? parent::upload($file, $arguments) : false);
    }
}