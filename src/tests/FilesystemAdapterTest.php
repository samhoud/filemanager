<?php
namespace tests;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use \Mockery as m;
use Samhoud\FileManager\FilesystemAdapter;

class FilesystemAdapterTest extends \PHPUnit_Framework_TestCase
{

    public $utils;
    public $filesystemAdapter;
    public $driver;

    public function tearDown()
    {
        m::close();
    }

    public function setUp(){
        $this->utils                = m::mock('alias:Samhoud\FileManager\Utils');
        $this->driver               = m::mock(FilesystemInterface::class);
        $this->filesystemAdapter    = new FilesystemAdapter($this->driver);
    }

    public function testGetFileInfo()
    {
        $this->driver->shouldReceive('has')->with('test.txt')->andReturn(true);

        // return a dummy array
        $this->driver->shouldReceive('getMetadata')->with('test.txt')->andReturn(['filename' => 'test']);
        $this->driver->shouldReceive('getMimetype')->with('test.txt')->andReturn('text/plain');

        // return a dummy array
        $this->utils->shouldReceive('pathinfo')->once()->andReturn(['path' => 'test']);
        $result = $this->filesystemAdapter->getFileInfo('test.txt');

        $this->assertTrue(is_array($result));
        $this->assertArrayHasKey('path', $result);
        $this->assertArrayHasKey('filename', $result);
        $this->assertArrayHasKey('mimetype', $result);
    }

    public function testGetFileInfoOfNonExistingFile()
    {
        $this->driver->shouldReceive('has')->with('test.txt')->andReturn(false);
        $result = $this->filesystemAdapter->getFileInfo('test.txt');
        $this->assertNull($result);
    }


    public function testGetFileSystemRootPath()
    {
        $adapter = m::mock(Filesystem::class);
        $adapter->shouldReceive('getPathPrefix')->andReturn('uploads/');
        $this->driver->shouldReceive('getAdapter')->andReturn($adapter);
        $result = $this->filesystemAdapter->getFileSystemRootPath();

        $this->assertEquals('uploads/', $result);
    }

    public function testGetFiles()
    {
        $this->driver->shouldReceive('listContents')->with('test', true)->andReturn(['file1', 'file2']);
        $result = $this->filesystemAdapter->listFiles('test');

        $this->assertEquals(['file1', 'file2'], $result);
    }


}
