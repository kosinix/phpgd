<?php
namespace PhpGd;

/**
 * A wrapper class for PHP GD image resource
 * @package PhpGd
 */
class Image implements ImageInterface {

    /**
     * @var string
     */
    protected $imageFile = ''; // Path to file
    /**
     * @var resource
     */
    protected $imageResource = null; // Holds the GD resource identifier
    /**
     * @var int
     */
    protected $width = 0; // Image width in pixels
    /**
     * @var int
     */
    protected $height = 0; // Image height in pixels
    /**
     * @var int
     */
    protected $type = 0; // Values are 0, 1, 2, 3 for unknown, gif, jpeg, and png. Using PHP default constants IMAGETYPE_XXX
    
    /**
     * Create a blank image or load image from file depending on parameters
     * @param string $imageFile Path to image file. Set to blank to create a blank 1x1 image in memory
     */
    public function __construct( $imageFile = '' ){
        if( '' != $imageFile ){
            $this->load( $imageFile );
        }
    }

    /**
     * Load image from file
     * @param string $imageFile
     * @return Image An instance of Image
     */
    public function load( $imageFile ){
        $this->_reset();
        $type = $this->_guessType( $imageFile );
        if( IMAGETYPE_JPEG == $type ){
            
            $this->loadJpeg( $imageFile );
            
        } else if( IMAGETYPE_PNG == $type ){
            
            $this->loadPng( $imageFile );
            
        } else if( IMAGETYPE_GIF == $type ){
            
            $this->loadGif( $imageFile );
            
        }
        return $this;
    }


    /**
     * Creates a blank image
     * @param int $width Width in pixels. Defaults to 1.
     * @param int $height Height in pixels. Defaults to 1.
     * @return Image An instance of Image
     */
    public static function createBlank($width = 1, $height = 1){

        $image = new self();

        $image->setImageResource(imagecreatetruecolor($width, $height));
        $image->setWidth($width);
        $image->setHeight($height);
        $image->setType(IMAGETYPE_UNKNOWN);
        
        return $image;
    }

    /**
     * Create image from string
     * @param string $string String of binary image data
     * @return Image An instance of image
     */
    public static function createFromString( $string ){

        $image = new self();

        $imageResource = imagecreatefromstring( $string );
        $image->setImageResource( $imageResource );
        $image->setWidth( imagesx( $imageResource ) );
        $image->setHeight( imagesy( $imageResource ) );
        $image->setType( IMAGETYPE_UNKNOWN );

        return $image;
    }

    /**
     * @param string $imageFile Path to image
     * @return Image An instance of Image
     */
    public function loadJpeg( $imageFile ){
        $resource = imagecreatefromjpeg( $imageFile );
    
        $this->imageFile = $imageFile;
        $this->imageResource = $resource;
        $this->width = imagesx( $resource );
        $this->height = imagesy( $resource );
        $this->type = IMAGETYPE_JPEG;
        
        return $this;
    }

    /**
     * @param string $imageFile Path to image
     * @return Image An instance of Image
     */
    public function loadPng( $imageFile ){
        $resource = imagecreatefrompng( $imageFile );
        
        $this->imageFile = $imageFile;
        $this->imageResource = $resource;
        $this->width = imagesx( $resource );
        $this->height = imagesy( $resource );
        $this->type = IMAGETYPE_PNG;
        $this->fullAlphaMode( true );
        return $this;
    }

    /**
     * @param string $imageFile Path to image
     * @return Image An instance of Image
     */
    public function loadGif( $imageFile ){
        $resource = imagecreatefromgif( $imageFile );
        
        $this->imageFile = $imageFile;
        $this->imageResource = $resource;
        $this->width = imagesx( $resource );
        $this->height = imagesy( $resource );
        $this->type = IMAGETYPE_GIF;
        
        return $this;
    }

    /**
     * Free GD resource from memory
     */
    public function free() {
        
        if( $this->imageResource ){
            imagedestroy( $this->imageResource ); // Free memory
        }
		
	}

    /**
     * Set the blending mode for an image. Allows transparent overlays on top of an image.
     * @param $flag
     * @return Image An instance of Image
     */
    public function alphaBlendingMode( $flag ){
        imagealphablending( $this->imageResource, $flag );
        
        return $this;
    }

    /**
     * Enable/Disable transparency
     * @param $flag
     * @return Image An instance of Image
     */
    public function fullAlphaMode( $flag ){
        if( true === $flag ){
            $this->alphaBlendingMode( false ); // Must be false for full alpha mode to work
        }
        imagesavealpha( $this->imageResource, $flag );
        
        return $this;
    }

    /**
     * @param $x
     * @param $y
     * @return hex RGB
     */
    public function getColorAt( $x, $y ){
        return imagecolorat ( $this->imageResource, $x, $y );
    }

    /**
     *
     */
    protected function _reset(){
        $this->imageFile = '';
        $this->imageResource = null;
        $this->width = 0;
        $this->height = 0;
        $this->type = '';
    }

    /**
     * @param $imageFile
     * @return int Values from http://us3.php.net/image.constants starting with IMAGETYPE_GIF
     */
    protected function _guessType( $imageFile ){
        list($width, $height, $type) = getimagesize( $imageFile );
        
        return $type;
    }

    /**
     * @return string
     */
    public function getImageFile()
    {
        return $this->imageFile;
    }

    /**
     * @param string $imageFile
     */
    public function setImageFile($imageFile)
    {
        $this->imageFile = $imageFile;
    }

    /**
     * @return resource
     */
    public function getImageResource()
    {
        return $this->imageResource;
    }

    /**
     * @param resource $imageResource
     */
    public function setImageResource($imageResource)
    {
        $this->imageResource = $imageResource;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param int $width
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param int $height
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }


}