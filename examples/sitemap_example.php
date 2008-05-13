<?php

require_once "File/Sitemap.php";
require_once "File/Sitemap/Index.php";

// Since dom is a protected member, we create
// this little utility class to show an example
// of the generated output.
class Sitemap_Example extends File_Sitemap
{
	function output()
	{
        $this->dom->formatOutput = true;
		$sm = $this->dom->saveXML();
		$sm = htmlentities($sm);
		echo "<pre>";
		echo $sm;
		echo "</pre>\n";
	}
}

// Create sitemap object
// $sm = new File_Sitemap();
$sm = new Sitemap_Example();

// Let define some urls
$baseurl = 'http://pear.php.net';
$urls = array('/',
	'/packages.php',
	'/manual/',
	'/manual/en/',
	'/manual/en/preface.php',
	'/pepr/',
	'/pepr/pepr-proposal-show.php?id=555',
);

// A function to generate an arbitrary priority number...
function priority($url)
{
	$a = array();
	$n = 0;
	$n += preg_match_all('/\.php/', $url, $a);	// url contains .php
	$n += preg_match_all('/\//', $url, $a);		// number of /
	$n += preg_match_all('/\?/', $url, $a);		// url contains ?

	$p = 1 / $n;

	return $p;
}

// Add urls to our sitemap
foreach ($urls as $url) {
	$sm->add($baseurl.$url, priority($url));
}

// Add some precisions for specific pages
$sm->add($baseurl.'/', NULL, 'daily');
$sm->add($baseurl.'/manual/', NULL, 'weekly');

// Validate our sitemap (not really needed is we used the API to generate it!)
// $sm->validate();

// Test validity of all urls in the sitemap
// This could take a very long time if sitemap is huge...
// $sm->test();

// Save sitemap to compressed file
// $sm->save('/path/to/web/root/sitemap1.gz');

// Notify Google about our sitemap update
// $sm->notify('http://my.web.site/sitemap1.gz');

// This is our sitemap: (output is not a function of File_Sitemap class!)
$sm->output();


// Not an example with sitemap index
$smi = new File_Sitemap_Index();

$sitemaps = array('http://my.web.site/sitemap1.gz',
		          'http://my.web.site/sitemap2.gz',
		          'http://my.web.site/sitemap3.gz',
);

$smi->add($sitemaps);

// It's a good idea to ensure that all sitemaps are reacheable...
// $smi->test();

// Save the sitemap index
// $smi->save('/path/to/wesite/root/sitemap.gz')

?>
