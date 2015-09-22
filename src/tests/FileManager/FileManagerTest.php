<?php
namespace UnitTests\FileManager;

use Illuminate\Support\Collection;
use Samhoud\FileManager\FileManager;
use Samhoud\FileManager\File;
use Samhoud\FileManager\FilterHandler;
use UnitTests\TestCase;
use \Mockery as m;

class FileManagerTest extends TestCase
{
    public $filesystem;
    public $utils;

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
        $fileManager = new FileManager($this->filesystem);

        $this->assertInstanceOf('Samhoud\FileManager\FileManager', $fileManager);
        $this->assertInstanceOf('Samhoud\FileManager\Contracts\Filesystem', $fileManager->getFilesystem());
        $this->assertEquals(1, $fileManager->getSettings()->count());
    }

    public function testConstructorWithSettings()
    {
        $this->basicExpectations();

        $settings = new Collection(
            [
                'uploadSettings' => ['path' => 'test/path'],
            ]);

        $fileManager = new FileManager($this->filesystem, $settings);

        $this->assertInstanceOf('Samhoud\FileManager\FileManager', $fileManager);
        $this->assertInstanceOf('Samhoud\FileManager\Contracts\Filesystem', $fileManager->getFilesystem());
        $this->assertEquals('test/path', $fileManager->getSettings('uploadSettings', 'path'));
    }


    /**
     * @expectedException \Exception
     * @expectedExceptionMessage cannot create path name. Incorrect configuration
     */

    public function testUploadWithInvalidSettings()
    {
        $this->basicExpectations();

        $settings = new Collection(
            [
                'uploadSettings' => ['wrong' => null],
            ]
        );
        $fileManager = new FileManager($this->filesystem, $settings);

        $file = m::mock('Symfony\Component\HttpFoundation\File\UploadedFile');

        $fileManager->upload($file);
    }

    public function testUpload()
    {
        $this->basicExpectations();
        $fileManager = new FileManager($this->filesystem);
        $this->filesystem->shouldReceive('exists')->twice()->andReturn(true, false);
        $this->filesystem->shouldReceive('put')->once()->with('file.txt', 'file contents')->andReturn(true);

        $file = m::mock('Symfony\Component\HttpFoundation\File\UploadedFile');
        $file->shouldReceive('getClientOriginalName')->andReturn('file.txt');
        $file->shouldReceive('getClientMimeType')->andReturn('text/plain');
        $file->shouldReceive('getClientOriginalExtension')->andReturn('txt');

        $this->utils->shouldReceive('getFileContents')->once()->with($file)->andReturn('file contents');

        $result = $fileManager->upload($file);

        $this->assertTrue($result);
    }

    public function testUploadWithCustomSettings()
    {
        $this->basicExpectations();
        $fileManager = new FileManager($this->filesystem);

        // second true means file exists en _2 will be appended to filename (before extension)
        $this->filesystem->shouldReceive('exists')->times(3)->andReturn(true, true, false);
        $this->filesystem->shouldReceive('put')->once()->with('new/path/file_2.txt', 'file contents')->andReturn(true);

        $file = m::mock('Symfony\Component\HttpFoundation\File\UploadedFile');
        $file->shouldReceive('getClientOriginalName')->andReturn('file.txt');
        $file->shouldReceive('getClientMimeType')->andReturn('text/plain');
        $file->shouldReceive('getClientOriginalExtension')->andReturn('txt');

        $this->utils->shouldReceive('getFileContents')->once()->with($file)->andReturn('file contents');

        $result = $fileManager->upload($file, ['uploadSettings' => ['path' => '/new/path']]);

        $this->assertTrue($result);
    }

    public function testUploadWithDateSettings()
    {
        $this->basicExpectations();
        $fileManager = new FileManager($this->filesystem);

        $this->filesystem->shouldReceive('exists')->times(2)->andReturn(true, false);
        $this->filesystem->shouldReceive('put')->once()->with(date('Y') . '/' . date('m') . '/file.txt',
            'file contents')->andReturn(true);

        $file = m::mock('Symfony\Component\HttpFoundation\File\UploadedFile');
        $file->shouldReceive('getClientOriginalName')->andReturn('file.txt');
        $file->shouldReceive('getClientMimeType')->andReturn('text/plain');
        $file->shouldReceive('getClientOriginalExtension')->andReturn('txt');

        $this->utils->shouldReceive('getFileContents')->once()->with($file)->andReturn('file contents');

        $result = $fileManager->upload($file, ['uploadSettings' => ['date' => 'Y/m']]);

        $this->assertTrue($result);
    }

    public function testUploadWithFilters()
    {
        $this->basicExpectations();
        $fileManager = new FileManager($this->filesystem);
        $this->filesystem->shouldReceive('exists')->twice()->andReturn(true, false);
        $this->filesystem->shouldReceive('put')->once()->with('file.txt', 'file contents')->andReturn(true);

        $file = m::mock('Symfony\Component\HttpFoundation\File\UploadedFile');
        $file->shouldReceive('getClientOriginalName')->andReturn('file.txt');
        $file->shouldReceive('getClientMimeType')->andReturn('text/plain');
        $file->shouldReceive('getClientOriginalExtension')->andReturn('txt');

        $filterHandler = m::mock(FilterHandler::class);
        $filterHandler->shouldReceive('handle')->once()->with($file)->andReturn($file);

        $this->utils->shouldReceive('getFileContents')->once()->with($file)->andReturn('file contents');

        $result = $fileManager->upload($file, null, $filterHandler);

        $this->assertTrue($result);
    }

    public function testGetSettings()
    {
        $this->basicExpectations();

        $fileManager = new FileManager($this->filesystem);

        $this->assertEquals(1, $fileManager->getSettings()->count());
        $this->assertEquals(1, count($fileManager->getSettings('uploadSettings')));
        $this->assertEquals('.', $fileManager->getSettings('uploadSettings', 'path'));
        $this->assertNull($fileManager->getSettings('dummyKey'));
        $this->assertNull($fileManager->getSettings('uploadSettings', 'dummyKey'));
    }

    public function testCustomGetSettings()
    {
        $this->basicExpectations();

        $fileManager = new FileManager($this->filesystem);
        $settings = new Collection(
            [
                'uploadSettings' => ['path' => 'test/path'],
            ]);
        $fileManager->setSettings($settings);

        $this->assertEquals(1, $fileManager->getSettings()->count());
        $this->assertEquals(1, count($fileManager->getSettings('uploadSettings')));
        $this->assertEquals('test/path', $fileManager->getSettings('uploadSettings', 'path'));
        $this->assertNull($fileManager->getSettings('dummyKey'));
        $this->assertNull($fileManager->getSettings('uploadSettings', 'dummyKey'));
    }

    public function testUpdatePublicRoot()
    {
        $this->utils->shouldReceive('publicPath')->twice()->andReturn('/path/to/public');
        $this->filesystem->shouldReceive('getFileSystemRootPath')->twice()->andReturn('/path/to/public/uploads/');
        $fileManager = new FileManager($this->filesystem);

        $this->assertEquals('/uploads/', $fileManager->getPublicRoot());
    }

    public function testIsImageForImageFile()
    {
        $this->basicExpectations();
        $fileManager = new FileManager($this->filesystem);

        $file = m::mock('Samhoud\FileManager\File');
        $file->extension = 'jpg';
        $file->mimetype = 'image/jpeg';

        $isImage = $fileManager->isImage($file);
        $this->assertTrue($isImage);
    }

    public function testIsImageForUploadedFile()
    {
        $this->basicExpectations();
        $fileManager = new FileManager($this->filesystem);
        $file = m::mock('Symfony\Component\HttpFoundation\File\UploadedFile');
        $file->shouldReceive('guessClientExtension')->andReturn('jpg');
        $file->shouldReceive('getClientMimeType')->andReturn('image/jpeg');


        $this->assertTrue($fileManager->isImage($file));
    }

    public function testIsImageForNonImageFile()
    {
        $this->basicExpectations();
        $fileManager = new FileManager($this->filesystem);
        $file = m::mock('Samhoud\FileManager\File');

        $file->extension = 'txt';
        $file->mimetype = 'application/txt';

        $this->assertFalse($fileManager->isImage($file));
    }

    public function testCheckUploadLocationWithExistingPath()
    {
        $this->basicExpectations();
        $fileManager = new FileManager($this->filesystem);

        $this->filesystem->shouldReceive('exists')->with("existing/path")->andReturn(true);
        $result = $fileManager->checkUploadLocation('existing/path');
        $this->assertNull($result);
    }

    public function testCheckUploadLocationWithoutExistingPath()
    {
        $this->basicExpectations();
        $fileManager = new FileManager($this->filesystem);

        $this->filesystem->shouldReceive('exists')->once()->with("non/existing/path")->andReturn(false);
        $this->filesystem->shouldReceive('makeDirectory')->once()->with("non/existing/path");


        $result = $fileManager->checkUploadLocation('non/existing/path');
        $this->assertNull($result);
    }

    public function testDeleteFile()
    {
        $this->basicExpectations();
        $fileManager = new FileManager($this->filesystem);

        $this->filesystem->shouldReceive('delete')->once()->with("path/to/file")->andReturn(true);


        $result = $fileManager->deleteFile('path/to/file');
        $this->assertTrue($result);
    }

    public function testFileExists()
    {
        $this->basicExpectations();
        $fileManager = new FileManager($this->filesystem);

        $this->filesystem->shouldReceive('exists')->once()->with("path/to/file")->andReturn(true);


        $result = $fileManager->fileExists('path/to/file');
        $this->assertTrue($result);
    }

    public function testRead()
    {
        $this->basicExpectations();
        $fileManager = new FileManager($this->filesystem);

        $this->filesystem->shouldReceive('get')->once()->with("path/to/file")->andReturn('file contents');


        $result = $fileManager->read('path/to/file');
        $this->assertEquals('file contents', $result);
    }


    public function testListFiles()
    {
        $this->basicExpectations();
        $fileManager = new FileManager($this->filesystem);
        $fileNames = ['file1.txt', 'file2.txt'];

        $filesData1 =
            [
                'dirname' => 'path/to/files',
                'basename' => 'file1.txt',
                'extension' => 'txt',
                'filename' => 'file1',
                'path' => '2015/12/',
                'fileRoot' => '/uploads/'
            ];
        $filesData2 =
            [
                'dirname' => 'path/to/files',
                'basename' => 'file1.txt',
                'extension' => 'txt',
                'filename' => 'file1',
                'path' => '2015/12/',
                'fileRoot' => '/uploads/'
            ];


        $this->filesystem->shouldReceive('getFileInfo')->once()->with("file1.txt")->andReturn($filesData1);
        $this->filesystem->shouldReceive('getFileInfo')->once()->with("file2.txt")->andReturn($filesData2);
        $this->filesystem->shouldReceive('files')->once()->with("path/to/files")->andReturn(null);
        $this->filesystem->shouldReceive('files')->once()->with("2015")->andReturn(null);
        $this->filesystem->shouldReceive('files')->once()->with("2015/12")->andReturn($fileNames);

        //$this->filesystem->shouldReceive('files')->times(3)->with("path/to/files", '2015', '2015/12')->andReturn(null,null,$fileNames);
        $this->filesystem->shouldReceive('directories')->once()->with("path/to/files")->andReturn(['2015']);
        $this->filesystem->shouldReceive('directories')->once()->with("2015")->andReturn(['2015/12']);
        $this->filesystem->shouldReceive('directories')->once()->with("2015/12")->andReturn(null);

        $result = $fileManager->listFiles("path/to/files");

        $this->assertInstanceOf('Samhoud\FileManager\Directory', $result);
        $this->assertEquals(1, $result->items->count());
        $this->assertEquals(1, $result->items->get(0)->items->count());
        $this->assertEquals(2, $result->items->get(0)->items->get(0)->items->count());
        $this->assertEquals('file1', $result->items->get(0)->items->get(0)->items->get(0)->filename);
    }


    public function testListImages()
    {
        $this->basicExpectations();
        $fileManager = new FileManager($this->filesystem);
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
                'path' => '2015/12/',
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

        $this->assertInstanceOf('Samhoud\FileManager\Directory', $result);
        $this->assertEquals(1, $result->items->count());
        $this->assertEquals(1, $result->items->get(0)->items->count());
        $this->assertEquals(1, $result->items->get(0)->items->get(0)->items->count());

        $this->assertEquals('image', $result->items->get(0)->items->get(0)->items->get(0)->filename);
    }


    public function testFlat()
    {
        $this->basicExpectations();
        $fileManager = new FileManager($this->filesystem);

        $filesData1 =
            [
                'dirname' => 'path/to/files',
                'basename' => 'file1.txt',
                'extension' => 'txt',
                'filename' => 'file1',
                'path' => '2015/11',
                'fileRoot' => '/uploads/'
            ];
        $filesData2 =
            [
                'dirname' => 'path/to/files',
                'basename' => 'file2.txt',
                'extension' => 'txt',
                'filename' => 'file2',
                'path' => '2015/12/',
                'fileRoot' => '/uploads/'
            ];


        $this->filesystem->shouldReceive('getFileInfo')->once()->with("file1.txt")->andReturn($filesData1);
        $this->filesystem->shouldReceive('getFileInfo')->once()->with("file2.txt")->andReturn($filesData2);
        $this->filesystem->shouldReceive('files')->once()->with("path/to/files")->andReturn(null);
        $this->filesystem->shouldReceive('files')->once()->with("2015")->andReturn(null);
        $this->filesystem->shouldReceive('files')->once()->with("2015/12")->andReturn(['file2.txt']);
        $this->filesystem->shouldReceive('files')->once()->with("2015/11")->andReturn(['file1.txt']);

        //$this->filesystem->shouldReceive('files')->times(3)->with("path/to/files", '2015', '2015/12')->andReturn(null,null,$fileNames);
        $this->filesystem->shouldReceive('directories')->once()->with("path/to/files")->andReturn(['2015']);
        $this->filesystem->shouldReceive('directories')->once()->with("2015")->andReturn(['2015/11', '2015/12']);
        $this->filesystem->shouldReceive('directories')->once()->with("2015/11")->andReturn(null);
        $this->filesystem->shouldReceive('directories')->once()->with("2015/12")->andReturn(null);

        $fileList = $fileManager->listFiles("path/to/files");

        $file1 = new File($filesData1);
        $file2 = new File($filesData2);
        $files = new Collection([$file1, $file2]);

        $result = $fileManager->flatten($fileList);
        $this->assertInstanceOf('Illuminate\Support\Collection', $result);
        $this->assertEquals(2, $result->count());
        $this->assertEquals('file1.txt', $result->get(0)->basename);
        $this->assertEquals('file2.txt', $result->get(1)->basename);


    }
}
