<?php
$code1 = 'require_once "../src/autoloader.php";

use PhpGd\Image;
use PhpGd\Editor;
use PhpGd\Rectangle;

$image = Image::createBlank(500, 200); // Blank image

$rectangle = new Rectangle(200, 100, "#ff0000"); // A 200x100 red rectangle

$editor = new Editor();
$editor->edit($image)
    ->fill("#cccccc") // Change blank image background to white
    ->addRectangle( $rectangle, 10, 10) // Add rectangle at position 10, 10
    ->save("tmp/rectangled.png");';


$code2 = 'require_once "../src/autoloader.php";

use PhpGd\Image;
use PhpGd\Editor;
use PhpGd\Rectangle;

$image = Image::createBlank(500, 200); // Blank image

$rectangle = new Rectangle(200, 100, "#ff0000", "#000000", 5); // A 200x100 red rectangle with a 5-pixel thick black border

$editor = new Editor();
$editor->edit($image)
    ->fill("#cccccc") // Change blank image background to white
    ->addRectangle( $rectangle, 10, 10) // Add at position 10, 10
    ->save("tmp/bordered.png");';

eval($code1);
eval($code2);
?><!DOCTYPE html>
<html>
<head>
    <title>Examples - Basic Geom</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
<h1>Basic Geometry</h1>
<p><a href="index.php">Home</a></p>

<hr>
<h2>Rectangle</h2>

<p>Add a rectangle:</p>
<pre><code><?php echo $code1; ?></code></pre>
<div>
    <img src="tmp/rectangled.png">
</div>

<p>Bordered:</p>
<pre><code><?php echo $code2; ?></code></pre>
<div>
    <img src="tmp/bordered.png">
</div>

</body>
</html>
