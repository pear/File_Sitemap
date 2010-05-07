<?php
/* vim: set noai expandtab ts=4 st=4 sw=4: */

/**
 * Generate sitemap files. See http://www.sitemaps.org/protocol.php
 * for more details.
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
 * @author   Charles Brunet <cbrunet@php.net>
 * @license  http://www.opensource.org/licenses/bsd-license.html BSD License
 * @version  CVS: $Id$
 * @link     http://pear.php.net/package/File_Sitemap
 */

require_once "File/Sitemap/Base.php";

/**
 * Generate sitemap files. See http://www.sitemaps.org/protocol.php
 * for more details.
 *
 * @category File
 * @package  File_Sitemap
 * @author   Charles Brunet <cbrunet@php.net>
 * @license  http://www.opensource.org/licenses/bsd-license.html BSD License
 * @version  Release: @package_version@
 * @link     http://pear.php.net/package/File_Sitemap
 */
class File_Sitemap extends File_Sitemap_Base
{

    const SCHEMA = 'http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd';

    /**
     * Constructor. Build an empty XML document, with xmlns and root element.
     * 
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct('urlset', self::SCHEMA);
    }

    /**
     * Add or update a location in current sitemap
     * 
     * @param mixed  $loc        string | array. URL (or array of URL).
     *    Must contains protocol (http://) and trailling slash.
     * @param float  $priority   A number between 0.0 and 1.0 describing
     *    relative priority. Default: 0.5
     * @param string $changefreq Optional. Must be 'always', 'houly', 'daily',
     *    'weekly', 'monthly', 'yearly' or 'never'.
     * @param string $lastmod    Optional. Date (and time) of last page
     *    modification.
     * 
     * @return void
     */
    public function add($loc, $priority = 0.5, $changefreq = null,
        $lastmod = null
    ) {
        if (!is_array($loc)) {
            $loc = array($loc);
        }

        foreach ($loc as $l) {
            // normalize and encode $l
            $l = $this->parseURL($l);

            // look for this url into the dom tree
            $url = $this->findLoc($l);
            if ($url === false) {
                // Create the url node, and append l node
                $url = $this->dom->createElementNS(self::XMLNS, 'url');

                $elemLoc = $this->dom->createElementNS(self::XMLNS, 'loc', $l);
                $url->appendChild($elemLoc);
            }

            if ($lastmod !== null) {
                $lastmod = $this->parseDateTime($lastmod);
                $this->updateNode($url, 'lastmod', $lastmod);
            }

            if ($changefreq !== null) {
                $changefreq = $this->parseChangefreq($changefreq);
                $this->updateNode($url, 'changefreq', $changefreq);
            }

            if ($priority !== null) {
                $priority = $this->parsePriority($priority);
                $this->updateNode($url, 'priority', $priority);
            }

            $this->dom->documentElement->appendChild($url);
        }
    }

    /**
     * Ensure that priority is a number between 0.0 and 1.0
     * 
     * @param float $priority A number between 0.0 and 1.0
     * 
     * @return string
     *
     * @throws {@link File_Sitemap_Exception} Priority is not a number.
     */
    protected function parsePriority($priority)
    {
        if (!is_numeric($priority)) {
            throw new File_Sitemap_Exception(
                    'priority must be a number between 0.0 and 1.0.',
                    File_Sitemap_Exception::PARSE_ERROR);
        }

        $priority = (float) $priority;
        if ($priority > 1.0) {
            $priority = 1.0;
        } elseif ($priority < 0.0) {
            $priority = 0.0;
        }
        $priority = (string) $priority;
        // Apending .0 will ensure that 0 and 1 will give 0.0 and 1.0
        $priority = substr($priority.'.0', 0, 3);
        return $priority;
    }

    /**
     * Ensure that $changefreq parameter is valid.
     * 
     * @param string $changefreq A valid changefreq parameter: always, hourly,
     *    daily, weekly, monthly, yearly or never.
     * 
     * @return string
     *
     * @throws {@link File_Sitemap_Exception} changefreq not valid.
     */
    protected function parseChangefreq($changefreq)
    {
        // I don't know why, but when changefreq === 0, it validates
        // if I don't do that...
        if ($changefreq === 0) {
            $changefreq = '';
        }
        switch ($changefreq) {
        case 'always':
        case 'hourly':
        case 'daily':
        case 'weekly':
        case 'monthly':
        case 'yearly':
        case 'never':
            break;
        default:
            throw new File_Sitemap_Exception(
                    'changefreq must be one of always, hourly, daily, weekly, '.
                    'monthly, yearly or never.',
                    File_Sitemap_Exception::PARSE_ERROR);
        }
        return $changefreq;
    }

    /**
     * Validate sitemap against its schema definition.
     * 
     * @return boolean
     */
    public function validate()
    {
        return parent::validate(self::SCHEMA);
    }

}

?>
