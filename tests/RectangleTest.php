<?php
use PhpGd\Rectangle;

class RectangleTest extends PHPUnit_Framework_TestCase {

    public function testInstance() {

        $rectangle = new Rectangle();

        $this->assertTrue($rectangle instanceof Rectangle);
    }

    public function testFullParamInstance() {

        $rectangle = new Rectangle(200, 100, "#ff0000", "#000000", 5);

        $this->assertTrue($rectangle instanceof Rectangle);
    }

    public function testFullParamValues() {

        $rectangle = new Rectangle(200, 100, "#ff0000", "#000000", 5);

        $this->assertEquals(200, $rectangle->getWidth());
        $this->assertEquals(100, $rectangle->getHeight());
        $this->assertEquals("#ff0000", $rectangle->getFillColor());
        $this->assertEquals("#000000", $rectangle->getBorderColor());
        $this->assertEquals(5, $rectangle->getBorderSize());
    }
}