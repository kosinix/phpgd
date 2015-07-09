<?php
$code1 = 'require_once "../src/autoloader.php";

use PhpGd\Image;
use PhpGd\Editor;

$image = new Image("images/sample-jpeg.jpg"); // Load image
$watermark = new Image ("images/watermark.png"); // Load image for watermark

$editor = new Editor();
$editor->edit($image);

$editor->addWatermark($watermark); // Add watermark

$editor->save("tmp/watermarked.jpg");';

$code2 = 'require_once "../src/autoloader.php";

use PhpGd\Image;
use PhpGd\Editor;

$image = new Image("images/sample-jpeg.jpg"); // Load image
$watermark = new Image ("images/watermark.png"); // Load image for watermark

$editor = new Editor();
$editor->edit($image);

$editor->addWatermark($watermark, 100, 100); // Add watermark and position it at 100 pixels from left and 100 pixels from top

// Save
$editor->save("tmp/watermark-x100-y100.jpg");';

$code3 = 'require_once "../src/autoloader.php";

use PhpGd\Image;
use PhpGd\Editor;

$image = new Image("images/sample-jpeg.jpg"); // Load image
$watermark = new Image ("images/watermark.png"); // Load image for watermark

$editor = new Editor();
$editor->edit($image);

$editor->addWatermark($watermark, "right", "bottom"); // Add watermark and position it at the right (x axis) bottom (y axis) of the image

// Save
$editor->save("tmp/watermark-xRight-yBottom.jpg");';

eval($code1);
eval($code2);
eval($code3);
?><!DOCTYPE html>
<html>
<head>
    <title>Examples - Add Watermark</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
<h1>Add Watermark</h1>
<p><a href="index.php">Home</a></p>

<hr>
<h2>Original Image</h2>
<div>
    <img src="images/sample-jpeg.jpg">
</div>

<h2>Watermark Image</h2>
<div>
    <img src="images/watermark.png">
</div>

<hr>
<h3>Default</h3>
<p>By default the watermark is placed in the center:</p>
<pre><code><?php echo $code1; ?></code></pre>
<div>
    <img src="tmp/watermarked.jpg">
</div>

<hr>
<h3>X and Y Coordinates</h3>
<p>Watermark positioned 100 pixels from left and 100 pixels from top:</p>
<pre><code><?php echo $code2; ?></code></pre>
<div>
    <img src="tmp/watermark-x100-y100.jpg">
</div>

<hr>
<h3>Using Words</h3>
<p>English words (top, center, bottom, left, right) can be used instead of numbers. Here a watermark is positioned at the bottom right of the image:</p>
<pre><code><?php echo $code3; ?></code></pre>
<div>
    <img src="tmp/watermark-xRight-yBottom.jpg">
</div>

</body>
</html>
