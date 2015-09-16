<?php
namespace Samhoud\FileManager\Contracts;

use Illuminate\Support\Collection;
use Samhoud\FileManager\FilterHandler;

/**
 * Interface FileManager
 * @package Samhoud\FileManager\Contracts
 */
interface FileManager
{

    /**
     * @param $file
     * @param array|null $arguments
     * @param FilterHandler $filterHandler
     * @return mixed
     */
    public function upload($file, array $arguments = null, FilterHandler $filterHandler = null);

    /**
     * @param \Samhoud\FileManager\Contracts\Filesystem $filesystem
     * @return mixed
     */
    public function setFilesystem(Filesystem $filesystem);

    /**
     * @return mixed
     */
    public function getFilesystem();

    /**
     * @param Collection $settings
     * @return void
     */
    public function setSettings(Collection $settings);

    /**
     * @param null|string $key
     * @param null|string $settingKey
     * @return mixed
     */
    public function getSettings($key = null, $settingKey = null);

    /**
     * @return string
     */
    public function getPublicRoot();

    /**
     * @return void
     */
    public function updatePublicRoot();

    /**
     * @param $path
     * @return mixed
     */
    public function checkUploadLocation($path);

    /**
     * @param $path
     * @return mixed
     */
    public function makeDirectory($path);

    /**
     * @param $path
     * @return mixed
     */
    public function directoryExists($path);

    /**
     * @param $path
     * @return mixed
     */
    public function deleteFile($path);

    /**
     * @param $path
     * @return mixed
     */
    public function fileExists($path);

    /**
     * @param $filename
     * @return mixed
     */
    public function read($filename);

    /**
     * @param null $path
     * @param null $type
     * @return mixed
     */
    public function listFiles($path = null, $type = null);

}