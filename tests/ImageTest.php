<?php
use PhpGd\Image;
use PhpGd\Editor;

class ImageTest extends PHPUnit_Framework_TestCase {
    
    public function testInstance() {
        
        $image = new Image();
        
        $this->assertTrue($image instanceof Image);
    }
    
    public function testBlank() {
        
        $image = Image::createBlank();
        
    }
    
    public function testBlankDimension() {
        
        $image = Image::createBlank();
        
        $this->assertEquals( 1, $image->getWidth());
        $this->assertEquals( 1, $image->getHeight());
    }
    
    public function testJpeg() {
        
        $image = new Image(TEST_DIR.'images/sample-jpeg.jpg');
        $this->assertEquals( 800, $image->getWidth() );
        $this->assertEquals( 450, $image->getHeight() );
        $this->assertEquals( IMAGETYPE_JPEG, $image->getType() );
    }
    
    public function testPng() {
        
        $image = new Image(TEST_DIR.'images/sample-png.png');
        $this->assertEquals( 800, $image->getWidth() );
        $this->assertEquals( 400, $image->getHeight() );
        $this->assertEquals( IMAGETYPE_PNG, $image->getType() );
    }
    
    public function testGif() {
        
        $image = new Image(TEST_DIR.'images/sample-gif.gif');
        $this->assertEquals( 500, $image->getWidth() );
        $this->assertEquals( 281, $image->getHeight() );
        $this->assertEquals( IMAGETYPE_GIF, $image->getType() );
    }
    
}