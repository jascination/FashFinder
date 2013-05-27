<?php
//Set infinite time limit
set_time_limit(0);

// Include simple html dom
include('simple_html_dom.php');
include("resize-class.php");
// Defining the basic cURL function
function curl($url)
{
    $ch = curl_init();
    // Initialising cURL
    curl_setopt($ch, CURLOPT_URL, $url);
    // Setting cURL's URL option with the $url variable passed into the function
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    // Setting cURL's option to return the webpage data
    $data = curl_exec($ch);
    // Executing the cURL request and assigning the returned data to the $data variable
    curl_close($ch);
    // Closing cURL
    return $data;
    // Returning the data from the function
}

function getLinks($url, $prodURL, $baseURL, $next_select)
{
    $urls = array();

    while ($url) {
        echo "Indexing: $url" . PHP_EOL;
        $html = str_get_html(curl($url));
        
        foreach ($html->find($prodURL) as $el) {
            $urls[] = $baseURL . $el->href;
        }
        
        $next = $html->find($next_select, 0);
        $url  = $next ? $baseURL . $next->href : null;
    }
    return $urls;
}

class Product
{
    //Creates an object class for products
    public $name = '';
    public $systemName = '';
    public $infoLink = '';
    public $description = '';
    public $mainImage = '';
    public $moreImages = array();
    public $price = '';
    public $designer = '';
    public $allImgPaths = array();
    public $store = '';
    public $category = '';
    public $spec_cat = '';
}

class Snippet
{
    public $mainImage = '';
    public $name = '';
    public $designer = '';
    public $price = '';
    public $store = '';
    public $category = '';
    public $spec_cat = '';
}

function getInfo($infoLink, $jsonLinks, $baseURL, $catURL, $store, $general_cat, $spec_cat, $next_select, $prod_name, $label_name, $description, $price, $mainImg, $more_imgs, $mainImgRef, $moreImgRef)
{
    
    
    
    $html                 = str_get_html(curl($infoLink));
    $product              = new Product;
    $product->name        = $html->find($prod_name, 0)->innertext;
    $product->systemName  = preg_replace("![^a-z0-9]+!i", "_", $product->name);
    $product->infoLink    = $infoLink;
    $product->designer    = $html->find($label_name, 0)->innertext;
    $product->description = strip_tags($html->find($description, 0)->innertext);
    $product->price       = strip_tags($html->find($price, 0)->innertext);
    $product->store       = $store;
    $product->category    = $general_cat;
    $product->spec_cat    = $spec_cat;
    
    if (!empty($mainImg)) {
        $product->mainImage = $html->find($mainImg, 0)->$mainImgRef;
        
        for ($idx = 0; $idx < 10; $idx++) {
            $more = $html->find($more_imgs, $idx);
            
            if (!is_null($more)) {
                $product->moreImages[$idx] = $more->$moreImgRef;
            }
        }
    } else {
        for ($idx = 0; $idx < 10; $idx++) {
            $more = $html->find($more_imgs, $idx);
            
            if (($idx == 0) && (!is_null($more))) {
                $product->mainImage = $more->$moreImgRef;
            } elseif (!is_null($more)) {
                $product->moreImages[$idx] = $more->$moreImgRef;
            }
        }
    }
    
    
    $imageUrls = $product->moreImages;
    $imgs      = 0;
    $imgDir    = "img/" . $store . "/";
    if (!is_dir($imgDir)) {
        mkdir($imgDir, 0755, true);
    }
    $product->allImgPaths[0] = $imgDir . $product->systemName . "_0.jpg";
    
    if ((substr($product->mainImage, 0, 4) == 'www.') || (substr($product->mainImage, 0, 5) == 'http:')) {
        $resizeObj = new resize($product->mainImage);
        $resizeObj->resizeImage(440, 440, 'auto');
        $resizeObj->saveImage($imgDir . $product->systemName . "_0.jpg", 80);
        
        //            copy($product->mainImage, $imgDir . $product->systemName."_0.jpg");    
    } else {
        $resizeObj = new resize($baseURL . $product->mainImage);
        $resizeObj->resizeImage(440, 440, 'auto');
        $resizeObj->saveImage($imgDir . $product->systemName . "_0.jpg", 80);
        
        //        copy($baseURL . $product->mainImage, $imgDir . $product->systemName."_0.jpg");    
    }
    foreach ($imageUrls as $key => $value) {
        $imgs++;
        $fileName = (string) $product->systemName . "_" . $imgs . ".jpg";
        if ((substr($value, 0, 4) === 'www.') || (substr($value, 0, 7) === 'http://')) {
            $resizeObj = new resize($value);
            $resizeObj->resizeImage(440, 440, 'auto');
            $resizeObj->saveImage($imgDir . $fileName, 100);
            
            
            //            copy($value, $imgDir . $fileName);
        } else {
            $resizeObj = new resize($baseURL . $value);
            $resizeObj->resizeImage(440, 440, 'auto');
            $resizeObj->saveImage($imgDir . $fileName, 100);
            //            copy($baseURL . $value, $imgDir . $fileName);
        }
        $product->allImgPaths[$imgs] = $imgDir . "/" . $fileName;
        
    }
    
    // Write an individual JSON file
    $fileDir = 'json/' . $store . '/';
    if (!is_dir($fileDir)) {
        mkdir($fileDir, 0755, true);
    }
    $fp = fopen($fileDir . $product->systemName . '.json', 'w');
    fwrite($fp, json_encode($product, JSON_PRETTY_PRINT));
    fclose($fp);
    
    echo "Writing: $infoLink" . PHP_EOL;
    

    //Return $product->systemName .".json" if using individual files
    $snippet             = New Snippet;
    $snippet->mainImage  = $product->allImgPaths;
    $snippet->name       = $product->name;
    $snippet->designer   = $product->designer;
    $snippet->price      = $product->price;
    $snippet->systemName = $product->systemName;
    $snippet->store      = $product->store;
    $snippet->category   = $product->category;
    $snippet->spec_cat   = $product->spec_cat;
    return ($snippet);
}


function workerFile($array, $mainImgRef, $moreImgRef)
{
    foreach ($array as $value) {
        
        $baseURL     = $value['baseurl'];
        $catURL      = $value['url'];
        $store       = $value['store'];
        $general_cat = $value['general_cat'];
        $spec_cat    = $value['spec_cat'];
        $next_select = $value['next_select'];
        $prod_name   = $value['prod_name_select'];
        $label_name  = $value['label_name_select'];
        $description = $value['desc_select'];
        $price       = $value['price_select'];
        $prodURL     = $value['product_url'];
        $mainImg     = $value['mainImg_select'];
        
        $more_imgs = $value['more_imgs'];
        
        $allLinks = getLinks($catURL, $prodURL, $baseURL, $next_select);
        
        $jsonLinks = array();
        
        $products = array();
        
        foreach ($allLinks as $key => $val) {
            $products[] = getInfo($val, $jsonLinks, $baseURL, $catURL, $store, $general_cat, $spec_cat, $next_select, $prod_name, $label_name, $description, $price, $mainImg, $more_imgs, $mainImgRef, $moreImgRef);
        }
        
        
        // Write Everything to one JSON file. 
        $jsonDir = 'catJSON/' . $store . '/';
        if (!is_dir($jsonDir)) {
            mkdir($jsonDir, 0755, true);
        }
        $fp = fopen($jsonDir . $general_cat . '.json', 'w');
        fwrite($fp, json_encode($products, JSON_PRETTY_PRINT));
        fclose($fp);
        
        echo "Writing: $general_cat JSON" . PHP_EOL;

    }
}

echo "Writing: Up There Json" . PHP_EOL;


$string     = file_get_contents("jsonWorkers/upThere.json");
$upThereArr = json_decode($string, true);
workerFile($upThereArr, 'src', 'href');

echo "Writing: Incu Json" . PHP_EOL;


$string  = file_get_contents("jsonWorkers/incuMens.json");
$incuArr = json_decode($string, true);
workerFile($incuArr, 'src', 'src');

?>