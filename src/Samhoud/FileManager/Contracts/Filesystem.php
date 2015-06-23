<?php
namespace Samhoud\FileManager\Contracts;

/**
 * Interface Filesystem
 * @package Samhoud\FileManager\Contracts
 */
interface Filesystem extends \Illuminate\Contracts\Filesystem\Filesystem
{

    /**
     * @param null|string $path
     * @param bool|true $recursive
     * @return mixed
     */
    public function listFiles($path = null, $recursive = true);

    /**
     * @param string $filename
     * @return mixed
     */
    public function getFileInfo($filename);

    /**
     * @return mixed
     */
    public function getFileSystemRootPath();
}