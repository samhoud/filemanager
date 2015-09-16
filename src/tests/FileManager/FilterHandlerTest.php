<?php


namespace UnitTests\FileManager;

use \Mockery as m;
use Samhoud\FileManager\Contracts\File;
use Samhoud\FileManager\FilterHandler;
use Samhoud\FileManager\Filters\Contracts\FilterInterface;
use UnitTests\TestCase;

class FilterHandlerTest extends TestCase
{
    public function testConstructor(){
        // arrange
        $filters = [
            m::mock(FilterInterface::class),
            m::mock(FilterInterface::class),
        ];

        //act
        $result = new FilterHandler($filters);

        //assert
        $this->assertInstanceOf(FilterHandler::class, $result);
        $this->assertInstanceOf(FilterInterface::class, $result->filters()[0]);
        $this->assertCount(2, $result->filters());
    }


    public function testEmptyHandle(){
        $file   = m::mock(File::class);
        $handler = new FilterHandler();
        $result = $handler->handle($file);

        $this->assertEquals(0, $handler->edits());
        $this->assertEquals($file, $result);
    }
    public function testHandle(){

        $file   = m::mock(File::class);

        $editor1 = m::mock(FilterInterface::class);
        $editor2 = m::mock(FilterInterface::class);
        $editor1->shouldReceive('applyFilter')->once()->with($file)->andReturn($file);
        $editor2->shouldReceive('applyFilter')->once()->with($file)->andReturn($file);


        $filters = [
            $editor1,
            $editor2,
        ];

        //act
        $handler = new FilterHandler($filters);

        $handler->handle($file);

        $this->assertEquals(2, $handler->edits());
    }
}
