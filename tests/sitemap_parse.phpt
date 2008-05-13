--TEST--
File_Sitemap: Validate different possible input parameters.
--FILE--
<?php
require_once "File/Sitemap.php";

$sm = new File_Sitemap();

echo "1 validate loc\n--------------------\n";

$u = array('http://www.php.net/', 'ftp://ftp.php.net/file.txt', 'https://secure.php.net/',
	'http://pear.php.net/manual/en/core.pear.pear-exception.intro.php',
	'http://mysite.net/caractères_spéciaux.php', 'http://mysite.net/query.php?a=0&b=1&c=4',
	'www.google.com', 'abcde', 'rsync:///myserver.net');
foreach ($u as $uu) {
	echo $uu.": ";
	try {
		$sm->add($uu);
		echo "OK\n";
	}
	catch (File_Sitemap_Exception $e)
	{
		echo "Exception: ".$e->getCode().": ".$e->getMessage()."\n";
	}
}

echo "2 validate lastmod\n--------------------\n";

$lm = array('2008', '2008-04', '2008-04-12', '2008-04-12T18:20Z', '2008-04-12T18:20+05:00',
	'2008-04-12T18:20:31-04:00', '2008-04-12T18:20:31.118-04:00', 'Apr. 12, 2008', '20h27',
	'abcdef');
foreach ($lm as $lmlm) {
	echo $lmlm.": ";
	try {
		$sm->add("http://www.php.net/", 0.5, null, $lmlm);
		echo "OK\n";
	}
	catch (File_Sitemap_Exception $e)
	{
		echo "Exception: ".$e->getCode().": ".$e->getMessage()."\n";
	}
}

echo "3 validate changefreq\n--------------------\n";

$cf = array('always', 'hourly', 'daily', 'weekly', 'monthly',
	'yearly', 'never', 0, 'stchroumph');
foreach ($cf as $cfcf) {
	echo $cfcf.": ";
	try {
		$sm->add("http://www.php.net/", 0.5, $cfcf);
		echo "OK\n";
	}
	catch (File_Sitemap_Exception $e)
	{
		echo "Exception: ".$e->getCode().": ".$e->getMessage()."\n";
	}
}

echo "4 validate priority\n--------------------\n";

$p = array(-1, 0, 0.5, 0.8, 1, 2, "-1", "0", "0.0", ".5", "0.85", "1.2", "a");
foreach ($p as $pp) {
	echo $pp.": ";
	try {
		$sm->add("http://www.php.net/", $pp);
		echo "OK\n";
	}
	catch (File_Sitemap_Exception $e)
	{
		echo "Exception: ".$e->getCode().": ".$e->getMessage()."\n";
	}
}

?>
--EXPECT--
1 validate loc
--------------------
http://www.php.net/: OK
ftp://ftp.php.net/file.txt: OK
https://secure.php.net/: OK
http://pear.php.net/manual/en/core.pear.pear-exception.intro.php: OK
http://mysite.net/caractères_spéciaux.php: OK
http://mysite.net/query.php?a=0&b=1&c=4: OK
www.google.com: Exception: 2000: URL must begin with a protocol (http, https, ftp).
abcde: Exception: 2000: URL must begin with a protocol (http, https, ftp).
rsync:///myserver.net: Exception: 2000: URL must begin with a protocol (http, https, ftp).
2 validate lastmod
--------------------
2008: OK
2008-04: OK
2008-04-12: OK
2008-04-12T18:20Z: OK
2008-04-12T18:20+05:00: OK
2008-04-12T18:20:31-04:00: OK
2008-04-12T18:20:31.118-04:00: OK
Apr. 12, 2008: OK
20h27: Exception: 2000: unable to parse date time string.
abcdef: Exception: 2000: unable to parse date time string.
3 validate changefreq
--------------------
always: OK
hourly: OK
daily: OK
weekly: OK
monthly: OK
yearly: OK
never: OK
0: Exception: 2000: changefreq must be one of always, hourly, daily, weekly, monthly, yearly or never.
stchroumph: Exception: 2000: changefreq must be one of always, hourly, daily, weekly, monthly, yearly or never.
4 validate priority
--------------------
-1: OK
0: OK
0.5: OK
0.8: OK
1: OK
2: OK
-1: OK
0: OK
0.0: OK
.5: OK
0.85: OK
1.2: OK
a: Exception: 2000: priority must be a number between 0.0 and 1.0.