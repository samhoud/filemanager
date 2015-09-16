<?php
namespace UnitTests\FileManager;

use Samhoud\FileManager\File;
use \Mockery as m;
use UnitTests\TestCase;

class FileTest extends TestCase
{
    private $file;

    public function setUp()
    {

        $args = [
            'dirname' => 'tests/images',
            'basename' => 'test.jpg',
            'extension' => 'jpg',
            'filename' => 'test',
            'path' => '2015/12/',
            'fileRoot' => '/uploads/'
        ];
        $this->file = new File($args);
    }


    public function testSetFileInfoFromPath()
    {
        $file = $this->file;

        $this->assertEquals('tests/images', $file->dirname);
        $this->assertEquals('test.jpg', $file->basename);
        $this->assertEquals('jpg', $file->extension);
        $this->assertEquals('test', $file->filename);
    }

    public function testIsFile()
    {
        $file = $this->file;
        $result = $file->isFile();
        $this->assertTrue($result);
    }

    public function testIsDirectory()
    {
        $file = $this->file;
        $result = $file->isDirectory();
        $this->assertFalse($result);
    }

    public function testUrl()
    {
        $utils = m::mock('alias:Samhoud\FileManager\Utils');
        $utils->shouldReceive('makeUrl')->with('/uploads/2015/12/')->andReturn('http:///site.com/uploads/2015/12/');
        $result = $this->file->url();
        $this->assertEquals('http:///site.com/uploads/2015/12/', $result);
    }

}
