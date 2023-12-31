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

use const PHP_ROUND_HALF_EVEN;
use function \round;

/**
 * Base class for {@link https://osdatahub.os.uk/ Ordnance Survey APIs}
 */
abstract class OrdnanceSurvey
{
    public const BASE_URI = 'https://api.os.uk/';
    private const RESPONSE_STATUS_OK = 200;

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
        $query = $latLng;
        $query['method'] = 'LatLongToBNG';

        self::$response = (new Client())
            ->get(
                'https://webapps.bgs.ac.uk/data/webservices/CoordConvert_LL_BNG.cfc',
                [
                    RequestOptions::QUERY => $query
                ]
            )
        ;

        if (self::$response->getStatusCode() === self::RESPONSE_STATUS_OK) {
            $result = Utils::jsonDecode(self::$response->getBody(), true);

            return [
                (int)round($result['EASTING'], 0, PHP_ROUND_HALF_EVEN),
                (int)round($result['NORTHING'], 0, PHP_ROUND_HALF_EVEN)
            ];
        }

        return false;
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected static function get(string $uri, array $query): bool|array|null
    {
        self::$response = (new Client(['base_uri' => self::BASE_URI]))
            ->get(
                $uri,
                [
                    RequestOptions::QUERY => $query,
                ]
            )
        ;

        if (self::$response->getStatusCode() === self::RESPONSE_STATUS_OK) {
            $result = Utils::jsonDecode(self::$response->getBody(), true);
            return $result['results'];
        }

        return false;
    }
}
