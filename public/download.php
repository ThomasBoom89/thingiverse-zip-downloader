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
use ThomasBoom89\ThingiverseZipDownloader\HtmlParser;
use ThomasBoom89\ThingiverseZipDownloader\InputParser;
use ThomasBoom89\ThingiverseZipDownloader\InputValidator;
use ThomasBoom89\ThingiverseZipDownloader\Scraper;

$inputValidator = new InputValidator();
$scraper        = new Scraper();
$htmlParser     = new HtmlParser();
$inputParser    = new InputParser();
$downloader     = new Downloader();

$inputModel = $_POST['model'];
if (!$inputValidator->isValid($inputModel)) {
    return;
}

try {
    $url         = $inputParser->buildFilesUrl($inputModel);
    $domDocument = $scraper->getDomDocumentFromUrl($url);
    $links       = $htmlParser->getDownloadLinksByHtml($domDocument);
    $filename    = $htmlParser->getModelName($domDocument) . '.zip';
    $downloader->toZipArchive($links, $filename);
    // Stream ZIPFile
    header('Content-Type: application/zip');
    header('Content-disposition: attachment; filename=' . $filename);
    header('Content-Length: ' . filesize($filename));
    flush();
    readfile($filename);
    // delete file
    unlink($filename);
} catch (Exception $e) {
    print_r($e->getTrace());
}

