<?php

namespace Tests;

use Brain\Monkey;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

abstract class TestCase extends PHPUnitTestCase {
	use MockeryPHPUnitIntegration;

	protected function loadPlugin( string $plugin ): void {
		require dirname( __DIR__ ) . '/plugins/' . $plugin . '.php';
	}

	protected function runPhp( string $code ): string {
		$pipes = [];
		$process = proc_open(
			[ PHP_BINARY, '-r', $code ],
			[ 0 => [ 'pipe', 'r' ], 1 => [ 'pipe', 'w' ], 2 => [ 'pipe', 'w' ] ],
			$pipes,
			dirname( __DIR__ )
		);

		fclose( $pipes[0] );
		$output = stream_get_contents( $pipes[1] );
		$error = stream_get_contents( $pipes[2] );
		fclose( $pipes[1] );
		fclose( $pipes[2] );

		if ( proc_close( $process ) !== 0 ) {
			throw new \RuntimeException( $error );
		}

		return trim( $output );
	}

	protected function setUp(): void {
		parent::setUp();
		Monkey\setUp();
	}

	protected function tearDown(): void {
		Monkey\tearDown();
		parent::tearDown();
	}
}
