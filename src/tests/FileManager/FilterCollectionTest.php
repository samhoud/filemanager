<?php


namespace UnitTests\FileManager;

use \Mockery as m;
use Samhoud\FileManager\FilterCollection;
use Samhoud\FileManager\Filters\Contracts\FilterInterface;
use UnitTests\TestCase;

class FilterCollectionTest extends TestCase
{
    public function testConstructor()
    {
        // arrange
        $filterCollection = [
            m::mock(FilterInterface::class),
            m::mock(FilterInterface::class),
        ];

        //act
        $filters = new FilterCollection($filterCollection);

        //assert
        $this->assertInstanceOf(FilterCollection::class, $filters);
        $this->assertCount(2, $filters->all());
    }

    public function testEmptyConstructor()
    {
        //act
        $filters = new FilterCollection([]);

        //assert
        $this->assertInstanceOf(FilterCollection::class, $filters);
        $this->assertCount(0, $filters->all());
    }

    /**
     * @expectedException \Samhoud\FileManager\Exceptions\InvalidFilterException
     */
    public function testConstructorFails()
    {

        $invalidClass = new \stdClass;

        new FilterCollection([
            m::mock(FilterInterface::class),
            $invalidClass
        ]);

    }


    public function testAddFilter()
    {
        // arrange
        $filterCollection = [
            m::mock(FilterInterface::class),
            m::mock(FilterInterface::class),
        ];
        $filters = new FilterCollection($filterCollection);

        //act
        $filters->add(m::mock(FilterInterface::class));

        //assert
        $this->assertCount(3, $filters->all());
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testAddFilterFails()
    {
        $filterCollection = [
            m::mock(FilterInterface::class),
        ];
        $filters = new FilterCollection($filterCollection);

        $filters->add(123);
    }

}
