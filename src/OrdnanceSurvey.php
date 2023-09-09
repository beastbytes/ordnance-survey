<?php
/**
 * @copyright Copyright © 2023 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

namespace BeastBytes\OrdnanceSurvey;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Utils;
use Psr\Http\Message\ResponseInterface;

/**
 * Base class for {@link https://osdatahub.os.uk/ Ordnance Survey APIs}
 */
abstract class OrdnanceSurvey
{
    public const BASE_URI = 'https://api.os.uk/search';
    private const RESPONSE_STATUS_OK = 200;

    /**
     * @var string Ordnance Survey API key
     */
    public $key;

    /**
     * @var ResponseInterface HTTP Client Response
     */
    protected static ResponseInterface $response;

    /**
     * Returns the HTTP Client Response object.
     * Useful if the Response is not OK.
     *
     * @return ResponseInterface HTTP Client Response object
     */
    public static function getResponse(): ResponseInterface
    {
        return self::$response;
    }

    /**
     * Converts latitude and longitude to British National Grid co-ordinates
     *
     * @param array $latLng Latitude and longitude (decimal) co-ordinates
     * @return array|bool|null Response data on success, FALSE on error
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function latLng2Bng(array $latLng): array|bool|null
    {
        $query = [
            'lat' => $latLng[0],
            'lon' => $latLng[1],
            'method' => 'LatLongToBNG'
        ];

        self::$response = self::client()
            ->get(
                'https://webapps.bgs.ac.uk/data/webservices/CoordConvert_LL_BNG.cfc',
                [
                    RequestOptions::QUERY => $query,
                ]
            )
        ;

        return self::getResult();
    }

    protected static function client(): Client
    {
        return new Client(['base_uri' => self::BASE_URI]);
    }

    protected static function getResult(): array|bool|null
    {
        if (self::$response->getStatusCode() === self::RESPONSE_STATUS_OK) {
            $result = Utils::jsonDecode(self::$response->getBody(), true);
            return $result['result'];
        }

        return false;
    }
}
