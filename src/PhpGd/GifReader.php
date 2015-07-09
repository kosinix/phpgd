<?php
/**
 * GIF Reader
 *
 * Ref - http://giflib.sourceforge.net/whatsinagif/bits_and_bytes.html
 *
 * @author Nico Amarilla
 */
namespace PhpGd;

class GifReader {
    
    protected $imageFile = ''; // Path to file
    protected $bytes = '';
    const LENGTH_HEADER = 12;
    const LENGTH_LOGICAL_SCREEN_DESCRIPTOR = 14;
    const LENGTH_GRAPHIC_CONTROL_EXT = 16;
    const LENGTH_IMAGE_DESCRIPTOR = 20;
    const LENGTH_APPLICATION_EXT = 38;
    
    /*
     * Create a blank image or load image from file depending on parameters
     *
     * @param string $imageFile Path to image file
     */
    public function __construct( $imageFile = '' ){
        if( '' != $imageFile ){
            $this->load( $imageFile );
        }
    }
    
    /*
     * Load image from file
     *
     * @param string $imageFile Path to image file
     */
    public function load( $imageFile ){
        $fp = fopen( $imageFile, 'rb'); // Binary read
        
        if($fp === false ) throw new \Exception('Error loading file.');
        
        $this->size = filesize( $imageFile );
        $this->content = fread($fp, $this->size);
        $data = unpack('H*all', $this->content); // Unpack as hex
        $this->bytes = $data['all'];
        fclose($fp);
        
        
        if($this->getSignature() !== 'GIF'){
            throw new \Exception('Invalid GIF');
        }
        if($this->getLogicalScreenDescriptorBlock() == ''){
            throw new \Exception('Missing logical screen descriptor block.');
        }
        if($this->getTrailerBlock() !== '3b'){
            throw new \Exception('Missing trailer block');
        }
    }
    
    /*
     * @return string Bytes as hex strings
     */
    public function getBytes(){
        return $this->bytes;   
    }
    
    public function explain(){
        $pos = 0;
        $blocks['header']['raw'] = substr($this->bytes, $pos, self::LENGTH_HEADER);
        $blocks['header']['signature'] = substr($this->bytes, $pos, self::LENGTH_HEADER);
        $pos = self::LENGTH_HEADER;
        
        $blocks['logicalScreenDescriptor'] = substr($this->bytes, $pos, self::LENGTH_LOGICAL_SCREEN_DESCRIPTOR );
    }
    /*
     * @return string Bytes as hex strings
     */
    public function getHeaderBlock(){
        
        return substr($this->bytes, 0, self::LENGTH_HEADER);
    }
    
    /*
     * @return string Bytes as hex strings
     */
    public function getLogicalScreenDescriptorBlock(){
        $startPos = self::LENGTH_HEADER;
        
        return substr($this->bytes, $startPos, self::LENGTH_LOGICAL_SCREEN_DESCRIPTOR );
    }
    
    /*
     * @return string Bytes as hex strings
     */
    public function getGlobalColorTableBlock(){
        
        $startPos = self::LENGTH_HEADER + self::LENGTH_LOGICAL_SCREEN_DESCRIPTOR;
        
        $endPos = strpos($this->bytes, '21ff'); // Check application extension block exist
        
        if($endPos === false){
            $endPos = strpos($this->bytes, '21f9'); // use the first graphics control extension block as end position
        }
        
        if($endPos === false){
            return '';
        }
        return substr($this->bytes, $startPos, $endPos - $startPos );
    }
    
    /*
     * @return string Bytes as hex strings
     */
    public function getApplicationExtensionBlock(){
        $startPos = strpos($this->bytes, '21ff');
        
        $endPos = $startPos + self::LENGTH_APPLICATION_EXT;
        
        if($startPos === false){
            return '';
        }
        
        return substr($this->bytes, $startPos, $endPos - $startPos );
    }
    
    /*
     * @param string $frame One of the frame returned by getFrames
     * @return string Bytes as hex strings
     */
    public function getGraphicsControlExtensionBlock( $frame ){
        $startPos = strpos($frame, '21f9');
        
        $endPos = strpos($frame, '2c', $startPos);
        
        if($startPos === false){
            return '';
        }
        
        return substr($frame, $startPos, $endPos - $startPos );
    }
    
    /*
     * @param string $frame One of the frame returned by getFrames
     * @return string Bytes as hex strings
     */
    public function getImageDescriptorBlock( $frame ){
        $startPos = strpos($frame, '2c');
        
        if($startPos === false){
            return '';
        }
        
        return substr($frame, $startPos, self::LENGTH_IMAGE_DESCRIPTOR);
    }
    
    /*
     * @param string $frame One of the frame returned by getFrames
     * @return string Bytes as hex strings
     */
    public function getImageDataBlock( $frame ){
        $startPos = strpos($frame, '2c');
        
        if($startPos === false){
            return '';
        }
        $startPos += self::LENGTH_IMAGE_DESCRIPTOR;
        return substr($frame, $startPos);
    }
    
    /*
     * @return int Number of GIF frames based on graphics control extension
     */
    public function countFrames(){
        
        $frames = $this->getFramePositions();
        return count($frames);
    }
    
    /*
     * @return array Array of int positions
     */
    public function getFramePositions(){
        
        $frames = array();
        
        $pos = strpos($this->bytes, '21f9', 0); // 21f9 is the graphic control identifier
        
        while($pos!==false){ // if $pos === false then do not enter loop
            $frames[] = $pos;
            
            $pos = strpos($this->bytes, '21f9', $pos+1);
            
        }
        return $frames;
    }
    
    /*
     * @return array Array of string frames
     */
    public function getFrames(){
        $frames = array();
        $positions = $this->getFramePositions();
        foreach($positions as $count=>$position){
            if(isset($positions[$count+1])) {
                $frames[] = substr($this->bytes, $position, $positions[$count+1]-$position);
            }
        }
        if(isset($position)){
            $end = strpos($this->bytes, '3b', $position); // EOF
            if( $end !== false ) {
                $frames[] = substr($this->bytes, $position, $end-$position);
            }
        }
        return $frames;
    }
    
    /*
     * @return string Bytes as hex strings
     */
    public function getTrailerBlock(){
        
        return substr($this->bytes, -2);
    }
    
    public function getCanvasWidth(){
        $block = $this->getLogicalScreenDescriptorBlock();
        $part = substr($block, 0, 4);
        $bytes = str_split($part, 2);
        return hexdec($bytes[1].$bytes[0]);
    }
    
    public function getCanvasHeight(){
        $block = $this->getLogicalScreenDescriptorBlock();
        $part = substr($block, 4, 4);
        $bytes = str_split($part, 2);
        return hexdec($bytes[1].$bytes[0]);
    }
    
    /*
     * @return string Binary string
     */
    public function expandPackedField( $packedField ){
        return base_convert($packedField, 16, 2);
    }
    
    /*
     * @return string Hex string
     */
    public function getLogicalScreenPackedField(){
        $block = $this->getLogicalScreenDescriptorBlock();
        return substr($block, 8, 2);
    }
    
    /*
     * @return bool True or false
     */
    public function hasGlobalColorTable(){
        $packedField = $this->getLogicalScreenPackedField();
        $expandedField = $this->expandPackedField( $packedField );
        return (boolean)substr($expandedField, 0, 1);
    }
    
    /*
     * @return int Decimal value of binary string
     */
    public function getColorResolution(){
        $packedField = $this->getLogicalScreenPackedField();
        $expandedField = $this->expandPackedField( $packedField );
        $bits = substr($expandedField, 1, 3);
        return bindec($bits);
    }
    
    /*
     * @return int Decimal value of binary string. Can be 0 or 1.
     */
    public function getSortFlag(){
        $packedField = $this->getLogicalScreenPackedField();
        $expandedField = $this->expandPackedField( $packedField );
        $bits = substr($expandedField, 4, 1);
        return bindec($bits);
    }
    
    /*
     * @return int Decimal value of binary string
     */
    public function getGlobalColorTableSize(){
        $packedField = $this->getLogicalScreenPackedField();
        $expandedField = $this->expandPackedField( $packedField );
        $bits = substr($expandedField, -3); // last 3 bits
        return bindec($bits);
    }
    
    /*
     * @return int Decimal value of byte length of color table
     */
    public function getColorTableByteSize( $size ){
        return 3 * pow(2, $size+1);//3 * 2 ^ (N+1)
    }
    
    /*
     * @return int Decimal value of binary string. Can be 0 or 1.
     */
    public function getBackgroundColorIndex(){
        $block = $this->getLogicalScreenDescriptorBlock();
        $bytes = substr($block, 11, 2);
        return bindec($bytes);
    }
    
    /*
     * @return string Signature GIF
     */
    public function getSignature(){
        $header = $this->getHeaderBlock();
        return $this->toChars( substr($header, 0, 6) );
    }
    
    /*
     * @return string Version of GIF. Examples: 89a, 87a
     */
    public function getVersion(){
        $header = $this->getHeaderBlock();
        return $this->toChars( substr($header, 6, 6) );
    }
    
    /*
     * @return string Header string converted to ascii characters. Examples: GIF89a, GIF87a
     */
    public function toChars($byteString){
        $bytes = str_split($byteString, 2);
        $string = '';
        foreach($bytes as $byte){
            $string .= chr(hexdec($byte)); // convert hex to dec to ascii character. See http://www.ascii.cl/
        }
        return $string;
    }
    
    /*
     * @return int Number of bytes
     */
    public function getSize(){
        return $this->size; // Note: Because PHP's integer type is signed and many platforms use 32bit integers, some filesystem functions may return unexpected results for files which are larger than 2GB.
    }
}