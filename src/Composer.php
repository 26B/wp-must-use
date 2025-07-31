<?php

namespace TSB\WP\MUPlugin;

/**
 * Use the necessary namespaces.
 */
use Composer\Composer as ComposerComposer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\EventDispatcher\EventSubscriberInterface;


class Composer implements PluginInterface, EventSubscriberInterface {

	/**
	 * Holds the extras array for the root Composer project.
	 * @var Array
	 */
	private $extras;

	/**
	 * Stores the extras array and config object for later use.
	 *
	 * @param Composer    $composer The main Composer object.
	 * @param IOInterface $io       The I/O Helper object.
	 * @return void
	 */
	public function activate( ComposerComposer $composer, IOInterface $io ) {
		$this->extras = $composer->getPackage()->getExtra();
	}

	/**
	 * Required by PluginInterface: Deactivate the plugin.
	 *
	 * @param Composer    $composer The main Composer object.
	 * @param IOInterface $io       The I/O Helper object.
	 * @return void
	 */
	public function deactivate( ComposerComposer $composer, IOInterface $io ) {
		// No action needed for this plugin.
	}

	/**
	 * Required by PluginInterface: Uninstall the plugin.
	 *
	 * @param Composer    $composer The main Composer object.
	 * @param IOInterface $io       The I/O Helper object.
	 * @return void
	 */
	public function uninstall( ComposerComposer $composer, IOInterface $io ) {
		// No action needed for this plugin.
	}
	/**
	 * Subscribes to autoload dump and package install events.
	 *
	 * When `pre-autoload-dump` fires, run the `dumpRequireFile` method.
	 * When `pre-package-install` fires, run the `overridePluginTypes` method.
	 *
	 * @return array The event subscription map.
	 */
	public static function getSubscribedEvents() {
		return array(
			'post-package-install'  => 'copyMUPlugins',
			'post-package-update'   => 'copyMUPlugins',
			'pre-package-uninstall' => 'deleteMUPlugins',
		);
	}

	public function copyMUPlugins() : void {
		echo "Copying 26b/wp-must-use mu-plugins...\n";
		$local_plugins_path = dirname( __DIR__ ) . '/plugins/';
		$mu_plugins_path    = $this->findMURelPath();

		echo $local_plugins_path . "\n";
		echo $mu_plugins_path . "\n";

		if ( ! is_dir( $local_plugins_path ) ) {
			echo "Local plugins path not found: $local_plugins_path\n";
			return;
		}

		if ( ! is_string( $mu_plugins_path ) || empty( $mu_plugins_path ) ) {
			echo "Mu-plugins path not found: $mu_plugins_path\n";
			return;
		}

		// Copy all PHP files from local_plugins_path to mu_plugins_path
		foreach ( glob( $local_plugins_path . '*.php' ) as $file ) {
			$dest = rtrim( $mu_plugins_path, '/\\' ) . '/' . basename( $file );
			if ( copy( $file, $dest ) ) {
				echo "Copied $file to $dest\n";
			} else {
				echo "Failed to copy $file\n";
			}
		}
	}

	public function deleteMUPlugins() : void {
		echo "Deleting 26b/wp-must-use mu-plugins...\n";
		$local_plugins_path = dirname( __DIR__ ) . '/plugins/';
		$mu_plugins_path    = $this->findMURelPath();

		if ( ! is_dir( $local_plugins_path ) ) {
			echo "Local plugins path not found: $local_plugins_path\n";
			return;
		}

		if ( ! is_string( $mu_plugins_path ) || empty( $mu_plugins_path ) ) {
			echo "Mu-plugins path not found: $mu_plugins_path\n";
			return;
		}

		// Delete all PHP files in $local_plugins_path found in $mu_plugins_path.
		foreach ( glob( $local_plugins_path . '*.php' ) as $file ) {
			$target = rtrim( $mu_plugins_path, '/\\' ) . '/' . basename( $file );
			if ( file_exists( $target ) ) {
				if ( unlink( $target ) ) {
					echo "Deleted $target\n";
				} else {
					echo "Failed to delete $target\n";
				}
			} else {
				echo "File not found: $target\n";
			}
		}
	}

	protected function findMURelPath() {
		$path = false;
		// Only keep going if we have install-paths in extras.
		if ( empty( $this->extras['installer-paths'] ) || ! is_array( $this->extras['installer-paths'] ) ) {
			return false;
		}
		// Find the array to the mu-plugin path.
		foreach ( $this->extras['installer-paths'] as $path => $types ) {
			if ( ! is_array( $types ) ) {
				continue;
			}
			if ( ! in_array( 'type:wordpress-muplugin', $types ) ) {
				continue;
			}
			$path = str_replace( '{$name}/', '', $path );
			break;
		}
		return $path;
	}
}
