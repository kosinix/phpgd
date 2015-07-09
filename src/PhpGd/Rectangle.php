<?php
namespace PhpGd;

/**
 * Class Rectangle
 * @package PhpGd
 */
class Rectangle {

    /**
     * Image width in pixels
     * @var int
     */
    protected $width;
    /**
     * Image height in pixels
     * @var int
     */
    protected $height;
    /**
     * @var string
     */
    protected $fillColor;
    /**
     * @var string
     */
    protected $borderColor;
    /**
     * @var int
     */
    protected $borderSize;


    /**
     * Constructor
     * @param null $width
     * @param null $height
     * @param null $fillColor
     * @param null $borderColor
     * @param null $borderSize
     */
    public function __construct( $width = null, $height = null, $fillColor = null, $borderColor = null, $borderSize = null ){
        $this->width = (null===$width) ? 0 : $width;
        $this->height = (null===$height) ? 0 : $height;
        $this->fillColor = (null===$fillColor) ? '#ffffff' : $fillColor;
        $this->borderColor = (null===$borderColor) ? '#000000' : $borderColor;
        $this->borderSize = (null===$borderSize) ? 0 : $borderSize;
    }

    /**
     * @return int
     */
    public function getWidth() {
        return $this->width;
    }

    /**
     * @param int $width
     */
    public function setWidth($width) {
        $this->width = $width;
    }

    /**
     * @return int
     */
    public function getHeight() {
        return $this->height;
    }

    /**
     * @param int $height
     */
    public function setHeight($height) {
        $this->height = $height;
    }

    /**
     * @return string
     */
    public function getFillColor() {
        return $this->fillColor;
    }

    /**
     * @param string $fillColor
     */
    public function setFillColor($fillColor) {
        $this->fillColor = $fillColor;
    }

    /**
     * @return string
     */
    public function getBorderColor() {
        return $this->borderColor;
    }

    /**
     * @param string $borderColor
     */
    public function setBorderColor($borderColor) {
        $this->borderColor = $borderColor;
    }

    /**
     * @return int
     */
    public function getBorderSize() {
        return $this->borderSize;
    }

    /**
     * @param int $borderSize
     */
    public function setBorderSize($borderSize) {
        $this->borderSize = $borderSize;
    }


}