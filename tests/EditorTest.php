<?php
use PhpGd\Image;
use PhpGd\Editor;
use PhpGd\Rectangle;

class EditorTest extends PHPUnit_Framework_TestCase {

    // Instance tests
    public function testInstance() {
        
        $editor = new Editor();
        
        $this->assertTrue($editor instanceof Editor);
    }

    // Resize tests
    public function testResizeJpeg() {

        $image = new Image(TEST_DIR.'images/sample-jpeg.jpg');

        $editor = new Editor();
        $editor->edit($image)
            ->resize(400, 225)
            ->save(TEST_DIR.'tmp/sample-jpeg.jpg');

        list($width, $height, $type) = getimagesize( TEST_DIR.'tmp/sample-jpeg.jpg' );

        $this->assertEquals( 400, $width );
        $this->assertEquals( 225, $height );
    }

    public function testResizePng() {

        $image = new Image(TEST_DIR.'images/sample-png.png');

        $editor = new Editor();
        $editor->edit($image)
            ->resize(400, 200)
            ->save(TEST_DIR.'tmp/sample-png.png');

        list($width, $height, $type) = getimagesize( TEST_DIR.'tmp/sample-png.png' );

        $this->assertEquals( 400, $width );
        $this->assertEquals( 200, $height );
    }

    public function testResizeGif() {

        $image = new Image(TEST_DIR.'images/sample-gif.gif');

        $editor = new Editor();
        $editor->edit($image)
            ->resize(400, 225)
            ->save(TEST_DIR.'tmp/sample-gif.gif');

        list($width, $height, $type) = getimagesize( TEST_DIR.'tmp/sample-gif.gif' );

        $this->assertEquals( 400, $width );
        $this->assertEquals( 225, $height );
    }

    // Save tests
    public function testSavePng() {

        $image = new Image(TEST_DIR.'images/sample-png.png');

        $editor = new Editor();
        $editor->edit($image)
            ->save(TEST_DIR.'tmp/testSavePng.png');

        list($width, $height, $type) = getimagesize( TEST_DIR.'tmp/testSavePng.png' );

        $this->assertEquals( IMAGETYPE_PNG, $type );
    }

    public function testSaveGif() {

        $image = new Image(TEST_DIR.'images/sample-gif.gif');

        $editor = new Editor();
        $editor->edit($image)
            ->save(TEST_DIR.'tmp/testSaveGif.gif');

        list($width, $height, $type) = getimagesize( TEST_DIR.'tmp/testSaveGif.gif' );

        $this->assertEquals( IMAGETYPE_GIF, $type );
    }

    // Watermark tests
    public function testWatermark() {

        $image = new Image(TEST_DIR.'images/sample-jpeg.jpg');
        $watermark = new Image(TEST_DIR.'images/watermark.png');

        $editor = new Editor();

        $editor->edit($watermark)
            ->resizeLandscape(200); // Resize watermark width to 200 and height auto

        $watermark = $editor->getImage();

        $editor->edit($image) // Edit main image
            ->addWatermark( $watermark , 'right', 'bottom')
            ->save(TEST_DIR.'tmp/testWatermark.jpg');

        $this->assertEquals( '8e116a61a8de48bd7b9e87f3c7d1be0c', md5_file(TEST_DIR.'tmp/testWatermark.jpg') );
    }

    // Rectangle
    public function  testAddRectangle(){
        $image = Image::createBlank(500, 200); // Blank image

        $rectangle = new Rectangle(200, 100, "#ff0000", "#000000", 5); // A 200x100 red rectangle with a 5-pixel thick black border

        $editor = new Editor();
        $editor->edit($image)
            ->fill("#cccccc") // Change blank image background to white
            ->addRectangle( $rectangle, 10, 10) // Add at position 10, 10
            ->save(TEST_DIR."tmp/bordered.png");

        $this->assertEquals( '4a8a40ec01c5fc2e11167d80a712d5fe', md5_file(TEST_DIR.'tmp/bordered.png') );
    }

    protected function setUp() {

    }

    protected function tearDown() {
        phpGdTestDeleteTmp(); // Delete images created by a test
    }

    public static function setUpBeforeClass() {

    }

    public static function tearDownAfterClass() {

    }
}