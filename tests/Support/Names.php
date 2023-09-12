<?php
/**
 * @copyright Copyright © 2023 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

namespace BeastBytes\OrdnanceSurvey\Tests\Support;

use BeastBytes\OrdnanceSurvey\OrdnanceSurvey;
use RuntimeException;

/**
 * Use the {@link https://osdatahub.os.uk/docs/names/overview Ordnance Survey Names API} for tests
 */
final class Names extends OrdnanceSurvey
{
    public const LATLNG2BNG_EXCEPTION = 'Unable to convert Lat/Lng to BNG';
    private const API_NAME = 'search/names';
    private const API_VERSION = 'v1';

    /**
     * Returns the closest address to a given point.
     *
     * @param string $key API key
     * @param array<string, float> $point ['lat' => lat, 'lon' => lon]
     * @return array|bool|null Response data on success, FALSE on error
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function nearest(string $key, array $point): array|bool|null
    {
        $bng = self::LatLng2Bng($point);
        if ($bng === false) {
            throw new RuntimeException(self::LATLNG2BNG_EXCEPTION);
        }

        $qry = ['key' => $key];
        $qry['point'] = implode(',', $bng);

        return self::get(self::API_NAME . '/' . self::API_VERSION . '/nearest', $qry);
    }
}
