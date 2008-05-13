--TEST--
File_Sitemap: Add and remove url from sitemap.
--FILE--
<?php
require_once "File/Sitemap.php";

try {
$sm = new File_Sitemap();

$sm->add("http://pear.php.net/");
$sm->add("http://pear.php.net/pepr/");
$sm->add("http://pear.php.net/packages.php");
$sm->remove("http://pear.php.net/pepr/");

$filename = tempnam("/tmp", "sitemap").".xml";

$sm->save($filename, false, true);

$f = fopen($filename, 'r');
if ($f === false) {
	throw new Exception("Cannot open file");
}
while (!feof($f)) {
	echo fread($f, 10000);
}
fclose($f);
unlink($filename);
}
catch (Exception $e) {
	echo $e->getMessage();
}

?>
--EXPECT--
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
  <url>
    <loc>http://pear.php.net/</loc>
    <priority>0.5</priority>
  </url>
  <url>
    <loc>http://pear.php.net/packages.php</loc>
    <priority>0.5</priority>
  </url>
</urlset>
