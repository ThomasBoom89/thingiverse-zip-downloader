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

namespace ThomasBoom89\ThingiverseZipDownloader;

use ThomasBoom89\ThingiverseZipDownloader\Dto\DownloadLink;
use ZipArchive;

class Downloader
{
    /**
     * @param DownloadLink[] $files
     * @param string $zipname
     * @return void
     */
    public function toZipArchive(array $files, string $zipname): void
    {
        $zipArchive = new ZipArchive;
        $zipArchive->open($zipname, ZipArchive::CREATE);
        foreach ($files as $file) {
            $zipArchive->addFromString($file->filename, file_get_contents($file->downloadUri));
        }
        $zipArchive->close();
    }
}
