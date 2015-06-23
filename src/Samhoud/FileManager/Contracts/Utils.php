<?php
namespace Samhoud\FileManager\Contracts;

interface Utils
{

    public static function publicPath();
    public static function getFileContents($file);
    public static function makeUrl($uri);
    public static function pathinfo($path);
    public static function normalizeDirname($dirname);

}