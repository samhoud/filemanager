<?php
namespace Samhoud\FileManager;

use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class FileManager
 * @package Samhoud\FileManager
 */
class FileManager extends Manager
{

    /**
     * @var
     */
    protected $pathSettings;

    /**
     * @var array allowed mime types per file type
     */
    protected $filters = [
        'image' => [
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpe'  => 'image/jpeg',
            'png'  => 'image/png',
            'gif'  => 'image/gif',
            'bmp'  => 'image/bmp',
            'tiff' => 'image/tiff',
            'tif'  => 'image/tiff'
        ]
    ];

    /**
     *
     * get upload path
     *
     * @param array $arguments
     * @return string upload path
     * @throws \Exception if no path is set, throw exception
     * @internal param array|null $uploadSettings
     */
    protected function path(array $arguments = null)
    {
        $path = null;
        $uploadSettings = $this->settings['uploadSettings'];
        if ($arguments !== null && array_key_exists('uploadSettings', $arguments)) {
            $uploadSettings = $arguments['uploadSettings'];
        }
        if (array_key_exists('date', $uploadSettings)) {
            $path = date($uploadSettings['date']);
        }
        if (array_key_exists('path', $uploadSettings)) {
            $path = $uploadSettings['path'];
        }
        if ($path === null) {
            throw new \Exception('cannot create path name. Incorrect configuration');
        }

        return $this->checkPathName($path);
    }


    /**
     * @param $path
     * @return string
     */
    protected function checkPathName($path)
    {
        if (trim($path) == "" || $path == ".") {
            return "";
        }
        $path = ltrim($path, '/');

        return rtrim($path, '/') . '/';
    }

    /**
     *
     * Upload a file
     *
     * @param mixed $file file to upload
     * @param null $arguments
     * @return bool result
     */
    public function upload($file, array $arguments = null)
    {
        $this->checkUploadLocation($this->path($arguments));
        $contents = file_get_contents($file);

        return $this->writeFile($this->makeUploadFileName($file), (string) $contents);
    }

    /**
     *
     * Write file contents to given location
     *
     * @param string $filename path and name to new file
     * @param mixed $contents file contents
     * @return bool
     */
    protected function writeFile($filename, $contents)
    {
        return $this->filesystem->put($filename, (string) $contents);
    }

    /**
     *
     * Check if file is an image based on mime type
     *
     * @param mixed $file file to check
     * @return bool true if file has image mimetype
     */
    public function isImage($file)
    {
        return $this->isAllowedType($file, 'image');
    }


    /**
     *
     * Check if directory exists. If directory does not exists, create it
     * @param $path
     * @return void
     */
    public function checkUploadLocation($path)
    {
        if (!$this->directoryExists($path)) {
            $this->makeDirectory($path);
        }
    }

    /**
     *
     * Create directory
     *
     * @param string $path path to directory
     * @return void
     */
    public function makeDirectory($path)
    {
        $this->filesystem->makeDirectory($path);
    }

    /**
     *
     * Check if directory exists
     *
     * @param string $path path name
     * @return bool true is exists
     */
    public function directoryExists($path)
    {
        if ($this->filesystem->exists($path)) {
            return true;
        }

        return false;
    }

    /**
     *
     * Delete a file
     *
     * @param string $path path to file
     * @return bool true if file is deleted
     */
    public function deleteFile($path)
    {
        return $this->filesystem->delete($path);
    }

    /**
     *
     * Check if file exists
     *
     * @param string $path path to file
     * @return bool true if file exitst
     */
    public function fileExists($path)
    {
        return $this->filesystem->exists($path);
    }

    /**
     *
     * Read a file from the filesystem
     *
     * @param string $filename name of file to read
     * @return string file contents
     */
    public function read($filename)
    {
        return $this->filesystem->get($filename);
    }

    /**
     *
     *    List files in directory
     *
     * @param null|string $path path to directory
     * @param null|string $type specify file type
     * @return Directory file list
     */
    public function listFiles($path = null, $type = null)
    {
        $items = $this->getFilesInDirectory($path, $type);

        return $items;
    }

    /**
     * @param null|string $path path to directory
     * @return array
     */
    public function listImages($path = null)
    {
        $files = $this->convertFiles($this->filesystem->listFiles(), 'image');

        return $files;
    }

    /**
     * @param null|string $path path to directory
     * @param null|string $type specify file type
     * @return array|null files in path
     */
    protected function getFilesInPath($path = null, $type = null)
    {
        $files = $this->filesystem->files($path);
        if (count($files) == 0) {
            return null;
        }
        $pathFiles = [];
        foreach ($files as $key => $file) {
            $file = $this->makeFile($file);;
            if ($this->isAllowedType($file, $type)) {
                $pathFiles[$key] = $file;
            }
        }

        return $pathFiles;
    }

    /**
     * @param $files
     * @param null $type
     * @return array
     */
    protected function convertFiles($files, $type = null)
    {
        $fileList = [];
        foreach ($files as $file) {
            $file = $this->convertFile($file, $type);
            if ($file) {
                $fileList[] = $file;
            }
        }

        return $fileList;
    }

    /**
     * @param $fileData
     * @param $type
     * @return null|File
     */
    protected function convertFile($fileData, $type)
    {
        if ($fileData['type'] == 'file') {
            $file = $this->makeFile($fileData['path']);
            if ($this->isAllowedType($file, $type)) {
                return $file;
            }
        }

        return null;
    }

    /**
     * @param $filename
     * @return File
     */
    protected function makeFile($filename)
    {
        return new File($this->getFileInfo($filename));
    }

    protected function getFileInfo($filename)
    {
        $file = $this->filesystem->getFileInfo($filename);
        $file['fileRoot'] = $this->publicRoot;

        return $file;
    }

    /**
     * @param $file
     * @param null $type
     * @return bool
     */
    protected function isAllowedType($file, $type = null)
    {
        if ($type === null) {
            return true;
        }
        if ($file instanceof UploadedFile) {
            $extension = $file->guessClientExtension();
            $mimetype = $file->getClientMimeType();
        } else {
            $extension = $file->extension;
            $mimetype = $file->mimetype;
        }
        $mimes = $this->filters[$type];
        if (array_key_exists($extension, $mimes)) {
            return $mimes[$extension] == $mimetype;
        }

        return false;
    }

    /**
     * @param null|string $path path to directory
     * @param null $type
     * @return Directory
     */
    protected function getFilesInDirectory($path = null, $type = null)
    {
        $files = $this->getFilesInPath($path, $type);
        $directory = new Directory($this->getBaseName($path), $path, new Collection($files, false));
        $directories = $this->filesystem->directories($path);
        if (count($directories) > 0) {
            foreach ($this->filesystem->directories($path) as $childDirectory) {
                $directory->addItem($this->getFilesInDirectory($childDirectory, $type));
            }
        }

        return $directory;
    }

    /**
     * @param $name
     * @return mixed
     */
    protected function getBaseName($name)
    {
        $nameParts = explode("/", $name);

        return last($nameParts);
    }


    /**
     * @param $file
     * @return string
     */
    protected function makeUploadFileName($file)
    {
        $filename = $this->path() . $file->getClientOriginalName();
        $i = 2;
        while ($this->filesystem->exists($filename)) {
            $ext = $file->getClientOriginalExtension();
            $filename = $file->getClientOriginalName();
            $filename = $this->path() . str_replace('.' . $ext, '_' . $i . '.' . $ext, $filename);
            $i++;
        }

        return $filename;
    }
}