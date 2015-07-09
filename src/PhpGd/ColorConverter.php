<?php
namespace PhpGd;

/**
 * Class ColorConverter contains functions to convert colors into different formats
 * @package PhpGd
 */
class ColorConverter {

    /**
     * Convert hex string to RGB
     * @param string $hex Hex string. Possible values: #ffffff, #fff, fff
     * @return array Contains (RGB) values red, green and blue
     */
    public function hexToRgb( $hex ) {
        $hex = ltrim($hex, '#'); // Remove #

        if(strlen($hex) == 3) {
            $r = hexdec(substr($hex,0,1).substr($hex,0,1));
            $g = hexdec(substr($hex,1,1).substr($hex,1,1));
            $b = hexdec(substr($hex,2,1).substr($hex,2,1));
        } else {
            $r = hexdec(substr($hex,0,2));
            $g = hexdec(substr($hex,2,2));
            $b = hexdec(substr($hex,4,2));
        }
        return array($r, $g, $b); // Returns an array with the rgb values
    }

    /**
     * Converts RGB to HSL. Formula src: http://www.rapidtables.com/convert/color/rgb-to-hsl.htm
     * @param int $r The red value
     * @param int $g The green value
     * @param int $b The blue value
     * @return array Array containing HSL values
     */
    public function rgbToHsl( $r, $g, $b ){

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


    /**
     * @param int $r
     * @param int $g
     * @param int $b
     * @return array
     */
    public function rgbToHsb($r,$g,$b){

        $min = min($r, $g, $b);
        $max = max($r, $g, $b);

        if($max - $min == 0) {
            $h = 0;
        } else {
            if($max == $r ){
                $h = fmod( 60 * (($g - $b) / ($max - $min)), 360);
            } else if( $max == $g) {
                $h = ( 60 * (($b - $r) / ($max - $min)) ) + 120;
            } else if( $max == $b) {
                $h = ( 60 * (($r - $g) / ($max - $min)) ) + 240;
            }

        }

        $b = $max;
        if($b == 0) {
            $s = 0;
        } else {
            $s = ($max - $min) / $b;
        }

        $s = round($s, 2); // Rounded to 2 decimal places
        $b = round($b/255, 2); // Turn it to range 0,1 rounded to 2 decimal places
        return array($h, $s, $b);
    }

    /**
     * HSLtoRGB Formula src: http://en.wikipedia.org/wiki/HSL_and_HSV#From_HSL
     * @param int $h
     * @param int $s
     * @param int $l
     * @return array
     */
    public function hslToRgb( $h, $s, $l ){

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