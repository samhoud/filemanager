<?php
namespace tests;
use \Mockery as m;
use Samhoud\FileManager\FilesystemManager;

class FilesystemManagerTest extends \PHPUnit_Framework_TestCase
{

    public function tearDown()
    {
        m::close();
    }


    public function testConstructor()
    {

        $filesystem = m::mock('\League\Flysystem\FilesystemInterface');
        $app        = m::mock('\Illuminate\Contracts\Foundation\Application');

        $filesystemManager   = new FilesystemManager($app);

        $this->assertInstanceOf('Samhoud\FileManager\FilesystemManager',        $filesystemManager);
    }
}
