<?php
$code1 = 'require_once "../src/autoloader.php";

use PhpGd\Image;
use PhpGd\Editor;

$image = new Image("images/sample-jpeg.jpg");

$editor = new Editor();
$editor->edit($image);
$editor->resize( 400, 225 );
$editor->save("tmp/sample-jpeg-resized.jpg");';

$code2 = 'require_once "../src/autoloader.php";

use PhpGd\Image;
use PhpGd\Editor;

$image = new Image("images/sample-png.png");

$editor = new Editor();
$editor->edit($image);
$editor->resize( 400, 200 );
$editor->save("tmp/sample-png-resized.png");';

$code3 = 'require_once "../src/autoloader.php";

use PhpGd\Image;
use PhpGd\Editor;

$image = new Image("images/sample-gif.gif");

$editor = new Editor();
$editor->edit($image);
$editor->resize( 400, 225 );
$editor->save("tmp/sample-gif-resized.gif");';

eval($code1);
eval($code2);
eval($code3);
?><!DOCTYPE html>
<html>
<head>
    <title>Examples - Resize</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <h1>Resize</h1>
    <p><a href="index.php">Home</a></p>
    <hr>
    <h2>JPEG</h2>
    <p>Original</p>
    <div>
        <img src="images/sample-jpeg.jpg">
    </div>
    <p>Resized</p>
    <div>
        <img src="tmp/sample-jpeg-resized.jpg">
    </div>
    <h3>PHP Code:</h3>
    <pre><code><?php echo $code1; ?></code></pre>

    <hr>
    <h2>PNG</h2>
    <p>Original</p>
    <div>
        <img src="images/sample-png.png">
    </div>
    <p>Resized</p>
    <div>
        <img src="tmp/sample-png-resized.png">
    </div>
    <h3>PHP Code:</h3>
    <pre><code><?php echo $code2; ?></code></pre>

    <hr>
    <h2>GIF</h2>
    <p>Note: Animated GIF is currently unsupported. Only the first frame is resized.</p>
    <p>Original</p>
    <div>
        <img src="images/sample-gif.gif">
    </div>
    <p>Resized</p>
    <div>
        <img src="tmp/sample-gif-resized.gif">
    </div>
    <h3>PHP Code:</h3>
    <pre><code><?php echo $code3; ?></code></pre>
</body>
</html>
