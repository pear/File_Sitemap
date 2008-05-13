<?php
/* vim: set noai expandtab ts=4 st=4 sw=4: */

/**
 * Exception class used by File_Sitemap package.
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

require_once "PEAR/Exception.php";

/**
 * Exeption class for the File_Sitemap package.
 *
 * @category File
 * @package  File_Sitemap
 * @author   Charles Brunet <charles.fmj@gmail.com>
 * @license  http://www.opensource.org/licenses/bsd-license.html BSD License
 * @version  Release: 0.1.1
 * @link     http://pear.php.net/package/File_Sitemap
 */
class File_Sitemap_Exception extends PEAR_Exception
{

    /**
     * Misc errors. Can be added to HTTP response code. 1404 means page not
     * found.
     */
    const ERROR = 1000;

    /**
     * Error relative to argument parsing when adding data to sitemap.
     */
    const PARSE_ERROR = 2000;

    /**
     * File related error when reading or writing sitemap file.
     */
    const FILE_ERROR = 3000;
}

?>
