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

use JsonException;
use ThomasBoom89\ThingiverseZipDownloader\Dto\Builder\DownloadLink as DownloadLinkBuilder;
use ThomasBoom89\ThingiverseZipDownloader\Dto\Builder\ThingiverseModel as ThingiverseModelBuilder;
use ThomasBoom89\ThingiverseZipDownloader\Dto\ThingiverseModel;
use ThomasBoom89\ThingiverseZipDownloader\Exception\BearerTokenNotFound;
use ThomasBoom89\ThingiverseZipDownloader\Exception\HtmlEmpty;
use ThomasBoom89\ThingiverseZipDownloader\Exception\JavascriptNotDownloadable;
use ThomasBoom89\ThingiverseZipDownloader\Exception\ModelNameNotFound;
use ThomasBoom89\ThingiverseZipDownloader\Exception\NoResponseFromThingiverseApi;
use ThomasBoom89\ThingiverseZipDownloader\Scraper\Strategy;

class HttpApi implements Strategy
{
    /** @var string */
    private const THINGIVERSE_APP_BUNDLE_URL = 'https://cdn.thingiverse.com/site/js/app.bundle.js?';
    private const THINGIVERSE_FILE_API_URL   = 'https://api.thingiverse.com/things/%s/files';
    private const HEADER_BEARER              = 'Authorization: Bearer %s';

    private ThingiverseModelBuilder $thingiverseModelBuilder;
    private DownloadLinkBuilder     $downloadLinkBuilder;

    public function __construct()
    {
        $this->thingiverseModelBuilder = new ThingiverseModelBuilder();
        $this->downloadLinkBuilder     = new DownloadLinkBuilder();
    }

    /**
     * @param string $url
     * @param string $modelId
     * @return ThingiverseModel
     * @throws BearerTokenNotFound
     * @throws HtmlEmpty
     * @throws JavascriptNotDownloadable
     * @throws JsonException
     * @throws ModelNameNotFound
     * @throws NoResponseFromThingiverseApi
     */
    public function getThingiverseModel(string $url, string $modelId): ThingiverseModel
    {
        // TODO: Remove quickfix
        $url = substr($url, 0, -6);

        $html          = $this->getHtml($url);
        $bundleVersion = $this->getJavascriptAppBundleVersion($html);
        $modelName     = $this->getModelName($html);
        // TODO: check for bundle version and cache token
        $javascript    = $this->getJavascriptBundleCode($bundleVersion);
        $token         = $this->getBearerToken($javascript);
        $downloadLinks = $this->getLinksFromApi($token, $modelId);
        $this->thingiverseModelBuilder->setThingiverseModel($modelName, $token, $downloadLinks);

        return $this->thingiverseModelBuilder->build();
    }

    /**
     * @param string $html
     * @return string
     * @throws JavascriptNotDownloadable
     */
    private function getJavascriptAppBundleVersion(string $html): string
    {
        // matches[0] = full url
        // matches[1] = bundle number
        if (!preg_match('/https:\/\/cdn\.thingiverse\.com\/site\/js\/app\.bundle\.js\?(\d+)/', $html, $matches)
            || !isset($matches[1])
        ) {
            throw new JavascriptNotDownloadable();
        }

        return $matches[1];
    }

    /**
     * @param string $url
     * @return string
     * @throws HtmlEmpty
     */
    private function getHtml(string $url): string
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $html = curl_exec($curl);

        if ($html === false) {
            throw new HtmlEmpty();
        }

        return $html;
    }

    /**
     * @throws BearerTokenNotFound
     */
    private function getBearerToken(string $javascript): string
    {
        if (!preg_match('/u=\"([a-z0-9]*)\"/', $javascript, $matches) || !isset($matches[1])) {
            throw new BearerTokenNotFound();
        }

        return $matches[1];
    }

    /**
     * @param string $version
     * @return string
     * @throws JavascriptNotDownloadable
     */
    private function getJavascriptBundleCode(string $version): string
    {
        $curl = curl_init(self::THINGIVERSE_APP_BUNDLE_URL . $version);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $javascript = curl_exec($curl);

        if ($javascript === false) {
            throw new JavascriptNotDownloadable();
        }

        return $javascript;
    }

    /**
     * @param string $html
     * @return string
     * @throws ModelNameNotFound
     */
    private function getModelName(string $html): string
    {
        if (!preg_match('/>(.*)by/', $html, $matches) || !isset($matches[1])) {
            throw new ModelNameNotFound();
        }

        return preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $matches[1]);
    }

    /**
     * @param string $token
     * @param string $modelId
     * @return array
     * @throws JsonException
     * @throws NoResponseFromThingiverseApi
     */
    private function getLinksFromApi(string $token, string $modelId): array
    {
        $curl = curl_init(sprintf(self::THINGIVERSE_FILE_API_URL, $modelId));
        $auth = sprintf(self::HEADER_BEARER, $token);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [$auth]);
        curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt(
            $curl,
            CURLOPT_USERAGENT,
            'User-Agent: Mozilla/5.0 (X11; Linux x86_64; rv:97.0) Gecko/20100101 Firefox/97.0'
        );
        $response = curl_exec($curl);

        if (curl_getinfo($curl, CURLINFO_HTTP_CODE) !== 200) {
            throw new NoResponseFromThingiverseApi();
        }
        $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $body       = substr($response, $headerSize);

        $files         = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        $downloadLinks = [];
        foreach ($files as $file) {
            if (!array_key_exists('name', $file)
                || (!array_key_exists('direct_url', $file) && !array_key_exists('download_url', $file))
            ) {
                continue;
            }
            $this->downloadLinkBuilder->setDownloadLink($file);
            $downloadLinks[] = $this->downloadLinkBuilder->build();
        }

        return $downloadLinks;
    }
}
