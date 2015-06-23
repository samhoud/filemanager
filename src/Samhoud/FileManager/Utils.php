<?php
namespace Samhoud\FileManager;

/**
 * Class Utils
 * @package Samhoud\FileManager
 */
class Utils implements Contracts\Utils
{

    /**
     *
     * Return Laravel public path
     *
     * @return string
     */
    public static function publicPath()
    {
        return public_path();
    }

    /**
     *
     * Get contents of a file
     *
     * @param $file
     * @return string
     */
    public static function getFileContents($file)
    {
        return file_get_contents($file);
    }

    /**
     *
     * Make an url via Laravel helper
     *
     * @param $uri
     * @return string
     */
    public static function makeUrl($uri)
    {
        return url($uri);
    }


    /**
     * Get normalized pathinfo.
     *
     * @param string $path
     *
     * @return array pathinfo
     */
    public static function pathinfo($path)
    {
        $pathinfo = pathinfo($path) + compact('path');
        $pathinfo['dirname'] = self::normalizeDirname($pathinfo['dirname']);

        return $pathinfo;
    }

    /**
     * Normalize a dirname return value.
     *
     * @param string $dirname
     *
     * @return string normalized dirname
     */
    public static function normalizeDirname($dirname)
    {
        if ($dirname === '.') {
            return '';
        }

        return $dirname;
    }
}