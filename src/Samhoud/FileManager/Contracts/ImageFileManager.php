<?php
namespace Samhoud\FileManager\Contracts;

interface ImageFileManager
{

    public function listImages($path = null);

    public function isImage($file);
}