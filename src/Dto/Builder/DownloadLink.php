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

namespace ThomasBoom89\ThingiverseZipDownloader\Dto\Builder;

use ThomasBoom89\ThingiverseZipDownloader\Dto\DownloadLink as ValueObject;
use ThomasBoom89\ThingiverseZipDownloader\Dto\DtoBuilder;

class DownloadLink implements DtoBuilder
{

    /** @var array */
    private array $downloadLink;

    /**
     * @param array $downloadLink
     * @return void
     */
    public function setDownloadLink(array $downloadLink): void
    {
        $this->downloadLink = $downloadLink;
    }

    /**
     * @return ValueObject
     */
    public function build(): ValueObject
    {
        $downloadLinkDto              = new ValueObject();
        $downloadLinkDto->filename    = $this->downloadLink['name'];
        $downloadLinkDto->downloadUri = $this->downloadLink['direct_url'] ?? $this->downloadLink['download_url'];

        return $downloadLinkDto;
    }
}
