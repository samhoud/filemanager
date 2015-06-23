<?php
namespace Samhoud\FileManager;

use League\Flysystem\Util;
use Samhoud\FileManager\Contracts\Filesystem;

/**
 * Class FilesystemAdapter
 * @package Samhoud\FileManager
 */
class FilesystemAdapter extends \Illuminate\Filesystem\FilesystemAdapter implements Filesystem
{

    /**
     *
     * Get extra information about a file
     *
     * @param string $filename name of file
     * @return array|false|null returns null if file does not exist, or an array with file info
     */
    public function getFileInfo($filename)
    {
        if (!$this->exists($filename)) {
            return null;
        }
        $info = $this->driver->getMetadata($filename);
        $info = $info + Utils::pathinfo($filename);
        $info['mimetype'] = $this->driver->getMimetype($filename);

        return $info;
    }


    /**
     *
     * list files in given path
     *
     * @param null $path
     * @param bool|true $recursive
     * @return array list of files
     */
    public function listFiles($path = null, $recursive = true)
    {
        $listing = $this->driver->listContents($path, $recursive);

        return $listing;
    }

    /**
     *
     * Get the root of the filesystem
     *
     * @return string
     */
    public function getFileSystemRootPath()
    {
        return $this->getDriver()->getAdapter()->getPathPrefix();
    }
}