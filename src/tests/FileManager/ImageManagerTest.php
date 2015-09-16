<?php
namespace UnitTests\FileManager;

use Samhoud\FileManager\ImageManager;
use \Mockery as m;
use UnitTests\TestCase;

class ImageManagerTest extends TestCase
{

    public $filesystem;
    public $utils;

    public function tearDown()
    {
        m::close();
    }

    public function setUp()
    {
        $this->filesystem = m::mock('Samhoud\FileManager\Contracts\Filesystem');
        $this->utils = m::mock('alias:Samhoud\FileManager\Utils');
    }

    private function basicExpectations()
    {
        $this->utils->shouldReceive('publicPath')->andReturn('/path/to/public/');
        $this->filesystem->shouldReceive('getFileSystemRootPath')->andReturn('/path/to/public/uploads/');
    }

    public function testConstructor()
    {
        $this->basicExpectations();
        $imageHandler = m::mock(\Intervention\Image\ImageManager::class);
        $fileManager = new ImageManager($this->filesystem, $imageHandler);

        $this->assertInstanceOf('Samhoud\FileManager\ImageManager', $fileManager);
        $this->assertInstanceOf('Samhoud\FileManager\Contracts\Filesystem', $fileManager->getFilesystem());
        $this->assertEquals(1, $fileManager->getSettings()->count());
    }

    public function testUpload()
    {
        $this->basicExpectations();

        $imageHandler = m::mock(\Intervention\Image\ImageManager::class);
        $image = m::mock(\Intervention\Image\Image::class);
        $image->shouldReceive('encode');


        $fileManager = new ImageManager($this->filesystem, $imageHandler);

        $this->filesystem->shouldReceive('exists')->twice()->andReturn(true, false);
        $this->filesystem->shouldReceive('put')->once()->with('image.jpg', (string)$image->encode())->andReturn(true);

        $file = m::mock('Symfony\Component\HttpFoundation\File\UploadedFile');
        $file->shouldReceive('guessClientExtension')->andReturn('jpg');
        $file->shouldReceive('getClientOriginalName')->andReturn('image.jpg');
        $file->shouldReceive('getClientMimeType')->andReturn('image/jpeg');
        $file->shouldReceive('getClientOriginalExtension')->andReturn('jpg');

        $imageHandler->shouldReceive('make')->with($file)->andReturn($image);

        $result = $fileManager->upload($file);

        $this->assertTrue($result);

    }

    public function testUploadNonImage()
    {
        $this->basicExpectations();

        $imageHandler = m::mock(\Intervention\Image\ImageManager::class);
        $fileManager = new ImageManager($this->filesystem, $imageHandler);

        $this->filesystem->shouldReceive('exists')->times(3)->andReturn(true, true, false);
        $this->filesystem->shouldReceive('put')->once()->with('file.txt', 'file contents')->andReturn(true);

        $file = m::mock('Symfony\Component\HttpFoundation\File\UploadedFile');
        $file->shouldReceive('guessClientExtension')->andReturn('txt');
        $file->shouldReceive('getClientOriginalName')->andReturn('file.txt');
        $file->shouldReceive('getClientMimeType')->andReturn('text/plain');
        $file->shouldReceive('getClientOriginalExtension')->andReturn('txt');

        $this->utils->shouldReceive('getFileContents')->once()->with($file)->andReturn('file contents');

        $result = $fileManager->upload($file);

        $this->assertTrue($result);

    }

    public function testUploadNonImageShouldFail()
    {
        $this->basicExpectations();

        $imageHandler = m::mock(\Intervention\Image\ImageManager::class);
        $fileManager = new ImageManager($this->filesystem, $imageHandler);
        $fileManager->uploadNonImages = false;

        $this->filesystem->shouldReceive('exists')->times(1)->andReturn(true);


        $file = m::mock('Symfony\Component\HttpFoundation\File\UploadedFile');
        $file->shouldReceive('guessClientExtension')->andReturn('txt');
        $file->shouldReceive('getClientOriginalName')->andReturn('file.txt');
        $file->shouldReceive('getClientMimeType')->andReturn('text/plain');
        $file->shouldReceive('getClientOriginalExtension')->andReturn('txt');

        $result = $fileManager->upload($file);

        $this->assertFalse($result);

    }

}
