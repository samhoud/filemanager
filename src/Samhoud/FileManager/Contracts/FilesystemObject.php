<?php
namespace Samhoud\FileManager\Contracts;

interface FilesystemObject
{

    public function isFile();

    public function isDirectory();
}