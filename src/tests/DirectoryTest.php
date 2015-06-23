<?php
namespace tests;

use Illuminate\Support\Collection;
use Mockery as m;
use Samhoud\FileManager\Directory;

class DirectoryTest extends \PHPUnit_Framework_TestCase
{

    public function tearDown()
    {
        m::close();
    }

    public function testConstructor()
    {
        $items = m::mock('Illuminate\Support\Collection');
        $directory = new Directory('testdir', "path/", $items);
        $this->assertInstanceOf('Samhoud\FileManager\Directory', $directory);
        $this->assertInstanceOf('Illuminate\Support\Collection', $directory->items);
        $this->assertEquals('testdir', $directory->name);
        $this->assertEquals('path/', $directory->path);
    }

    public function testConstructorWithoutItems()
    {
        $directory = new Directory('testdir', "path/");
        $this->assertInstanceOf('Samhoud\FileManager\Directory', $directory);
        $this->assertInstanceOf('Illuminate\Support\Collection', $directory->items);
        $this->assertEquals('testdir', $directory->name);
        $this->assertEquals('path/', $directory->path);
    }

    public function testIsFile()
    {
        $directory = new Directory('', "");
        $this->assertFalse($directory->isFile());
    }

    public function testIsDirectory()
    {
        $directory = new Directory('', "");
        $this->assertTrue($directory->isDirectory());
    }

    public function testAdditem()
    {
        $items = m::mock('Illuminate\Support\Collection');
        $item = m::mock('Samhoud\FileManager\Contracts\FileSystemObject');
        $items->shouldReceive('push')->once()->with($item);
        $directory = new Directory('', "", $items);
        $directory->addItem($item);
    }

    public function testHasDirectories()
    {
        $subdir = new Directory('a', 'b');
        $file = m::mock('Samhoud\FileManager\File');
        $items = new Collection([$subdir, $file]);
        $directory = new Directory('', '', $items);
        $result = $directory->hasDirectories();
        $this->assertEquals(1, $result->count());
    }

    public function testHasNoDirectories()
    {
        $file = m::mock('Samhoud\FileManager\File');
        $items = new Collection([$file]);
        $directory = new Directory('', '', $items);
        $result = $directory->hasDirectories();
        $this->assertEquals(0, $result->count());
    }

    public function testHasFiles()
    {
        $subdir = m::mock('Samhoud\FileManager\Directory');
        $file = m::mock('Samhoud\FileManager\File');
        $items = new Collection([$subdir, $file]);
        $directory = new Directory('', '', $items);
        $result = $directory->hasFiles();
        $this->assertEquals(1, $result->count());
    }

    public function testHasNoFiles()
    {
        $subdir = m::mock('Samhoud\FileManager\Directory');
        $items = new Collection([$subdir]);
        $directory = new Directory('', '', $items);
        $result = $directory->hasFiles();
        $this->assertEquals(0, $result->count());
    }


    public function testFlatten()
    {
        $file1 = m::mock('Samhoud\FileManager\File');
        $file2 = m::mock('Samhoud\FileManager\File');
        $file3 = m::mock('Samhoud\FileManager\File');
        $file4 = m::mock('Samhoud\FileManager\File');

        $subdir1sub = new Directory('c', 'a/b/c', new Collection([$file1, $file2]));
        $subdir1    = new Directory('b', 'a/b', new Collection([$subdir1sub]));
        $subdir2    = new Directory('d', 'a/d', new Collection([$file3]));

        $directory  = new Directory('a', 'a/', new Collection([$file4, $subdir1, $subdir2]));

        $result = $directory->flatten();

        $this->assertEquals($result->count(), 4);
    }
}
