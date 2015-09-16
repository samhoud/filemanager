<?php


namespace Samhoud\FileManager\Filters\Contracts;


use Samhoud\FileManager\Contracts\File;

interface FileFilterInterface extends FilterInterface
{
    /**
     * Applies filter to given file
     *
     * @param  \Samhoud\FileManager\Contracts\File $file
     * @return \Samhoud\FileManager\Contracts\File
     */
    public function applyFilter(File $file);
}