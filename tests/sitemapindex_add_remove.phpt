--TEST--
File_Sitemap: Add and remove url from sitemap index.
--FILE--
<?php
require_once "File/Sitemap/Index.php";

try {
$sm = new File_Sitemap_Index();

$sm->add("http://mysite.net/sitemap1.gz");
$sm->add("http://mysite.net/sitemap2.gz");
$sm->add("http://mysite.net/sitemap3.gz");
$sm->remove("http://mysite.net/sitemap2.gz");

$filename = tempnam("/tmp", "sitemapindex").".xml";

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
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/siteindex.xsd">
  <sitemap>
    <loc>http://mysite.net/sitemap1.gz</loc>
  </sitemap>
  <sitemap>
    <loc>http://mysite.net/sitemap3.gz</loc>
  </sitemap>
</sitemapindex>