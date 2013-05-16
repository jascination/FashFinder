<?php

// *** Include the class  
include("resize-class.php");  
  
// *** 1) Initialize / load image  
$resizeObj = new resize('http://i.imgur.com/ujzFTH3.jpg');  
  
// *** 2) Resize image (options: exact, portrait, landscape, auto, crop)  
$resizeObj -> resizeImage(440, 440, 'auto');  
  
// *** 3) Save image  
$resizeObj -> saveImage('sample.jpg', 100);  

?>