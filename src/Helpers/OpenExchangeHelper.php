<?php
namespace Cyberinfomatic\UltimateCryptoWidget\Helpers;

use Cyberinfomatic\UltimateCryptoWidget\Controllers\Settings;

class OpenExchangeHelper extends APIHelper {

	const HOST = 'https://openexchangerates.org/api';

	static function get_api_key() {
		return Settings::get('openexchangerates_app_id');
	}

	const ENDPOINTS = [
		'convert' => [
			'route' => '/convert/{{value}}/{{from}}/{{to}}',
			'method' => 'GET',
			'params' => [
				'app_id' => [self::class, 'get_api_key'],
				'prettyprint' => true
			],
			'substitutions' => [
				'value', 'from', 'to'
			]
		],
		'latest' => [
			'route' => '/latest.json',
			'method' => 'GET',
			'params' => [
				'app_id' => [self::class, 'get_api_key'],
				'prettyprint' => true,
				'bases' => 'USD',
				'symbols' => 'USD'
			]
		]
	];

	static function get_headers() {
		return [
			'accept' => 'application/json',
		];
	}

	static function convert($from, $to, int $value = 1): int {
		try {
			$data = self::make_request('convert', [], [
				'from'  => strtoupper($from),
				'to'    => strtoupper($to),
				'value' => intval($value)
			]);
			return $data['response'] ?? 1;
		} catch (\Exception $e) {
			return 0;
		}
	}

	public static function get_latest($pairs = ['USD']): array {
		try {
			$data = self::make_request('latest', [], [
				'symbols' => implode(',', $pairs)
			]);
			return $data['response'] ?? [];
		} catch (\Exception $e) {
			return [];
		}
	}

	static function getName(): string {
		return 'open_exchange';
	}

}
