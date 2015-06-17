<?php


namespace Samhoud\FileManager;

use Illuminate\Contracts\Filesystem\Filesystem;

/**
 * Class Manager
 * @package Samhoud\FileManager
 */
abstract class Manager implements Contracts\FileManager{

	/**
	 * @var Filesystem
	 */
	protected $filesystem;

	/**
	 * @var array
	 */
	protected $settings;
	/**
	 * @param Filesystem $filesystem
	 */
	public function __construct(Filesystem $filesystem, array $settings = []){
		if($settings == []){
			$settings = ['path' => ""];
		}

		$this->settings     = $settings;
		$this->filesystem   = $filesystem;
	}

	/**
	 * @param Filesystem $filesystem
	 */
	public function setFilesystem(Filesystem $filesystem){
		$this->filesystem = $filesystem;
	}

	public function setSettings(array $settings){
		$this->settings = $settings;
	}


	/**
	 *
	 * get upload path
	 *
	 * @param null|array $uploadSettings
	 * @return string upload path
	 * @throws \Exception if no path is set, throw exception
	 */
	abstract protected function path(array $arguments = null);
}