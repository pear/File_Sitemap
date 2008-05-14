<?php
/* vim: set noai expandtab ts=4 st=4 sw=4: */

/**
 * Abstract class providing common functions to File_Sitemap related classes.
 *
 * PHP versions 5
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *  * Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 *  * Redistributions in binary form must reproduce the above copyright notice,
 *    this list of conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 *  * The names of its contributors may not be used to endorse or promote
 *    products derived from this software without specific prior written
 *    permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category File
 * @package  File_Sitemap
 * @author   Charles Brunet <charles.fmj@gmail.com>
 * @license  http://www.opensource.org/licenses/bsd-license.html BSD License
 * @version  CVS: $Id$
 * @link     http://pear.php.net/package/File_Sitemap
 */

require_once "File/Sitemap/Exception.php";

/**
 * Abstract class providing common functions to File_Sitemap related classes.
 *
 * @category File
 * @package  File_Sitemap
 * @author   Charles Brunet <charles.fmj@gmail.com>
 * @license  http://www.opensource.org/licenses/bsd-license.html BSD License
 * @version  Release: @package_version@
 * @link     http://pear.php.net/package/File_Sitemap
 */
abstract class File_Sitemap_Base
{

    /**
     * The internal DOMDocument used by this class
     * 
     * @var DOMDocument
     */
    protected $dom;

    /**
     * XML namespace 
     */
    const XMLNS = 'http://www.sitemaps.org/schemas/sitemap/0.9';

    /**
     * namespace of XMLSchema-instance 
     */
    const XSI = 'http://www.w3.org/2001/XMLSchema-instance';

    /**
     * Constructor. Build an empty XML document, with xmlns and root element.
     * 
     * @param string $root   Name of the root element
     * @param string $schema Location of the schema
     *
     * @return void
     */
    public function __construct($root, $schema)
    {
        $imp = new DomImplementation();

        $this->dom           = $imp->createDocument(self::XMLNS, $root);
        $this->dom->version  = '1.0';
        $this->dom->encoding = 'UTF-8';
        $attr                = $this->dom->createAttributeNS(self::XSI,
                'xsi:schemaLocation');
        $attr->value         = self::XMLNS.' '.$schema;
        $this->dom->documentElement->appendChild($attr);
    }

    /**
     * Returns the DOMNode element which contains $url, or false if not found.
     * 
     * @param string $url URL (loc) we are looking for.
     *
     * @return mixed DOMNode | false
     */
    protected function findLoc($url)
    {
        foreach ($this->dom->getElementsByTagNameNS(self::XMLNS, 'loc')
                as $urlElem) {
            if ($urlElem->nodeValue == $url) {
                return $urlElem->parentNode;
            }
        }
        return false;
    }

    /**
     * Set to $nodeValue the value of the $nodeName child of $urlNode.
     *
     * If $urlNode doen't have a $nodeName child, add it.
     * 
     * @param DOMNode $urlNode  The parent of the node we want to update.
     * @param string  $nodeName The name of the node we want to update.
     * @param string  $nodeVal  The value we want to put into the node.
     *
     * @return void
     */
    protected function updateNode($urlNode, $nodeName, $nodeVal)
    {
        $exists = false;
        // replace old priority if it exists
        foreach ($urlNode->childNodes as $child) {
            if ($child->nodeName == $nodeName) {
                $child->nodeValue = $nodeVal;
                return;
            }
        }

        // If we found a value, function returns.
        // If we are here, then the node wasn't find.
        $elem = $this->dom->createElementNS(self::XMLNS, $nodeName, $nodeVal);
        $urlNode->appendChild($elem);
    }

    /**
     * Used as callback function to preg_replace_callback to urlencode char
     * 
     * @param array $char $char[0] will be encoded
     *
     * @return string
     */
    private static function _myUrlEncode($char)
    {
        return rawurlencode($char[0]);
    }

    /**
     * Ensure url contains valid chars and isn't longer than 2048 chars.
     * 
     * urlencode invalid chars. Convert invalid XML chars to entities.
     *
     * @param string $url The url we want to verify and encode.
     *
     * @return string
     *
     * @throws {@link File_Sitemap_Exception} URL doesn't begin with valid
     *    protocol (http, https, ftp) or encoded URL longer than 2048 chars.
     */
    protected function parseURL($url)
    {
        $protocols = array('http',
                'https',
                'ftp',
                );

        if (!preg_match('/^('.implode($protocols, ':\/\/|').':\/\/)/', $url)) {
            throw new File_Sitemap_Exception(
                    'URL must begin with a protocol ('.
                    implode($protocols, ', ').').',
                    File_Sitemap_Exception::PARSE_ERROR);
        }

        // encode XML special chars
        $url = strtr($url, array('&'=>'&amp;',
                    '\''=>'&apos;',
                    '"'=>'&quot;',
                    '>'=>'&gt;',
                    '<'=>'&lt;',
                    ));
        // replace other chars with %nn form
        $url = preg_replace_callback('/[^0-9a-zA-Z_'.
                ':\/?#\[\]@!$&\'()*+,;=%~.-]/',
                'File_Sitemap_Base::_myUrlEncode', $url);

        if (strlen($url) > 2048) {
            throw new File_Sitemap_Exception(
                    'URL must not be longer than 2048 chars.',
                    File_Sitemap_Exception::PARSE_ERROR);
        }

        return $url;
    }

    /**
     * Ensure that $datetime is a valid date time string
     *
     * If $datetime is conform to the spec, it is returned as is.
     * Else we try to decode it using strtotime function.
     *
     * @param string $datetime The date (and time) to pase.
     *
     * @return string
     * 
     * @see http://www.w3.org/TR/NOTE-datetime
     * @throws {@link File_Sitemap_Exception} Indalid date / time format.
     */
    protected function parseDateTime($datetime)
    {
        if (preg_match('/^\d{4}(-\d{2}(-\d{2}(T\d{2}:\d{2}(:\d{2}(\.\d+)?)?'.
                                '([+-]\d{2}:\d{2}|Z))?)?)?$/', $datetime)) {
            return $datetime;
        }

        // Try to convert it
        $timestamp = @strtotime($datetime);
        if ($timestamp === false) {
            throw new File_Sitemap_Exception(
                    'unable to parse date time string.',
                    File_Sitemap_Exception::PARSE_ERROR);
        }
        $datetime = date('Y-m-d\TH:i:sP', $timestamp);
        return $datetime;
    }

    /**
     * Remove DOMNode that contains url $loc from the document.
     * 
     * @param string $loc URL to remove
     *
     * @return void
     */
    public function remove($loc)
    {
        $loc     = $this->parseURL($loc);
        $urlNode = $this->findLoc($loc);
        if ($urlNode !== false) {
            $this->dom->documentElement->removeChild($urlNode);
        }
    }

    /**
     * Load sitemap from file. The file can be gzipped or not.
     * 
     * @param string $file Filename (or URL).
     *
     * @return void
     *
     * @throws {@link File_Sitemap_Exception} File read error.
     */
    public function load($file)
    {
        if (substr($file, -2) == 'gz') {
            $gzfile = gzopen($file, 'r');
            if ($gzfile === false) {
                throw new File_Sitemap_Exception(
                        'error opening gziped sitemap file.',
                        File_Sitemap_Exception::FILE_ERROR);
            }
            $xml = '';
            while (!gzeof($gzfile)) {
                $xml .= gzread($gzfile, 10000);
            }
            gzclose($gzfile);
            $this->dom->loadXML($xml);
        } else {
            $this->dom->load($file);
        }
    }

    /**
     * Save sitemap to file.
     * 
     * @param string  $file         Filename (or URL), including path.
     * @param boolean $compress     gzip the file? Default true.
     * @param boolean $formatOutput Nice format XML. Default false.
     *
     * @return void
     *
     * @throws {@link File_Sitemap_Exception} File write error.
     */
    public function save($file, $compress = true, $formatOutput = false)
    {
        $this->dom->formatOutput = $formatOutput;

        if ($compress) {
            if (substr($file, -3) != '.gz') {
                $file .= '.gz';
            }
            $gzfile = gzopen($file, 'w9');
            if ($gzfile === false) {
                throw new File_Sitemap_Exception(
                        'error saving gziped sitemap file.',
                        File_Sitemap_Exception::FILE_ERROR);
            }
            gzwrite($gzfile, $this->dom->saveXML());
            gzclose($gzfile);
        } else {
            $this->dom->save($file);
        }
    }

    /**
     * Notify $site that a sitemap was updated at $url 
     *
     * @param string $url  URL of the sitemap file (must be valid)
     * @param mixed  $site string | array. URL (or array of URL) of the search
     *    engine ping site
     *
     * @return void
     *
     * @throws {@link File_Sitemap_Exception} Sitemap file not reachable
     *    or ping site error.
     */
    public function notify($url,
            $site = 'http://www.google.com/webmasters/sitemaps/ping')
    {
        $this->_includeHTTPRequest();

        // check that $url exists
        $req = new HTTP_Request('');
        $req->setURL($url);
        $req->sendRequest();
        $code = $req->getResponseCode();

        switch ($code) {
        case 200:
            // Everything ok!
            break;
        default:	
            throw new File_Sitemap_Exception(
                    'Cannot reach sitemap file. Error: '.$code,
                    File_Sitemap_Exception::ERROR + $code);
        }

        // Ping the web search engine
        if (!is_array($site)) {
            $site = array($site);
        }

        $req->setMethod(HTTP_REQUEST_METHOD_GET);
        foreach ($site as $s) {
            $req->setURL($s);
            $req->addQueryString('sitemap', $url);
            $req->sendRequest();
            $code = $req->getResponseCode();

            if ($code != 200) {
                throw new File_Sitemap_Exception(
                        'Cannot reach '.$s.'. Error: '.$code,
                    File_Sitemap_Exception::ERROR + $code);
            }
        }
    }

    /**
     * Test that all url in sitemap are valid URL
     *
     * @param array &$results An array that will contains result codes.
     *    key is the url, value is the response code (200, 302, 404, etc.)
     * 
     * @return boolean true if all URLs reached
     */
    public function test(&$results = array())
    {
        $this->_includeHTTPRequest();

        $req   = new HTTP_Request('');
        $allok = true;

        $urllist = $this->dom->getElementsByTagNameNS(self::XMLNS, 'loc');
        foreach ($urllist as $urlnode) {
            $url = html_entity_decode($urlnode->nodeValue);
            $req->setURL($url);
            $req->sendRequest();
            $code          = $req->getResponseCode();
            $results[$url] = $code;
            if ($code >= 400) {
                $allok = false;
            }
        }
        return $allok;
    }

    /**
     * Validate the sitemap document against DTD
     * 
     * Be warned that it will issue some warnings if it doesn't validate.
     *
     * @param string $schema URL of the validating schema.
     *
     * @return boolean
     */
    public function validate($schema)
    {
        return $this->dom->schemaValidate($schema);
    }

    /**
     * Check for HTTP_Request package and include it
     * 
     * @return void
     */
    private function _includeHTTPRequest()
    {
        static $included = false;

        if ($included) {
            return;
        }

        @include_once 'HTTP/Request.php';

        if (!class_exists('HTTP_Request')) {
            throw new File_Sitemap_Exception(
                    'HTTP_Request class not found.',
                File_Sitemap_Exception::ERROR);
        }
    
        $included = true;
    }
}

?>
