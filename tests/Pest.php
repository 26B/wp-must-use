<?php

require_once __DIR__ . '/TestCase.php';

define( 'ABSPATH', __DIR__ . '/Fixtures/wordpress/' );
define( 'WPMU_PLUGIN_DIR', __DIR__ . '/Fixtures/mu-plugins' );

uses( Tests\TestCase::class )->in( '.' );
