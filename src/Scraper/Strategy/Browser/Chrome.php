<?php

/*
 * This file is part of the Thingiverse Zip Downloader.
 *
 * (c) ThomasBoom89 <51998416+ThomasBoom89@users.noreply.github.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace ThomasBoom89\ThingiverseZipDownloader\Scraper\Strategy\Browser;

use DOMDocument;
use Exception;
use HeadlessChromium\BrowserFactory;
use HeadlessChromium\Page;
use ThomasBoom89\ThingiverseZipDownloader\Exception\HtmlEmpty;

class Chrome
{
    /**
     * @param string $url
     * @return DOMDocument
     * @throws HtmlEmpty
     */
    public function getDomDocumentFromUrl(string $url): DOMDocument
    {
        // On alpine use chromium-browser
        $browserFactory = new BrowserFactory('chromium-browser');
//        $browserFactory = new BrowserFactory('chromium');

        // starts headless chrome
        $browser = $browserFactory->createBrowser(['noSandbox' => true, 'enableImages' => false]);

        $html = '';

        try {
            // creates a new page and navigate to an URL
            $page = $browser->createPage();
            $page->navigate($url)
                 ->waitForNavigation(Page::NETWORK_IDLE);
            $html = $page->getHtml();
        } catch (Exception $e) {
            var_dump($e->getMessage());
        } finally {
            // bye
            $browser->close();
        }
        $domDocument = new DomDocument;
        if (@$domDocument->loadHTML($html) === false) {
            throw new HtmlEmpty();
        }

        return $domDocument;
    }
}
