<?php
/**
 * Image Interface for consistent interface between classes
 * 
 * @author Nico Amarilla
 */
namespace PhpGd;

interface ImageInterface {

	/* Must have these getters */
    public function getImageFile();
    
    public function getImageResource();
    
    public function getWidth();
    
    public function getHeight();
    
	public function getType();
	
	/* Must have these setters */
	public function setImageFile( $value );
    
    public function setImageResource( $value );
    
    public function setWidth( $value );
    
    public function setHeight( $value );
	
	public function setType( $value );
}