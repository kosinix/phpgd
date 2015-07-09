<?php
namespace PhpGd;

use PhpGd\Image;
use PhpGd\Rectangle;
use PhpGd\ColorConverter;

/**
 * Class Editor
 * @package PhpGd
 */
class Editor {

    /**
     * @var ImageInterface Instance implementing ImageInterface
     */
    protected $image;

    /**
     * @var ColorConverter
     */
    protected $colorConverter;

    /**
     * Constructor
     */
    public function __construct(){
        $this->image = null;
        $this->colorConverter = new ColorConverter();
    }

    /**
     * Edit an image
     * @param ImageInterface $image
     * @return Editor Instance of Editor
     */
    public function edit( ImageInterface $image ){
        $this->image = $image;
        return $this;
    }


    /**
     * TODO: Add backup
     * Backs up current image state
     * @return Editor Instance of Editor
     * @throws \Exception Throws exception if there is no image resource
     */
    private function backup(){
        if( null === $this->image->getImageResource() or 'gd' != get_resource_type($this->image->getImageResource())){
            throw new \Exception('Could not backup an uninitialized image.');
        }

        switch ( $this->image->getType() ){
            case IMAGETYPE_JPEG:
            case IMAGETYPE_PNG:
            case IMAGETYPE_GIF:
                $this->backup = new Image( $this->image->getImageFile() );
                break;
            default:
                $this->backup = Image::createBlank($this->image->getWidth(), $this->image->getHeight()); // Create blank with width & height of orig image
        }

        return $this;
    }

    /**
     * TODO: see backup
     * Revert image to backup
     * @return Editor Instance of Editor
     * @throws \Exception Throws exception if there is no backup
     */
    private function revert(){
        if( null === $this->backup){
            throw new \Exception('No backup.');
        }
        $this->image = $this->backup;
        return $this;
    }

    /**
     * Resize to given width with auto height
     * @param int $newWidth
     * @return Editor Instance of Editor
     */
    public function resizeLandscape( $newWidth ){
        $ratio = $this->image->getHeight() / $this->image->getWidth();

        $optimalWidth = $newWidth;
        $optimalHeight = $newWidth * $ratio;

        return $this->resize($optimalWidth, $optimalHeight);
    }

    /**
     * Resize to given height with auto width
     * @param int $newHeight
     * @return Editor Instance of Editor
     */
    public function resizePortrait( $newHeight ){
        $ratio = $this->image->getWidth() / $this->image->getHeight();

        $optimalWidth = $newHeight * $ratio;
        $optimalHeight = $newHeight;

        return $this->resize($optimalWidth, $optimalHeight);
    }


    /**
     * Resize to width and height
     * @param $newWidth
     * @param $newHeight
     * @return Editor Instance of Editor
     */
    public function resize( $newWidth, $newHeight ){

        // Re-sample - create image canvas of x, y size
		$newImage = Image::createBlank($newWidth , $newHeight);

        if( IMAGETYPE_PNG === $this->image->getType() ){
            // Preserve PNG transparency
            $newImage->fullAlphaMode( true );
        }

		imagecopyresampled($newImage->getImageResource(), $this->image->getImageResource(), 0, 0, 0, 0, $newWidth, $newHeight, $this->image->getWidth(), $this->image->getHeight() );

		// Free memory of old resource
		imagedestroy( $this->image->getImageResource() );

		// Assign new resource
		$this->image->setImageResource( $newImage->getImageResource() );
        $this->image->setWidth( $newWidth );
        $this->image->setHeight( $newHeight );
        return $this;
    }


    /**
     * Crop image from center
     * @param $newWidth
     * @param $newHeight
     * @param null $crop_start_x
     * @param null $crop_start_y
     * @return Editor Instance of Editor
     */
    public function crop($newWidth, $newHeight, $crop_start_x = null, $crop_start_y = null ) {

        if( null === $crop_start_x ){
            $crop_start_x = ( $this->image->getWidth() / 2) - ( $newWidth /2 ); // Center the crop rectangle in the x axis
        }
		if( null === $crop_start_y ){
            $crop_start_y = ( $this->image->getHeight() / 2) - ( $newHeight/2 ); // Center the crop rectangle in the y axis
        }

		// Now crop from center to exact requested size
		$newImage = Image::createBlank($newWidth , $newHeight);

        // Preserve PNG transparency
        $newImage->fullAlphaMode( true );

		imagecopyresampled($newImage->getImageResource(), $this->image->getImageResource(), $dst_x=0, $dst_y=0, $crop_start_x, $crop_start_y, $newWidth, $newHeight , $src_w=$newWidth, $src_h=$newHeight);

		// Free memory
		imagedestroy( $this->image->getImageResource() );

		// Assign new resource
		$this->image->setImageResource( $newImage->getImageResource() );
        $this->image->setWidth( $newWidth );
        $this->image->setHeight( $newHeight );
        return $this;
	}

    /**
     * Fill entire image with color
     * @param string $color Color in hex format: #fffff
     * @param int $x X-coordinate of start point
     * @param int $y Y-coordinate of start point
     * @param int $alpha Alpha 0-127 where 0 is opaque and 127 is transparent
     * @return Editor Instance of Editor
     */
    public function fill( $color, $x = 0, $y = 0, $alpha = 0 ){
        list($r, $g, $b) = $this->colorConverter->hexToRgb($color);

        $colorResource = imagecolorallocatealpha($this->image->getImageResource(), $r, $g, $b, $alpha);
        imagefill( $this->image->getImageResource(), $x, $y, $colorResource );
        return $this;
    }

    /**
     * Add a rectangle
     * @param Rectangle $rectangle
     * @param int $x Optional. Position on the X axis.
     * @param int $y Optional. Position on the Y axis.
     * @return Editor Instance of Editor
     */
    public function addRectangle( Rectangle $rectangle, $x = 0, $y = 0 ){
        $alpha = 0; // TODO: Make this editable in the future
        $width = $rectangle->getWidth();
        $height = $rectangle->getHeight();
        $borderSize = $rectangle->getBorderSize();

        list($r, $g, $b) = $this->colorConverter->hexToRgb( $rectangle->getFillColor() );
        $fillColorResource = imagecolorallocatealpha($this->image->getImageResource(), $r, $g, $b, $alpha);



        $x1 = $x;
        $x2 = $x + $width;
        $y1 = $y;
        $y2 = $y + $height;

        if($borderSize == 0){
            imagefilledrectangle($this->image->getImageResource(), $x1, $y1, $x2, $y2, $fillColorResource);
        } else {
            list($r, $g, $b) = $this->colorConverter->hexToRgb( $rectangle->getBorderColor() );
            $borderColorResource = imagecolorallocatealpha($this->image->getImageResource(), $r, $g, $b, $alpha);

            imagefilledrectangle($this->image->getImageResource(), $x1, $y1, $x2, $y2, $borderColorResource);
            imagefilledrectangle($this->image->getImageResource(), $x1 + $borderSize, $y1 + $borderSize, $x2 - $borderSize, $y2 - $borderSize, $fillColorResource);
        }
        return $this;
    }

    /**
     * Add text to image
     * @param $text
     * @param int $size
     * @param int $x
     * @param int $y
     * @param string $color
     * @param int $alpha
     * @return Editor Instance of Editor
     */
    public function text( $text, $size = 4, $x = 0, $y = 0, $color = '#000000', $alpha = 0 ){
        list($r, $g, $b) = $this->colorConverter->hexToRgb($color);

        $colorResource = imagecolorallocatealpha($this->image->getImageResource(), $r, $g, $b, $alpha);
        imagestring($this->image->getImageResource(), $size, $x, $y, $text, $colorResource);
        return $this;
    }

    /**
     * Invert colors
     * @return Editor Instance of Editor
     */
    public function invert(){
        imagefilter($this->image->getImageResource(), IMG_FILTER_NEGATE);
        return $this;
    }

    /**
     * Convert colors to grayscale
     * @return Editor Instance of Editor
     */
    public function grayscale(){
        imagefilter($this->image->getImageResource(), IMG_FILTER_GRAYSCALE);
        return $this;
    }

    /**
     * Adjust image brightness
     * @param int $brightness Brightness factor -255 to 255 where 0 is no change
     * @return Editor Instance of Editor
     */
    public function brightness( $brightness ){
        imagefilter($this->image->getImageResource(), IMG_FILTER_BRIGHTNESS, $brightness);
        return $this;
    }

    /**
     * Adjust image contrast
     * @param int $contrast Contrast factor -255 to 255 where 0 is no change
     * @return Editor Instance of Editor
     */
    public function contrast( $contrast ){
        imagefilter($this->image->getImageResource(), IMG_FILTER_CONTRAST, $contrast);
        return $this;
    }

    /**
     * Add watermark on image
     * @param ImageInterface $watermark
     * @param string|int $xPos Horizontal position of image. Can be 'left','center','right' or integer number. Defaults to 'center'.
     * @param string|int $yPos Vertical position of image. Can be 'top', 'center','bottom' or integer number. Defaults to 'middle'.
     * @return Editor Instance of Editor
     */
    public function addWatermark( ImageInterface $watermark, $xPos='center', $yPos='center'){

        $x=0;
        $y=0;

        if(is_string($xPos)){
            // Compute position from string
            switch ($xPos){
                case 'left':
                    $x = 0;
                    break;

                case 'right':
                    $x = $this->image->getWidth() - $watermark->getWidth();
                    break;

                case 'center':
                default:
                    $x = (int) round( ($this->image->getWidth()/2) - ($watermark->getWidth()/2) );
                    break;
            }
        } else {
            $x = $xPos;
        }

        if(is_string($yPos)){
            switch ($yPos){
                case 'top':
                    $y = 0;
                    break;

                case 'bottom':
                    $y = $this->image->getHeight() - $watermark->getHeight();
                    break;

                case 'center':
                default:
                    $y = (int) round( ($this->image->getHeight()/2) - ($watermark->getHeight()/2) );
                    break;
            }
        } else {
            $y = $yPos;
        }

		imagecopyresampled(
            $this->image->getImageResource(), // Image to apply watermark
            $watermark->getImageResource(), // Watermark
            (int)$x, // Watermark x position
            (int)$y, // Watermark y position
            0,
            0,
            $watermark->getWidth(), // Watermark final width
            $watermark->getHeight(), // Watermark final height
            $watermark->getWidth(), // Watermark source width
            $watermark->getHeight() // Watermark source height
        );
		return $this;

    }

    /**
     * Save image to file
     * @param string $imageFile File name of image.
     * @param int|null $quality Optional. PNG quality from 0 to 9 where 0 is best quality. If JPEG quality is 0-100 where 100 is best
     * @param int|null $type Optional. Force save an image to this format. 1,2,3 for GIF,JPEG,PNG or use PHP constants IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG. If its null, will use image->type
	 * @param bool $interlace Optional. If true, a GIF is saved as interlaced, JPEG as progressive. In PNG this is ignored.
     * @return Editor Instance of Editor
	 */
    public function save( $imageFile, $quality = null, $type = null, $interlace = false ){

        if ( null === $type ){

            $type = $this->getImageTypeFromFileName( $imageFile ); // Null given, guess type from file extension
            if( IMAGETYPE_UNKNOWN === $type ) {
                $type = $this->image->getType(); // 0 result, use original image type
            }
        }

        switch ( $type ){
            case IMAGETYPE_GIF :
                imagegif($this->image->getImageResource(), $imageFile);
                break;

            case IMAGETYPE_PNG :
                $quality = ($quality===null) ? 0 : $quality;
                $quality = ($quality>9) ? 9 : $quality;
                $quality = ($quality<0) ? 0 : $quality;
                imagepng($this->image->getImageResource(), $imageFile, $quality);
                break;

            default: // Defaults to jpeg
                $quality = ($quality===null) ? 100 : $quality;
                $quality = ($quality>100) ? 100 : $quality;
                $quality = ($quality<0) ? 0 : $quality;
                imageinterlace($this->image->getImageResource(), $interlace);
                imagejpeg($this->image->getImageResource(), $imageFile, $quality);
        }
        return $this;
    }


    /**
     * Save image to original location overwriting the original image
     * @param null $quality
     * @param null $type
     * @return Editor Instance of Editor
     */
    public function saveToOriginal( $quality = null, $type = null ){
        $this->image->save( $this->image->getImageFile(), $type, $quality );
        return $this;
    }

    /**
     * Guess file type from file extension
     * Helps prevent problems opening an image in Photoshop and other image editors due to file format and file extension mismatch
     * @param string Filename or path to file
     * @return int Any of the IMAGETYPE_* constants
     */
    public function getImageTypeFromFileName( $imageFile ){
        $ext = strtolower((string)pathinfo($imageFile, PATHINFO_EXTENSION));

        if( 'jpg' == $ext or 'jpeg' == $ext ){
            return IMAGETYPE_JPEG;
        } else if( 'gif' == $ext ){
            return IMAGETYPE_GIF;
        } else if( 'png' == $ext ){
            return IMAGETYPE_PNG;
        } else {
            return IMAGETYPE_UNKNOWN;
        }
    }

    /**
     * Free memory
     * @return Editor Instance of Editor
     */
    public function free(){
        $this->image = null;
        return $this;
    }

    /**
     * @return ImageInterface
     */
    public function getImage() {
        return $this->image;
    }

    /**
     * @param ImageInterface $image
     */
    public function setImage($image) {
        $this->image = $image;
    }

    /**
     * @return ColorConverter
     */
    public function getColorConverter() {
        return $this->colorConverter;
    }

    /**
     * @param ColorConverter $colorConverter
     */
    public function setColorConverter($colorConverter) {
        $this->colorConverter = $colorConverter;
    }


}