<?php
namespace tests;

use Samhoud\FileManager\File;
use \Mockery as m;

function url() {
    return FileTest::$functions->url();
}


class FileTest extends \PHPUnit_Framework_TestCase
{
    public static $functions;
    private $file;
    public function setUp() {
        self::$functions = m::mock();

        $args = [
            'dirname'   => 'tests/images',
            'basename'  => 'test.jpg',
            'extension' => 'jpg',
            'filename'  => 'test',
            'path'      => '2015/12/',
            'fileRoot'  => '/uploads/'
        ];
        $this->file = new File($args);
    }

    public function tearDown()
    {
        m::close();
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
        $file  = $this->file;
        $result = $file->isFile();
        $this->assertTrue($result);
    }

    public function testIsDirectory()
    {
        $file  = $this->file;
        $result = $file->isDirectory();
        $this->assertFalse($result);
    }

    public function testUrl()
    {
        $this->assertEquals('/uploads/2015/12/', $this->file->fileRoot.$this->file->path);
    }

}
