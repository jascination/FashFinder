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
	public $infoLink = '';
	public $description = '';
	public $mainImage = '';
	public $moreImages = array();
	public $price = '';
	public $designer = '';
}

function getInfo($infoLink) {
	$html = str_get_html(curl($infoLink));

	$product = new Product;
	$product->name = $html->find("#item_info h1", 0)->innertext;
	$product->infoLink = $infoLink;
	$product->designer = $html->find("#item_info h2", 0)->innertext;
	$product->description = $html->find("#item_info .product-body", 0)->innertext;
	$product->price = $html->find(".price", 0)->innertext;
	//Might cause issues because there are multiple <p> tags in this div
	$product->mainImage = $html->find("#item_image .imagecache-product_item_default", 0)->src;

	for ($idx = 0; $idx < 4; $idx++) {
		$more = $html->find(".extra_images img", $idx);

		if (!is_null($more)) {
			$product->moreImages[] = $more->src;
		}
	}

	print_r($product);

	return $product;
}

$allLinks = getLinks("http://www.uptherestore.com/department/accessories");

$products = array();
foreach ($allLinks as $key => $value) {
	$products[] = getInfo($value);
}

$fp = fopen('results.json', 'w');
fwrite($fp, json_encode($products));
fclose($fp);

?>