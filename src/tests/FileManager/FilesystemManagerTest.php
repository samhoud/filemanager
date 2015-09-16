<?php
namespace UnitTests\FileManager;

use \Mockery as m;
use Samhoud\FileManager\FilesystemManager;
use UnitTests\TestCase;

class FilesystemManagerTest extends TestCase
{


    public function testConstructor()
    {

        $filesystem = m::mock('\League\Flysystem\FilesystemInterface');
        $app = m::mock('\Illuminate\Contracts\Foundation\Application');

        $filesystemManager = new FilesystemManager($app);

        $this->assertInstanceOf('Samhoud\FileManager\FilesystemManager', $filesystemManager);
    }
}
