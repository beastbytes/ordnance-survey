<?php
/**
 * @copyright Copyright © 2023 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\OrdnanceSurvey\Tests;

use BeastBytes\OrdnanceSurvey\Tests\Support\Names;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class OrdnanceSurveyTest extends TestCase
{
    private const GAZETTEER_ENTRY = 'GAZETTEER_ENTRY';

    private static string $apiKey;

    public static function setUpBeforeClass(): void
    {
        self::$apiKey = require __DIR__ . '/Support/apiKey.php';
    }

    public static function pointProvider(): Generator
    {
        foreach ([
            'Heathrow' => [
                'point' => ['lat' => 51.47121514468652, 'lon' => -0.45364817429284376],
                'bng' => [507501, 175828]
            ],
            'Winter Gardens, Blackpool' =>  [
                'point' => ['lat' => 53.8171606460015, 'lon' => -3.0510696987209664],
                'bng' => [330898, 436166]
            ],
            'Canterbury Cathedral' => [
                'point' => ['lat' => 51.27989675153937, 'lon' => 1.083149997917232],
                'bng' => [615119, 157932]
            ],
        ] as $name => $data) {
            yield $name => $data;
        }
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    #[dataProvider('pointProvider')]
    public function testLatLng2Bng(array $point, array $bng): void
    {
        $this->assertSame($bng, Names::LatLng2Bng($point));
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    #[dataProvider('pointProvider')]
    public function testGet(array $point): void
    {
        $result = Names::nearest(self::$apiKey, $point);

        $this->assertIsArray($result);
        $this->arrayHasKey(self::GAZETTEER_ENTRY, $result[0]);
        $this->assertIsArray($result[0][self::GAZETTEER_ENTRY]);
    }
}
