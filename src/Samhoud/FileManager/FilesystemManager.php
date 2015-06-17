<?php
namespace Samhoud\FileManager;

use League\Flysystem\FilesystemInterface;

class FilesystemManager extends \Illuminate\Filesystem\FilesystemManager
{

    /**
     * Adapt the filesystem implementation.
     *
     * @param  \League\Flysystem\FilesystemInterface $filesystem
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    protected function adapt(FilesystemInterface $filesystem)
    {
        return new FilesystemAdapter($filesystem);
    }
}