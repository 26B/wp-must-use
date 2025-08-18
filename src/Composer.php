<?php

namespace TSB\WP\MUPlugin;

use Composer\Composer as ComposerComposer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\EventDispatcher\EventSubscriberInterface;

/**
 * Composer plugin to handle mu-plugins.
 *
 * @since 0.1.0
 */
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
	 * @since 0.1.0
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
	 * @since 0.1.0
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
	 * @since 0.1.0
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

	/**
	 * Copies mu-plugins from the local plugins directory to the mu-plugins directory.
	 *
	 * @since 0.1.0
	 *
	 * @param \Composer\EventDispatcher\GenericEvent $event The event object.
	 * @return void
	 */
	public function copyMUPlugins( $event ) : void {
		// Get the package being worked on.
		$operation = $event->getOperation();
		if ( $operation instanceof \Composer\DependencyResolver\Operation\UpdateOperation ) {
			$package = $operation->getInitialPackage();
		} else {
			$package = $operation->getPackage();
		}

		if ( $package->getName() !== '26b/wp-must-use' ) {
			// Not the right package, skip.
			return;
		}

		echo "Copying 26b/wp-must-use mu-plugins...\n";
		$local_plugins_path = dirname( __DIR__ ) . '/plugins/';
		$mu_plugins_path    = $this->findMURelPath();

		// Check if the plugin repository directory exits.
		if ( ! is_dir( $local_plugins_path ) ) {
			echo "Local plugins path not found: $local_plugins_path\n";
			return;
		}

		// Check if the path is defined.
		if ( ! is_string( $mu_plugins_path ) || empty( $mu_plugins_path ) ) {
			echo "Mu-plugins path not found: $mu_plugins_path\n";
			return;
		}

		// Check for a file with the same name as the expected directory.
		if ( file_exists( $mu_plugins_path ) && ! is_dir( $mu_plugins_path ) ) {
			echo "There is a file with the mu-plugins folder name: $mu_plugins_path\n";
			return;
		}

		// There is no directory to copy to.
		if ( ! file_exists( $mu_plugins_path ) && ! is_dir( $mu_plugins_path ) {
				mkdir( $mu_plugins_path, 0755, true );
		}

		// Copy all PHP files from local_plugins_path to mu_plugins_path
		foreach ( glob( $local_plugins_path . '*.php' ) as $file ) {
			$dest = rtrim( $mu_plugins_path, '/\\' ) . '/' . basename( $file );
			if ( ! copy( $file, $dest ) ) {
				echo "Failed to copy $file\n";
			}
		}

		echo "26b/wp-must-use mu-plugins copied.\n";
	}

	/**
	 * Deletes mu-plugins from the mu-plugins directory.
	 *
	 * @since 0.1.0
	 *
	 * @param \Composer\EventDispatcher\GenericEvent $event The event object.
	 * @return void
	 */
	public function deleteMUPlugins( $event ) : void {
		// Get the package being worked on.
		$operation = $event->getOperation();
		$package   = $operation->getPackage();

		if ( $package->getName() !== '26b/wp-must-use' ) {
			// Not the right package, skip.
			return;
		}

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
				if ( ! unlink( $target ) ) {
					echo "Failed to delete $target\n";
				}
			} else {
				echo "File not found: $target\n";
			}
		}

		echo "26b/wp-must-use mu-plugins deleted.\n";
	}

	/**
	 * Finds the relative path to the mu-plugins directory.
	 *
	 * @since 0.1.0
	 *
	 * @return string|false The relative path to the mu-plugins directory, or false if not found.
	 */
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
