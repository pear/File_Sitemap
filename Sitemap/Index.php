<?php
/* vim: set noai expandtab ts=4 st=4 sw=4: */

/**
 * Generate sitemap index file.
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

require_once "File/Sitemap/Base.php";

/**
 * Generate sitemap index file.
 *
 * @category File
 * @package  File_Sitemap
 * @author   Charles Brunet <charles.fmj@gmail.com>
 * @license  http://www.opensource.org/licenses/bsd-license.html BSD License
 * @version  Release: @package_version@
 * @link     http://pear.php.net/package/File_Sitemap
 */
class File_Sitemap_Index extends File_Sitemap_Base
{

    /**
     * URL of XML schema
     */
    const SCHEMA = 'http://www.sitemaps.org/schemas/sitemap/0.9/siteindex.xsd';

    /**
     * Constructor.
     * 
     * @return void
     */
    public function __construct()
    {
        parent::__construct('sitemapindex', self::SCHEMA);
    }

    /**
     * Add a sitemap to the sitemapindex.
     * 
     * @param mixed $loc     string | array. URL (or array of URL) of the
     *    sitemap file.
     * @param mixed $lastmod Date (and time) of last modification (optional).
     *
     * @return void
     */
    public function add($loc, $lastmod = null)
    {
        if (!is_array($loc)) {
            $loc = array($loc);
        }

        foreach ($loc as $l) {
            // normalize and encode $loc
            $l = $this->parseURL($l);

            // look for this url into the dom tree
            $sitemap = $this->findLoc($l);
            if ($sitemap === false) {
                // Create the url node, and append loc node
                $sitemap = $this->dom->createElementNS(self::XMLNS, 'sitemap');

                $elemLoc = $this->dom->createElementNS(self::XMLNS, 'loc', $l);
                $sitemap->appendChild($elemLoc);
                $newURL = true;
            } else {
                $newURL = false;
            }

            if ($lastmod !== null) {
                $lastmod = $this->_parseDateTime($lastmod);
                $this->updateNode($sitemap, 'lastmod', $lastmod);
            }

            $this->dom->documentElement->appendChild($sitemap);
        }
    }

    /**
     * Validate sitemap index with the schema definition.
     * 
     * @return boolean
     */
    public function validate()
    {
        return parent::validate(self::SCHEMA);
    }

}

?>
