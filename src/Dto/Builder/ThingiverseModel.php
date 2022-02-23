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

use ThomasBoom89\ThingiverseZipDownloader\Dto\DtoBuilder;
use ThomasBoom89\ThingiverseZipDownloader\Dto\DownloadLink;
use ThomasBoom89\ThingiverseZipDownloader\Dto\ThingiverseModel as ValueObject;

class ThingiverseModel implements DtoBuilder
{

    /** @var string */
    private string $name;

    /** @var string */
    private string $token;

    /** @var DownloadLink[] */
    private array $downloadLinks;

    /**
     * @param string $name
     * @param string $token
     * @param DownloadLink[] $downloadLinks
     * @return void
     */
    public function setThingiverseModel(string $name, string $token, array $downloadLinks): void
    {
        $this->name          = $name;
        $this->token         = $token;
        $this->downloadLinks = $downloadLinks;
    }

    /**
     * @return ValueObject
     */
    public function build(): ValueObject
    {
        $thingiverseModelDto                = new ValueObject();
        $thingiverseModelDto->name          = $this->name;
        $thingiverseModelDto->token         = $this->token;
        $thingiverseModelDto->downloadLinks = $this->downloadLinks;

        return $thingiverseModelDto;
    }
}
