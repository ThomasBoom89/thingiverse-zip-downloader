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

class InputParser
{

    public function getModelIdFromUrl(string $url): string
    {
        preg_match('/\/thing:(\d*)/', $url, $matches);

        return $matches[1];
    }

    public function buildFilesUrl(string $input): string
    {
        if (preg_match('/^\d*$/', $input) === 1) {

            return 'https://www.thingiverse.com/thing:' . $input . '/files';
        }

        return $this->prepareUrl($input);
    }


    private function prepareUrl(string $url): string
    {
        if (str_ends_with($url, 'files')) {
            return $url;
        }

        if (str_ends_with($url, '/')) {
            return $url . 'files';
        }

        return $url . '/files';
    }
}
