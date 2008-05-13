--TEST--
File_Sitemap: Test URLs of sitemap
--FILE--
<?php
require_once "File/Sitemap.php";

try {
$sm = new File_Sitemap();

$sm->add("http://pear.php.net/");
$sm->add("http://pear.php.net/pepr/");
$sm->add("http://pear.php.net/packages.php");
$results = array();
$sm->test($results);
print_r($results);
}
catch (Exception $e) {
	echo $e->getMessage();
}

?>
--EXPECT--
Array
(
    [http://pear.php.net/] => 200
    [http://pear.php.net/pepr/] => 200
    [http://pear.php.net/packages.php] => 200
)