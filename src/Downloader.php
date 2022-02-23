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

use ThomasBoom89\ThingiverseZipDownloader\Dto\ThingiverseModel;
use ZipArchive;

class Downloader
{
    /**
     * @param ThingiverseModel $thingiverseModel
     * @param string $zipname
     * @return void
     */
    public function toZipArchive(ThingiverseModel $thingiverseModel, string $zipname): void
    {
        $contextOptions = [
            'http' => [
                "method" => 'GET',
            ]
        ];
        if ($thingiverseModel->token !== '') {
            $contextOptions['http']['header'] = 'Authorization: Bearer ' . $thingiverseModel->token;
        }
        $context    = stream_context_create($contextOptions);
        $zipArchive = new ZipArchive;
        $zipArchive->open($zipname, ZipArchive::CREATE);
        foreach ($thingiverseModel->downloadLinks as $file) {
            $zipArchive->addFromString($file->filename, file_get_contents($file->downloadUri, false, $context));
        }
        $zipArchive->close();
    }
}
