<?php
/**
 * Filters an image.
 * Note: The filter functions are experimental. They are slow on large images. Use with caution.
 * 
 * @author Nico Amarilla
 */
namespace PhpGd;

use PhpGd\Image;

class Filter {
    
    protected $image;
    
    public function __construct(){}
    
    public function edit( ImageInterface $image ){
        $this->image = $image;
        return $this;
    }
    
    /*
	 * Shade will darken an image
	 * 
     * @param float $factor Tint factor 0-1.0 Eg. 0.25, 0.75
     */
    public function shade($factor){
        $w = $this->image->getWidth();
        $h = $this->image->getHeight();
        $image = $this->image->getImageResource();
        for($x = 0; $x < $w; $x++){
            for($y =0; $y < $h; $y++){
                $rgb = imagecolorat($image,$x,$y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;
                
                $newR = ceil($r * (1 - $factor));
                $newG = ceil($g * (1 - $factor));
                $newB = ceil($b * (1 - $factor));
                
                $color = imagecolorallocate($image, $newR, $newG, $newB);
                imagesetpixel($image, $x, $y, $color);
            }
        }
		return $this;
    }
    
    /*
	 * Tint will lighten an image
	 * 
     * @param float $factor Tint factor 0-1.0 Eg. 0.25, 0.75
     */
    public function tint($factor){
        $w = $this->image->getWidth();
        $h = $this->image->getHeight();
        $image = $this->image->getImageResource();
        for($x = 0; $x < $w; $x++){
            for($y =0; $y < $h; $y++){
                $rgb = imagecolorat($image,$x,$y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;
                
                $newR = ceil($r + (255 - $r) * $factor);
                $newG = ceil($g + (255 - $g) * $factor);
                $newB = ceil($b + (255 - $b) * $factor);
                
                $color = imagecolorallocate($image, $newR, $newG, $newB);
                imagesetpixel($image, $x, $y, $color);
            }
        }
		return $this;
    }
    
	/*
	 * Saturate will increase color
	 */
    public function saturate($factor){
        $w = $this->image->getWidth();
        $h = $this->image->getHeight();
        $image = $this->image->getImageResource();
        
        for($y =0; $y < $h; $y++){
            for($x = 0; $x < $w; $x++){
                $rgb = imagecolorat($image,$x,$y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;
                
                list($hue, $sat, $light) = $this->rgbToHsl($r, $g, $b);
                
                list($r2, $g2, $b2) = $this->hslToRgb( $hue, $sat + $factor, $light );
                
                $color = imagecolorallocate($image, $r2, $g2, $b2);
                imagesetpixel($image, $x, $y, $color);
            }
        }
		return $this;
    }
    
    /*
	 * Add points to images
	 * 
     * @param int $diameter Diameter of the point
     * @param int $alpha Transparency of point range[0,127]
     * @parem int $overlap The overlap between pixels
     */
    public function pointillize($diameter = 15, $alpha = 60, $overlap = 5){
        $w = $this->image->getWidth();
        $h = $this->image->getHeight();
        
        $this->image->alphaBlendingMode( true );
        $image = $this->image->getImageResource();
        
        for($y =0; $y < $h; $y++){
            for($x = 0; $x < $w; $x++){
                
                $span = $diameter - $overlap;
                if($y % $span == 0){
                    if($x % $span == 0){
                        $rgb = imagecolorat($image,$x,$y);
                        $r = ($rgb >> 16) & 0xFF;
                        $g = ($rgb >> 8) & 0xFF;
                        $b = $rgb & 0xFF;
                        $color = imagecolorallocatealpha($image, $r, $g, $b, $alpha);
                        imagefilledellipse($image, $x, $y, $diameter, $diameter, $color);
                    }   
                }
                
            }
        }
		return $this;
    }
    
    public function save( $imageFile, $type = null, $quality = null ){
        $this->image->save( $imageFile, $type, $quality );
        return $this;
    }
    
    public function free(){
        $this->image = null;
        return $this;
    }
    
    /*
     * Getters
     */
    public function getImage(){
        return $this->image;
    }
    
    /*
     * Setters
     */
    public function setImage( $value ){
        $this->image = $value ;
    }
    
	/*
	 * Formula http://www.rapidtables.com/convert/color/rgb-to-hsl.htm
	 */
	function rgbToHsl($r,$g,$b){
		
		$r = $r / 255;
		$g = $g / 255;
		$b = $b / 255;
		
		$max = max($r, $g, $b);
		$min = min($r, $g, $b);
		
		$diff = $max - $min;
		
		$l = ($max + $min) / 2;
		if( $diff == 0 ) {
			$h = 0;
			$s = 0;
		} else {
			if($max == $r ){
				$h = 60 * fmod( ($g - $b) / $diff, 6 );
			} else 
			if( $max == $g) {
				$h = 60 * (( ($b - $r) / $diff ) + 2 );
			} else
			if( $max == $b) {
				$h = 60 * (( ($r - $g) / $diff ) + 4 );
			}
			
			$s = $diff / (1 - abs( (2*$l) - 1 ));
			
			if($h < 0 ){
				$h += 359;
			}
		}
		
		return array(round($h), round($s,2), round($l,2));
	}
    
    /*
	* Formula http://en.wikipedia.org/wiki/HSL_and_HSV#From_HSL
	*/
	function hslToRgb( $h, $s, $l ){
		
		// Bounds checks
		$h = ($h>=360) ? 359 : $h;
		$s = ($s>1) ? 1 : $s;
		$l = ($l>1) ? 1 : $l;
		
		$c = ( 1 - abs( (2*$l)-1 ) ) * $s; // chrome
		
		$h1 = $h / 60;
		$x = $c * ( 1 - abs( fmod($h1, 2) - 1 ) );
		
		if($h1 >= 0 and $h1 < 1){
			$r = $c;
			$g = $x;
			$b = 0;
		} else if( $h1 >= 1 and $h1 < 2 ){
			$r = $x;
			$g = $c;
			$b = 0;
		} else if( $h1 >= 2 and $h1 < 3 ){
			$r = 0;
			$g = $c;
			$b = $x;
		} else if( $h1 >= 3 and $h1 < 4 ){
			$r = 0;
			$g = $x;
			$b = $c;
		} else if( $h1 >= 4 and $h1 < 5 ){
			$r = $x;
			$g = 0;
			$b = $c;
		} else if( $h1 >= 5 and $h1 < 6 ){
			$r = $c;
			$g = 0;
			$b = $x;
		}
		
		$m = $l - (0.5 * $c);
		$r += $m;
		$g += $m;
		$b += $m;
		return array(round($r*255), round($g*255), round($b*255));
	}
}