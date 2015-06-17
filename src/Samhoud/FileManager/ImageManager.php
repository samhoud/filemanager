<?php


namespace Samhoud\FileManager;


use Illuminate\Contracts\Filesystem\Filesystem;

/**
 * Class ImageManager
 * @package Samhoud\FileManager
 */
class ImageManager extends FileManager implements Contracts\ImageFileManager{

	protected $uploadNonImages = true;

	/**
	 * @var \Intervention\Image\ImageManager
	 */
	protected $imageHandler;

	/**
	 * @param Filesystem $filesystem
	 * @param \Intervention\Image\ImageManager $imageHandler
	 */
	public function __construct(Filesystem $filesystem, array $settings = [], \Intervention\Image\ImageManager $imageHandler){
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
	public function upload($file, array $arguments = null){
		$this->checkUploadLocation($this->path($arguments));
		if($this->isImage($file)){
			$contents = $this->imageHandler->make($file);
			$contents->encode();
			return $this->writeFile($this->makeUploadFileName($file), (string) $contents);
		}

		return ($this->uploadNonImages ? parent::upload($file, $arguments) : false);
	}
}