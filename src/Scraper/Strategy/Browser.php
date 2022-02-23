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

namespace ThomasBoom89\ThingiverseZipDownloader\Scraper\Strategy;

use ThomasBoom89\ThingiverseZipDownloader\Dto\Builder\ThingiverseModel as ThingiverseModelBuilder;
use ThomasBoom89\ThingiverseZipDownloader\Dto\ThingiverseModel;
use ThomasBoom89\ThingiverseZipDownloader\Exception\FileNameNotFound;
use ThomasBoom89\ThingiverseZipDownloader\Exception\HrefNotFound;
use ThomasBoom89\ThingiverseZipDownloader\Exception\HtmlEmpty;
use ThomasBoom89\ThingiverseZipDownloader\Exception\ModelNameNotFound;
use ThomasBoom89\ThingiverseZipDownloader\Scraper\Strategy;
use ThomasBoom89\ThingiverseZipDownloader\Scraper\Strategy\Browser\Chrome;
use ThomasBoom89\ThingiverseZipDownloader\Scraper\Strategy\Browser\HtmlParser;

class Browser implements Strategy
{
    private Chrome                  $chrome;
    private HtmlParser              $parser;
    private ThingiverseModelBuilder $thingiverseModelBuilder;

    public function __construct()
    {
        $this->chrome                  = new Chrome();
        $this->parser                  = new HtmlParser();
        $this->thingiverseModelBuilder = new ThingiverseModelBuilder();
    }

    /**
     * @throws ModelNameNotFound
     * @throws FileNameNotFound
     * @throws HtmlEmpty|HrefNotFound
     */
    public function getThingiverseModel(string $url, string $modelId): ThingiverseModel
    {
        $domDocument   = $this->chrome->getDomDocumentFromUrl($url);
        $downloadLinks = $this->parser->getDownloadLinksByHtml($domDocument);
        $modelName     = $this->parser->getModelName($domDocument);
        $this->thingiverseModelBuilder->setThingiverseModel($modelName, '', $downloadLinks);

        return $this->thingiverseModelBuilder->build();
    }
}
