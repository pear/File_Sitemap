--TEST--
File_Sitemap: Test sitemap file writing and reading
--FILE--
<?php
require_once "File/Sitemap.php";

try {
$sm = new File_Sitemap();

$sm->add("http://pear.php.net/");
$sm->add("http://pear.php.net/pepr/");
$sm->add("http://pear.php.net/packages.php");

$filename = tempnam("/tmp", "sitemap").".xml";
$filename2 = tempnam("/tmp", "sitemap").".gz";
$filename3 = tempnam("/tmp", "sitemap").".xml";
$filename4 = tempnam("/tmp", "sitemap").".gz";

$sm->save($filename, false);
$sm->save($filename2, true);

$sm3 = new File_Sitemap();
$sm3->load($filename);
$sm3->save($filename3, false);
$sm4 = new File_Sitemap();
$sm4->load($filename2);
$sm4->save($filename4, true);

if (md5_file($filename) == md5_file($filename3)) {
	echo "Plain: passed!\n";
}
else {
	echo "Plain: failed...\n";
}
if (md5_file($filename2) == md5_file($filename4)) {
	echo "Gzipped: passed!";
}
else {
	echo "Gzipped: failed...";
}

unlink($filename);
unlink($filename2);
unlink($filename3);
unlink($filename4);
}
catch (Exception $e) {
	echo $e->getMessage();
}

?>
--EXPECT--
Plain: passed!
Gzipped: passed!
