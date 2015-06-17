<?php
namespace Samhoud\FileManager\Contracts;

use Illuminate\Contracts\Filesystem\Filesystem;

interface FileManager
{

    public function upload($file, array $arguments = null);

    public function setFilesystem(Filesystem $filesystem);

    public function checkUploadLocation($path);

    public function makeDirectory($path);

    public function directoryExists($path);

    public function deleteFile($path);

    public function fileExists($path);

    public function read($filename);

    public function listFiles($path = null, $type = null);

}