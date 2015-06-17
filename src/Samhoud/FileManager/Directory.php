<?php
namespace Samhoud\FileManager;

use Illuminate\Support\Collection;

class Directory implements Contracts\FileSystemObject, Contracts\Directory
{

    public $items;
    public $name;
    public $path;

    function __construct($name, $path, Collection $items = null)
    {
        $this->items = $items;
        if ($items === null) {
            $this->items = new Collection();
        }
        $this->name = $name;
        $this->path = $path;
    }

    public function isFile()
    {
        return false;
    }

    public function isDirectory()
    {
        return true;
    }

    public function addItem(Contracts\FileSystemObject $item)
    {
        $this->items->push($item);
    }

    public function hasDirectories()
    {
        return $this->items->filter(function ($content) {
            if ($content instanceof Directory) {
                return true;
            }
        });
    }

    public function hasFiles()
    {
        return $this->items->filter(function ($content) {
            if ($content instanceof File) {
                return true;
            }
        });
    }
}