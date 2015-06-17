<?php
namespace Samhoud\FileManager;

/**
 * Class File
 * @package Samhoud\FileManager
 */
class File implements Contracts\FileSystemObject, Contracts\File
{

    /**
     *
     */
    const ISFILE = true;
    /**
     *
     */
    const ISDIR = false;

    /**
     * @var string
     */
    public $fileRoot = "";
    /**
     * @var string
     */
    public $mimetype;
    /**
     * @var string
     */
    public $extension;
    /**
     * @var integer
     */
    public $size;
    /**
     * @var integer
     */
    public $timestamp;
    /**
     * @var string
     */
    public $filename;
    /**
     * @var string
     */
    public $basename;
    /**
     * @var string|Directory
     */
    public $directory;
    /**
     * @var string
     */
    public $path;
    /**
     * @var string
     */
    public $type;

    /**
     * @param array $arguments
     */
    public function __construct(array $arguments)
    {
        foreach (array_keys($arguments) as $key) {
            $this->{$key} = $arguments[$key];
        }
    }

    /**
     * @return bool
     */
    public function isFile()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isDirectory()
    {
        return false;
    }

    /**
     * @return string
     */
    public function url()
    {
        return url($this->fileRoot . $this->path);
    }
}