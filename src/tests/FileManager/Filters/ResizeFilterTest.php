<?php


namespace UnitTests\FileManager\Filters;


use Intervention\Image\Image;
use Samhoud\FileManager\Filters\ResizeFilter;
use UnitTests\TestCase;
use \Mockery as m;

/**
 * Class ResizeFilterTest
 * @package UnitTests\FileManager\Filters
 */
class ResizeFilterTest extends TestCase
{
    /**
     * @var Image
     */
    public $kittenImage;

    /**
     * @var Image
     */
    public $kittenImagePortrait;


    /**
     * @var string
     */
    public $kittenPath;

    /**
     * @var string
     */
    public $kittenPortraitPath;
    /**
     * @var string
     */
    public $kittenPathTmp;

    /**
     * @var string
     */
    public $kittenPortraitPathTmp;

    /**
     * @var string
     */
    public $resultsDir;



    /**
     * @var \Intervention\Image\ImageManager
     */
    public $imageManager;


    public $saveResult = false;
    /**
     *
     */
    public function setUp(){
        $this->kittenPath               = __DIR__.'/images/kitten.jpg';
        $this->kittenPathTmp            = __DIR__.'/images/kitten_tmp.jpg';
        $this->kittenPortraitPath       = __DIR__.'/images/kitten_portrait.png';
        $this->kittenPortraitPathTmp    = __DIR__.'/images/kitten_portrait_tmp.png';
        $this->resultsDir               = __DIR__.'/images/results/';
        $this->imageManager             = new  \Intervention\Image\ImageManager();
        parent::setUp();
        $this->tmpImage();
    }

    /**
     *
     */
    public function tmpImage(){
        @copy($this->kittenPath,$this->kittenPathTmp);
        @copy($this->kittenPortraitPath,$this->kittenPortraitPathTmp);

        $this->kittenImage = $this->imageManager->make($this->kittenPathTmp);
        $this->kittenImagePortrait = $this->imageManager->make($this->kittenPortraitPathTmp);
    }

    /**
     *
     */
    public function clean(){
        @unlink($this->kittenPathTmp);
        @unlink($this->kittenPortraitPathTmp);
    }

    /**
     *
     */
    public function tearDown(){
        parent::tearDown();
       $this->clean();
    }

    /**
     *
     */
    public function testConstructor()
    {
        $resizer = new ResizeFilter(100, 200);
        $this->assertInstanceOf(ResizeFilter::class, $resizer);
        $this->assertEquals(100, $resizer->width);
        $this->assertEquals(200, $resizer->height);
        $this->assertTrue($resizer->constrainAspectRatio);
        $this->assertTrue($resizer->preventUpsizing);

        $resizer = new ResizeFilter(300, 500, false, false);
        $this->assertInstanceOf(ResizeFilter::class, $resizer);
        $this->assertEquals(300, $resizer->width);
        $this->assertEquals(500, $resizer->height);
        $this->assertFalse($resizer->constrainAspectRatio);
        $this->assertFalse($resizer->preventUpsizing);
    }

    /**
     *
     */
    public function testApplyFilterWithConstraints()
    {
        $image = m::mock(Image::class);
        $image->shouldReceive('resize')->once()->withArgs(
            [
                100,
                200,
                m::on(function ($constraint){
                  return is_callable($constraint);
                })
            ]
        )->andReturnSelf();
        $resizer = new ResizeFilter(100, 200);

        $resizedImage = $resizer->applyFilter($image);

        $this->assertEquals($resizedImage, $image);
    }

    /**
     *
     */
    public function testApplyFilterWithoutConstraints(){

        $image = m::mock(Image::class);
        $image->shouldReceive('resize')->once()->withArgs(
            [100, 200, null]
        )->andReturnSelf();
        $resizer = new ResizeFilter(100, 200, false, false);

        $resizedImage = $resizer->applyFilter($image);

        $this->assertEquals($resizedImage, $image);
    }

    /**
     *
     */
    public function testFilterWithKittenNoRatioNoUpsizePrevention()
    {
        $image = $this->kittenImage;
        $settings = [100, 200, false, false];

        $this->resize($image, $settings);
    }

    public function testFilterWithKittenWithRatioNoUpsizePrevention(){
        $image = $this->kittenImage;
        $settings = [600, 1200, true, false];
        $this->resize($image, $settings, null, 397);
    }

    public function testFilterWithKittenWithRatioWithUpsizePrevention(){
        $image = $this->kittenImage;

        $settings = [600, 1200];
        $this->resize($image, $settings);
    }

    public function testFilterWithKittenWidthOnlyAndConstraints(){
        $image = $this->kittenImage;

        $settings = [213];
        $this->resize($image, $settings, null, 141);
    }

    public function testFilterWithKittenHeightOnlyAndConstraints(){
        $image = $this->kittenImage;

        $settings = [null, 1200];
        $this->resize($image, $settings);
    }

    /**
     *
     */
    public function testFilterWithPortraitKittenNoRatioNoUpsizePrevention(){
        $image      = $this->kittenImagePortrait;
        $settings   = [100, 200, false, false];
        $this->resize($image, $settings);
    }

    public function testFilterWithPortraitKittenWithRatioNoUpsizePrevention(){
        $image      = $this->kittenImagePortrait;
        $settings = [600, 1200, true, false];
        $this->resize($image, $settings, null, 985);
    }

    public function testFilterWithPortraitKittenWithRatioWithUpsizePrevention(){
        $image      = $this->kittenImagePortrait;
        $settings = [600, 1200];
        $this->resize($image, $settings);
    }

    public function testFilterWithPortraitKittenWidthOnlyAndConstraints(){
        $image = $this->kittenImagePortrait;

        $settings = [138];
        $this->resize($image, $settings, null, 227);
    }

    public function testFilterWithPortraitKittenHeightOnlyAndConstraints(){
        $image = $this->kittenImagePortrait;

        $settings = [null, 1200];
        $this->resize($image, $settings);
    }

    private function resize($image, $settings, $expectedWidth = null, $expectedHeight = null){
        $resizer = new ResizeFilter(
            (array_key_exists(0, $settings) ? $settings[0] : null),
            (array_key_exists(1, $settings) ? $settings[1] : null),
            (array_key_exists(2, $settings) ? $settings[2] : true),
            (array_key_exists(3, $settings) ? $settings[3] : true)
        );
        $resizedImage = $resizer->applyFilter($image);

        if($this->saveResult) {
            $newName = $this->resultsDir . time() . "_x" . $resizer->width . '-' . $resizedImage->getWidth() . '_y' . $resizer->height . '-' . $resizedImage->getHeight() . '.png';
            $resizedImage->save($newName);
        }
        $this->assertEquals(
            ($expectedWidth ? $expectedWidth : $image->getWidth()),
            $resizedImage->getWidth());

        $this->assertEquals(($expectedHeight ? $expectedHeight: $image->getHeight()),
            $resizedImage->getHeight());
    }

}

