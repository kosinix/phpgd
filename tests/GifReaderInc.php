<html>
<head>
    <meta charset="UTF-8">
    <title>Document</title>
    <style>
        body {
            font-size: 16px;
            background: #c9c9c9;
        }
        div {
            display: inline-block;
            padding: 2px;
            background: #fff;
           
        }
        .value {
             text-transform: uppercase;
        }
        .header {
            background-color: #F9E89D;
        }
        .getLogicalScreenDescriptorBlock {
            background: #C8DBD9;
        }
        .getGlobalColorTableBlock {
            background: #E1E1E1;
        }
        .getApplicationExtensionBlock{
            background: #b6b88d;
        }
        .getGraphicsControlExtensionBlock,
        .getTrailerBlock{
            background: #F9EB9D;
        }
        .getImageDescriptorBlock{
            background: #C2D1DC;
        }
        .getImageDataBlock{
            background: #D0C4C4;
        }
    </style>
</head>
<body>
<?php
require_once '../src/autoloader.php';

use PhpGd\Image;
use PhpGd\Editor;
use PhpGd\GifReader;


$gifReader = new GifReader('images/anim.gif'); 
echo $gifReader->getSignature();
echo $gifReader->getVersion();
echo ' width '.$gifReader->getCanvasWidth();
echo ' height '.$gifReader->getCanvasHeight();
echo ' hasGlobalColorTable '.$gifReader->hasGlobalColorTable();
echo ' getGlobalColorTableSize '.$gifReader->getGlobalColorTableSize();
echo ' getGlobalColorTableByteSize '.$gifReader->getColorTableByteSize( $gifReader->getGlobalColorTableSize() );
echo ' getBackgroundColorIndex '.$gifReader->getBackgroundColorIndex();
p('Header Block', $gifReader->getHeaderBlock(), 'header');
p('Logical Screen Descriptor Block', $gifReader->getLogicalScreenDescriptorBlock(), 'getLogicalScreenDescriptorBlock');
p('Global Color Table Block', $gifReader->getGlobalColorTableBlock(), 'getGlobalColorTableBlock');
p('Application Extension Block', $gifReader->getApplicationExtensionBlock(), 'getApplicationExtensionBlock');
//$start = microtime(true);
$frames = $gifReader->getFrames();
//echo microtime(true) - $start;

foreach($frames as $frame){
    p('Graphics Control Extension Block', $gifReader->getGraphicsControlExtensionBlock( $frame ), 'getGraphicsControlExtensionBlock');
    p('Image Descriptor Block', $gifReader->getImageDescriptorBlock( $frame ), 'getImageDescriptorBlock');
    p('Image Data Block', $gifReader->getImageDataBlock( $frame ), 'getImageDataBlock');
}
p('Trailer Block', $gifReader->getTrailerBlock(), 'getTrailerBlock');


function p($name, $value, $class){
    ob_start();
    $value = str_split($value, 2);
    foreach($value as $byte){
        echo $byte.' ';
    }
    $value = ob_get_clean();
    echo '<div> '.$name.' </div><div class="value '.$class.'"> '.$value.' </div>';
}
?>
</body>
</html>