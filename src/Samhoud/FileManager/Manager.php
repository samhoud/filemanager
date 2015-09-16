<?php
namespace Samhoud\FileManager;

use Illuminate\Support\Collection;
use Intervention\Image\Image;
use Samhoud\FileManager\Contracts\Filesystem;

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
     * @var Collection
     */
    protected $settings;

    /**
     * @param Filesystem $filesystem
     * @param Collection $settings
     * @param Collection $fileEditors
     */
    public function __construct(Filesystem $filesystem, Collection $settings = null)
    {

        $this->settings = $this->checkSettings($settings);
        $this->setFilesystem($filesystem);
    }

    /**
     * @param Collection|null $settings
     * @return array|Collection
     */
    private function checkSettings(Collection $settings = null)
    {
        if ($settings === null) {
            $settings = [
                'uploadSettings' => ['path' => "."],
            ];
            return new Collection($settings);
        }
        return $settings;
    }

    /**
     * @param Filesystem $filesystem
     * @return mixed|void
     */
    public function setFilesystem(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
        $this->updatePublicRoot();
    }


    /**
     * @return Filesystem
     */
    public function getFilesystem()
    {
        return $this->filesystem;
    }

    /**
     * @param array|Collection $settings
     */
    public function setSettings(Collection $settings)
    {
        $this->settings = $settings;
    }


    /**
     * @param null|string $key
     * @param null|string $settingKey
     * @return mixed
     */
    public function getSettings($key = null, $settingKey = null)
    {
        $settings = $this->settings;
        if ($key !== null) {
            $settings = $this->getSetting($settings, $key);
            if ($settings !== null && $settingKey !== null) {
                $settings = $this->getSetting($settings, $settingKey);
            }
        }

        return $settings;
    }

    /**
     * @param array|Collection $settings
     * @param $key
     * @return null
     */
    protected function getSetting($settings, $key)
    {
        if (is_array($settings) && array_key_exists($key, $settings)) {
            return $settings[$key];
        }
        if (!is_array($settings) && $settings->has($key)) {
            return $settings->get($key);
        }

        return null;
    }

    /**
     * update root
     * @return void
     */
    public function updatePublicRoot()
    {
        $this->publicRoot = "";
        if (str_contains($this->filesystem->getFileSystemRootPath(), Utils::publicPath())) {
            $this->publicRoot = str_replace(Utils::publicPath(), "", $this->filesystem->getFileSystemRootPath());
        }
    }

    /**
     * @return string
     */
    public function getPublicRoot()
    {
        return $this->publicRoot;
    }


    /**
     * @param FilterHandler|null $filterHandler
     * @param $file
     * @return File|Image
     */
    protected function applyFilters(FilterHandler $filterHandler = null, $file)
    {
        if ($filterHandler) {
            $file = $filterHandler->handle($file);
        }
        return $file;
    }

    /**
     *
     * get upload path
     *
     * @param null|array $arguments
     * @return string upload path
     * @throws \Exception if no path is set, throw exception
     */
    abstract protected function path(array $arguments = null);
}