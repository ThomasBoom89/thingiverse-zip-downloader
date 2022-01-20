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

class InputValidator
{
    public function isValid(string $input): bool
    {
        return (bool)preg_match('/(https:\/\/www\.thingiverse\.com\/thing:\d+\/?(?:files)?\/?$)|(^\d+)/', $input);
    }
}
