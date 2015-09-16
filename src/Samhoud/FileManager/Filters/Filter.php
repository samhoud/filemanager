<?php


namespace Samhoud\FileManager\Filters;


use Samhoud\FileManager\Contracts\File;
use Samhoud\FileManager\Filters\Contracts\FileFilterInterface;

abstract class Filter implements FileFilterInterface
{

    /**
     * Applies filter to given file
     *
     * @param  \Samhoud\FileManager\Contracts\File $file
     * @return \Samhoud\FileManager\Contracts\File
     */
    abstract public function applyFilter(File $file);
}