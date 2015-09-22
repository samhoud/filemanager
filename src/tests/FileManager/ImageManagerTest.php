<?php
namespace UnitTests\FileManager;

use Samhoud\FileManager\File;
use Samhoud\FileManager\FilterHandler;
use Samhoud\FileManager\ImageManager;
use \Mockery as m;
use UnitTests\TestCase;
use Intervention\Image\Image;

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

    public function testEditImage(){
        // arrange
        $this->basicExpectations();


        $path = 'path/to/image.jpg';
        $fullPath = '/path/to/public/uploads/path/to/image.jpg';
        $imageHandler = m::mock(\Intervention\Image\ImageManager::class);

        $fileManager = new ImageManager($this->filesystem, $imageHandler);

        $image = m::mock(Image::class);
        $image->shouldReceive('save')->once()->andReturnSelf();
        $imageHandler->shouldReceive('make')->once()->with($fullPath)->andReturn($image);

        $this->filesystem->shouldReceive('exists')->once()->with($path)->andReturn(true);

        $filterHandler = m::mock(FilterHandler::class);
        $filterHandler->shouldReceive('handle')->once()->with($image)->andReturn($image);

        //act
        $result = $fileManager->edit($path, $filterHandler);

        //assert
        $this->assertEquals($image, $result);
    }

    /**
     * @expectedException \Samhoud\FileManager\Exceptions\FileNotFoundException
     * @expectedExceptionMessage Image not found at: /path/to/unknown_image.jpg
     */
    public function testEditImageFailsIfImageIsNotFound(){
        $this->basicExpectations();

        $imageHandler = m::mock(\Intervention\Image\ImageManager::class);
        $fileManager = new ImageManager($this->filesystem, $imageHandler);
        // arrange
        $image = m::mock(Image::class);

        $this->filesystem->shouldReceive('exists')->once()->with('/path/to/unknown_image.jpg')->andReturn(false);
        $filterHandler = m::mock(FilterHandler::class);


        $fileManager->edit('/path/to/unknown_image.jpg', $filterHandler);
    }

    public function testMakeImage(){
        $this->basicExpectations();

        $image = m::mock(Image::class);

        $imageHandler = m::mock(\Intervention\Image\ImageManager::class);
        $fileManager = new ImageManager($this->filesystem, $imageHandler);

        $imageHandler->shouldReceive('make')->with('/path/to/public/uploads/path/to/image.jpg')->andReturn($image);

        $result = $fileManager->make('path/to/image.jpg');

        $this->assertEquals($image, $result);
    }

    public function testIsImageForUploadedFile()
    {
        $this->basicExpectations();
        $imageHandler = m::mock(\Intervention\Image\ImageManager::class);
        $fileManager = new ImageManager($this->filesystem, $imageHandler);

        $file = m::mock('Symfony\Component\HttpFoundation\File\UploadedFile');
        $file->shouldReceive('guessClientExtension')->andReturn('jpg');
        $file->shouldReceive('getClientMimeType')->andReturn('image/jpeg');


        $this->assertTrue($fileManager->isImage($file));
    }

    public function testIsImageForImage()
    {
        $this->basicExpectations();

        $image = m::mock(Image::class);

        $imageHandler = m::mock(\Intervention\Image\ImageManager::class);
        $fileManager = new ImageManager($this->filesystem, $imageHandler);


        $this->assertTrue($fileManager->isImage($image));
    }

    public function testIsImageForNonImage()
    {
        $this->basicExpectations();

        $nonImage = m::mock(File::class);

        $imageHandler = m::mock(\Intervention\Image\ImageManager::class);
        $fileManager = new ImageManager($this->filesystem, $imageHandler);


        $this->assertFalse($fileManager->isImage($nonImage));
    }

    public function testListImages()
    {
        $this->basicExpectations();
        $imageHandler = m::mock(\Intervention\Image\ImageManager::class);
        $fileManager = new ImageManager($this->filesystem, $imageHandler);

        $image = m::mock(Image::class);

        $imageHandler->shouldReceive('make')->with('/path/to/public/uploads/2015/12/image.jpg')->andReturn($image);

        $fileNames = ['file1.txt', 'file2.txt', 'image.jpg'];

        $filesData1 =
            [
                'dirname' => 'path/to/files',
                'basename' => 'file1.txt',
                'extension' => 'txt',
                'filename' => 'file1',
                'path' => '2015/12/',
                'fileRoot' => '/uploads/',
                'mimetype' => 'text/plain'
            ];
        $filesData2 =
            [
                'dirname' => 'path/to/files',
                'basename' => 'file1.txt',
                'extension' => 'txt',
                'filename' => 'file1',
                'path' => '2015/12/',
                'fileRoot' => '/uploads/',
                'mimetype' => 'text/plain'
            ];

        $filesData3 =
            [
                'dirname' => 'path/to/files',
                'basename' => 'image.jpg',
                'extension' => 'jpg',
                'filename' => 'image',
                'path' => '2015/12/image.jpg',
                'mimetype' => 'image/jpeg'
            ];


        $this->filesystem->shouldReceive('getFileInfo')->once()->with("file1.txt")->andReturn($filesData1);
        $this->filesystem->shouldReceive('getFileInfo')->once()->with("file2.txt")->andReturn($filesData2);
        $this->filesystem->shouldReceive('getFileInfo')->once()->with("image.jpg")->andReturn($filesData3);
        $this->filesystem->shouldReceive('files')->once()->with("path/to/files")->andReturn(null);
        $this->filesystem->shouldReceive('files')->once()->with("2015")->andReturn(null);
        $this->filesystem->shouldReceive('files')->once()->with("2015/12")->andReturn($fileNames);

        //$this->filesystem->shouldReceive('files')->times(3)->with("path/to/files", '2015', '2015/12')->andReturn(null,null,$fileNames);
        $this->filesystem->shouldReceive('directories')->once()->with("path/to/files")->andReturn(['2015']);
        $this->filesystem->shouldReceive('directories')->once()->with("2015")->andReturn(['2015/12']);
        $this->filesystem->shouldReceive('directories')->once()->with("2015/12")->andReturn(null);

        $result = $fileManager->listImages("path/to/files");

        $this->assertInstanceOf(Image::class, $result->first());
    }
}
