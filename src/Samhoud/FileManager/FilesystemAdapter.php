<?php
namespace Samhoud\FileManager;

use League\Flysystem\Util;

/**
 * Class FilesystemAdapter
 * @package Samhoud\FileManager
 */
class FilesystemAdapter extends \Illuminate\Filesystem\FilesystemAdapter
{

    /**
     *
     * Get extra information about a file
     *
     * @param $filename name of file
     * @return array|false|null returns null if file does not exist, or an array with file info
     */
    public function getFileInfo($filename)
    {
        if (!$this->exists($filename)) {
            return null;
        }
        $info = $this->driver->getMetadata($filename);
        $info = $info + $this->pathinfo($filename);
        $info['mimetype'] = $this->driver->getMimetype($filename);

        return $info;
    }

    /**
     * Get normalized pathinfo.
     *
     * @param string $path
     *
     * @return array pathinfo
     */
    protected function pathinfo($path)
    {
        $pathinfo = pathinfo($path) + compact('path');
        $pathinfo['dirname'] = $this->normalizeDirname($pathinfo['dirname']);

        return $pathinfo;
    }

    /**
     * Normalize a dirname return value.
     *
     * @param string $dirname
     *
     * @return string normalized dirname
     */
    protected static function normalizeDirname($dirname)
    {
        if ($dirname === '.') {
            return '';
        }

        return $dirname;
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

    public function getFileSystemRootPath()
    {
        return $this->getDriver()->getAdapter()->getPathPrefix();
    }
}