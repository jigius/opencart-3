<?php

require_once __DIR__ . "/assets/FakedConfig.php";
require_once __DIR__ . "/assets/FakedDb.php";
require_once __DIR__ . "/../../system/library/url.php";
require_once __DIR__ . "/../../system/engine/registry.php";
require_once __DIR__ . "/../../system/engine/controller.php";
require_once __DIR__ . "/../../catalog/controller/startup/seo_url.php";

define("DB_PREFIX", "foo");

$registry = new Registry();
$registry
	->set(
		'config',
		new FakedConfig(['config_seo_url' => true, 'config_store_id' => 0, 'config_language_id' => 1])
	);
$url = new Url("http://example.com/", "https://example.com/");
$url->addRewrite(new ControllerStartupSeoUrl($registry));

echo "Test:\n";

ob_start();
// Test 1
/*
 * seo_url keywords are not defined for the product & category
 */
$registry->set('db', new FakedDb([]));
echo
	$url
		->link(
			"product/product",
			[
				'product_id' => 123,
				'path' => 78
			]
		) === "http://example.com/index.php?route=product/product&amp;product_id=123&path=78"? ".": "E";

// Test 2
/*
 * seo_url keyword is defined for the product only
 */
$registry->set('db', new FakedDb(['product' => "parrot"]));
echo
	$url
		->link(
			"product/product",
			[
				'path' => 78,
				'product_id' => 123,
			]
		) === "http://example.com/parrot?path=78"? ".": "E";

// Test 3
/*
 * seo_url keyword is defined for the product only
 */
$registry->set('db', new FakedDb(['product' => "parrot"]));
echo
	$url
		->link(
			"product/product",
			[
				'product_id' => 123,
				'path' => "78_90"
				
			]
		) === "http://example.com/parrot?path=78_90"? ".": "E";

// Test 4
/*
 * seo_url keyword is defined for the product and with two defined categories
 */
$registry->set('db', new FakedDb(['product' => "parrot", "category" => [78 => "tree", 90 => "leaf"]]));
echo
	$url
		->link(
			"product/product",
			[
				'product_id' => 123,
				'path' => "78_90"
			]
		) === "http://example.com/parrot/tree/leaf"? ".": "E";

// Test 5
/*
 * seo_url keyword is defined for the product and with only one defined categories
 */
$registry->set('db', new FakedDb(['product' => "parrot", "category" => [78 => "tree"]]));
echo
	$url
		->link(
			"product/product",
			[
				'product_id' => 123,
				'path' => "78_90"
			]
		) === "http://example.com/parrot?path=78_90"? ".": "E";

// Test 6
/*
 * seo_url keyword is defined for the product only
 */
$registry->set('db', new FakedDb(['product' => "parrot"]));
echo
	$url
		->link(
			"product/product",
			[
				'path' => 78,
				'product_id' => 123
			]
		) === "http://example.com/parrot?path=78"? ".": "E";

// Test 7
/*
 * seo_url keywords are defined for the product & category
 */
$registry->set('db', new FakedDb(['product' => "parrot", 'category' => "tree"]));
echo
	$url
		->link(
			"product/product",
			[
				'product_id' => 123,
				'path' => 78
			]
		) === "http://example.com/parrot/tree"? ".": "E";

// Test 8
/*
 * seo_url keywords are defined for the product & category
 */
$registry->set('db', new FakedDb(['product' => "parrot", 'category' => "tree"]));
echo
	$url
		->link(
			"product/product",
			[
				'path' => 78,
				'product_id' => 123
			]
		) === "http://example.com/tree/parrot"? ".": "E";

// Test 9
/*
 * seo_url keyword is defined for the category only
 */
$registry->set('db', new FakedDb(['category' => "tree"]));
echo
	$url
		->link(
			"product/product",
			[
				'path' => 78,
				'product_id' => 123
			]
		) === "http://example.com/tree?product_id=123"? ".": "E";

// Test 10
/*
 * seo_url keyword is defined for the category only
 */
$registry->set('db', new FakedDb(['category' => "tree"]));
echo
	$url
		->link(
			"product/product",
			[
				'product_id' => 123,
				'path' => 78
			]
		) === "http://example.com/tree?product_id=123"? ".": "E";

$result = ob_get_clean();

echo $result;
echo "\n\n";
if (strstr($result, "E")) {
	echo "Some tests were failure.\n\n";
	$ret = 1;
} else {
	echo "Success!\n\n";
	$ret = 0;
}
exit($ret);
