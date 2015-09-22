<?php
namespace Samhoud\FileManager;

use Illuminate\Support\Collection;
use Intervention\Image\Image;
use Samhoud\FileManager\Contracts\Filesystem;
use Samhoud\FileManager\Exceptions\FileNotFoundException;

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
     * @param $data
     * @return Image
     */
    public function make($data)
    {
        $data = $this->filesystem->getFileSystemRootPath() . $data;
        return $this->imageHandler->make($data);
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

    public function edit($path, FilterHandler $filterHandler = null)
    {

        if (!$this->fileExists($path)) {
            throw new FileNotFoundException('Image not found at: ' . $path);
        }
        $image = $this->make($path);
        $image = $this->applyFilters($filterHandler, $image);
        return $image->save();
    }

    /**
     * @param null|string $path path to directory
     * @return array
     */
    public function listImages($path = null)
    {
        $files = parent::listImages($path)->flatten();
        $images = $files->map(function($file){
           return $this->make($file->path);
        });
        return $images;
    }

    /**
     *
     * Check if file is an image based on mime type
     *
     * @param mixed $file file to check
     * @return bool true if file has image mimetype or is instance of Intervention\Image
     */
    public function isImage($file)
    {
        return ($file instanceof Image || $this->isAllowedType($file, 'image'));
    }

}