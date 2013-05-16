<?php
//Set infinite time limit
set_time_limit (0);
// Include simple html dom
include('simple_html_dom.php');
// Defining the basic cURL function
function curl($url) {
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


function getLinks($url) {
	$urls = array();

	while($url) {
		echo "Indexing: $url" . PHP_EOL;
		$html = str_get_html(curl($url));

		foreach ($html->find("div.views-row a.imagecache-product_list") as $el) {
			$urls[] = "http://www.uptherestore.com" . $el->href;
		}

		$next = $html->find("li.pager-next a", 0);
		$url = $next ? "http://www.uptherestore.com" . $next->href : null;
	}

	return $urls;
}

class Product {
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
}

class Snippet{
	public $mainImage = '';
	public $name = '';
	public $designer = '';
	public $price = '';
}

function getInfo($infoLink, $headURL) {
	$html = str_get_html(curl($infoLink));

	$product = new Product;
	$product->name = $html->find("#item_info h1", 0)->innertext;
	$product->systemName = preg_replace("![^a-z0-9]+!i", "_", $product->name);
	$product->infoLink = $infoLink;
	$product->designer = $html->find("#item_info h2", 0)->innertext;
	$product->description = strip_tags($html->find("#item_info .product-body p", 0)->innertext);
	$product->price = strip_tags($html->find(".price", 0)->innertext);
	//Might cause issues because there are multiple <p> tags in this div
	$product->mainImage = $html->find("#item_image .imagecache-product_item_default", 0)->src;

	for ($idx = 0; $idx < 4; $idx++) {
		$more = $html->find(".extra_images", $idx);

		if (!is_null($more)) {
			$product->moreImages[] = $more->href;
		}
	}
	
	$imageUrls = $product->moreImages;
	$imgs = 0;
	$product->allImgPaths[0] = "img/".$product->systemName."_0.jpg";
	copy($product->mainImage, "img/".$product->systemName."_0.jpg");	
		
		foreach ($imageUrls as $key => $value){
			$imgs++;
			$fileName = (string)$product->systemName."_".$imgs.".jpg";
			copy($headURL.$value, "img/".$fileName);
			$product->allImgPaths[$imgs] = "img/".$fileName;
		}
		
	
	// Write an individual JSON file
		$fp = fopen('json/' . $product->systemName . '.json', 'w');
		fwrite($fp, json_encode($product, JSON_PRETTY_PRINT));
		fclose($fp);
		
	//Return $product->systemName .".json" if using individual files
		$snippet = New Snippet;
		$snippet->mainImage = $product->allImgPaths;
		$snippet->name = $product->name;
		$snippet->designer = $product->designer;
		$snippet->price = $product->price;
		$snippet->systemName = $product->systemName;

		return($snippet);
	}

$headURL  = "http://www.uptherestore.com";
$allLinks = getLinks("http://www.uptherestore.com/department/accessories");
$jsonLinks = array();

$products = array();
foreach ($allLinks as $key => $value) {
	$products[] = getInfo($value, $headURL, $jsonLinks);
}

// Write Everything to one JSON file. 
	$fp = fopen('upThereAccessories.json', 'w');
	fwrite($fp, json_encode($products, JSON_PRETTY_PRINT));
	fclose($fp);


?>