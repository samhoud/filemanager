<?php
namespace Samhoud\FileManager;

use Illuminate\Support\Collection;

/**
 * Class Directory
 * @package Samhoud\FileManager
 */
class Directory implements Contracts\FileSystemObject, Contracts\Directory
{

    /**
     * @var Collection
     */
    public $items;
    /**
     * @var string
     */
    public $name;
    /**
     * @var string
     */
    public $path;

    /**
     * @param string $name
     * @param string $path
     * @param Collection|null $items
     */
    function __construct($name, $path, Collection $items = null)
    {
        $this->items = $items;
        if ($items === null) {
            $this->items = new Collection();
        }
        $this->name = $name;
        $this->path = $path;
    }

    /**
     * @return bool
     */
    public function isFile()
    {
        return false;
    }

    /**
     * @return bool
     */
    public function isDirectory()
    {
        return true;
    }

    /**
     * @param Contracts\FileSystemObject $item
     */
    public function addItem(Contracts\FileSystemObject $item)
    {
        $this->items->push($item);
    }

    /**
     * @return static
     */
    public function hasDirectories()
    {
        return $this->items->filter(function ($content) {
            if ($content instanceof Directory) {
                return true;
            }
        });
    }

    /**
     * @return static
     */
    public function hasFiles()
    {
        return $this->items->filter(function ($content) {
            if ($content instanceof File) {
                return true;
            }
        });
    }
}