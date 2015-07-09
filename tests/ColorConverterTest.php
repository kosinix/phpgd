<?php
use PhpGd\ColorConverter;

class ColorConverterTest extends PHPUnit_Framework_TestCase {
    
    public function testHexToRgb() {
        $colorConverter = new ColorConverter();

        list($r, $g, $b) = $colorConverter->hexToRgb('#ffffff'); // White
        
        $this->assertEquals(255, $r);
        $this->assertEquals(255, $g);
        $this->assertEquals(255, $b);

        list($r, $g, $b) = $colorConverter->hexToRgb('#000000'); // Black

        $this->assertEquals(0, $r);
        $this->assertEquals(0, $g);
        $this->assertEquals(0, $b);

        list($r, $g, $b) = $colorConverter->hexToRgb('#ff0000'); // Red

        $this->assertEquals(255, $r);
        $this->assertEquals(0, $g);
        $this->assertEquals(0, $b);
    }
}