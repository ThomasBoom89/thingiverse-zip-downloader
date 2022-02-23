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
use DOMNode;
use DOMXPath;
use ThomasBoom89\ThingiverseZipDownloader\Dto\Builder\DownloadLink as DownloadLinkBuilder;
use ThomasBoom89\ThingiverseZipDownloader\Dto\DownloadLink;
use ThomasBoom89\ThingiverseZipDownloader\Exception\FileNameNotFound;
use ThomasBoom89\ThingiverseZipDownloader\Exception\HrefNotFound;
use ThomasBoom89\ThingiverseZipDownloader\Exception\ModelNameNotFound;

class HtmlParser
{
    private DownloadLinkBuilder $downloadLinkBuilder;

    public function __construct()
    {
        $this->downloadLinkBuilder = new DownloadLinkBuilder();
    }

    /**
     * @param DOMDocument $domDocument
     * @return DownloadLink[]
     * @throws FileNameNotFound
     * @throws HrefNotFound
     */
    public function getDownloadLinksByHtml(DOMDocument $domDocument): array
    {
        $downloadLinks = [];
        $xpath         = new DomXPath($domDocument);
        $thingRows     = $xpath->query("//div[starts-with(@class, 'ThingFile__fileRow')]");

        foreach ($thingRows as $thingRow) {
            $linkList = $xpath->query(".//a[starts-with(@class, 'ThingFile')]", $thingRow);
            if ($linkList === false) {

                continue;
            }
            $link = $linkList->item(0);
            if (!isset($link) || !$link->hasAttributes()) {
                continue;
            }
            $downloadUri = '';
            /**@var DOMNode $attribute */
            foreach ($link->attributes as $attribute) {
                if ($attribute->nodeName !== 'href') {
                    throw new HrefNotFound();
                }
                $downloadUri = str_replace(' ', '_', $attribute->nodeValue);
            }
            if (!isset($downloadUri)) {
                continue;
            }
            $filename = $this->getNameFromRow($xpath, $thingRow);
            $this->downloadLinkBuilder->setDownloadLink(['name' => $filename, 'direct_url' => $downloadUri]);
            $downloadLinks[] = $this->downloadLinkBuilder->build();
        }

        return $downloadLinks;
    }


    /**
     * @throws ModelNameNotFound
     */
    public function getModelName(DOMDocument $domDocument): string
    {
        $xpath = new DomXPath($domDocument);
        $model = $xpath->query("//div[starts-with(@class, 'ThingPage__modelName')]");

        if ($model === false) {

            throw new ModelNameNotFound();
        }

        return preg_replace('/[^A-Za-z0-9_\-]/', '_', $model->item(0)->nodeValue);
    }

    /**
     * @throws FileNameNotFound
     */
    private function getNameFromRow(DOMXPath $xpath, DOMNode $row): string
    {
        $name = $xpath->query(".//div[starts-with(@class, 'ThingFile__fileName')]", $row);
        if ($name === false) {

            throw new FileNameNotFound();
        }

        return preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $name->item(0)->nodeValue);
    }
}
