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

require_once '../vendor/autoload.php';

use ThomasBoom89\ThingiverseZipDownloader\Downloader;
use ThomasBoom89\ThingiverseZipDownloader\InputParser;
use ThomasBoom89\ThingiverseZipDownloader\InputValidator;
use ThomasBoom89\ThingiverseZipDownloader\Scraper\Strategy\HttpApi;

$inputValidator = new InputValidator();
$inputModel     = $_POST['model'];
if ($inputModel === '' || !$inputValidator->isValid($inputModel)) {
    return;
}

$inputParser = new InputParser();
$downloader  = new Downloader();

try {
    $url              = $inputParser->buildFilesUrl($inputModel);
    $http             = new HttpApi();
    $thingiverseModel = $http->getThingiverseModel($url, $inputParser->getModelIdFromUrl($url));
    $filename         = $thingiverseModel->name . '.zip';
    $downloader->toZipArchive($thingiverseModel, $filename);

    // Stream ZIPFile
    header('Content-Type: application/zip');
    header('Content-disposition: attachment; filename=' . $filename);
    header('Content-Length: ' . filesize($filename));
    flush();
    readfile($filename);
    // delete file
    unlink($filename);
} catch (Exception $e) {
    print_r(get_class($e));
    echo PHP_EOL;
    echo PHP_EOL;
    print_r($e->getTrace());
}

