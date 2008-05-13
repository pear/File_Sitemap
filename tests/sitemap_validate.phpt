--TEST--
File_Sitemap: Test URLs of sitemap
--FILE--
<?php
require_once "File/Sitemap.php";
require_once "File/Sitemap/Index.php";

try {
$sm = new File_Sitemap();

$sm->add("http://pear.php.net/");
$sm->add("http://pear.php.net/pepr/");
$sm->add("http://pear.php.net/packages.php");
$result = $sm->validate();

if ($result) {
	echo "Sitemap valid!\n";
}
else {
	echo "Sitemap not validated...\n";
}

$smi = new File_Sitemap_Index();

$smi->add("http://mysite.net/sitemap1.gz");
$smi->add("http://mysite.net/sitemap2.gz");
$smi->add("http://mysite.net/sitemap3.gz");
$result = $smi->validate();

if ($result) {
	echo "Sitemap index valid!\n";
}
else {
	echo "Sitemap index not validated...\n";
}
}
catch (Exception $e) {
	echo $e->getMessage();
}

?>
--EXPECT--
Sitemap valid!
Sitemap index valid!