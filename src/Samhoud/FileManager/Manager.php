<?php
namespace Samhoud\FileManager;

use Illuminate\Contracts\Filesystem\Filesystem;

/**
 * Class Manager
 * @package Samhoud\FileManager
 */
abstract class Manager implements Contracts\FileManager
{

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var
     */
    protected $publicRoot = "";

    /**
     * @var array
     */
    protected $settings;

    /**
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem, array $settings = [])
    {
        if ($settings == []) {
            $settings = ['path' => ""];
        }
        $this->settings = $settings;
        $this->setFilesystem($filesystem);
    }

    /**
     * @param Filesystem $filesystem
     */
    public function setFilesystem(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
        $this->updatePublicRoot();
    }

    /**
     * @param array $settings
     */
    public function setSettings(array $settings)
    {
        $this->settings = $settings;
    }

    /**
     *
     */
    public function updatePublicRoot()
    {
        if (str_contains($this->filesystem->getFileSystemRootPath(), public_path())) {
            $this->publicRoot = str_replace(public_path(), "", $this->filesystem->getFileSystemRootPath());
        }
        $this->publicRoot = "";
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