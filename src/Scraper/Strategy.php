<?php

/*
 * This file is part of the Thingiverse Zip Downloader.
 *
 * (c) ThomasBoom89 <51998416+ThomasBoom89@users.noreply.github.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ThomasBoom89\ThingiverseZipDownloader\Scraper;

use ThomasBoom89\ThingiverseZipDownloader\Dto\ThingiverseModel;

interface Strategy
{
    public function getThingiverseModel(string $url, string $modelId): ThingiverseModel;
}
