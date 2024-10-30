<?php

namespace Ads_Txt\Managers;

use Ioc\Marfeel_Press_App;
use Base\Utils\Mrf_Filesystem_Wrapper;

class Marfeel_Ads_Txt_File_Manager extends Marfeel_Ads_Txt_Manager {

	const HAD_FILE_KEY = 'ads.ads_txt.had_file';
	const CONTENT_KEY = 'ads.ads_txt.content';

	/** @var string */
	private $file_path;

	/** @var string */
	private $backup_file;

	/** @var Mrf_Filesystem_Wrapper */
	private $filesystem;

	public function __construct() {
		$this->file_path = ABSPATH . 'ads.txt';
		$this->backup_file = ABSPATH . 'ads.mrf.txt';
		$this->filesystem = Marfeel_Press_App::make( 'filesystem_wrapper' );
	}

	public function file_exists() {
		return $this->filesystem->exists( $this->file_path );
	}

	public function backup_exists() {
		return $this->filesystem->exists( $this->backup_file );
	}

	public function is_valid() {
		return $this->filesystem->is_writable( ABSPATH );
	}

	public function get_contents() {
		return $this->filesystem->get_contents( $this->file_path );
	}

	public function save( $lines, $force = false ) {
		Marfeel_Press_App::make( 'settings_service' )->set( 'ads.ads_txt.content_merged', $lines );

		if ( ! $this->backup_exists() && $force ) {
			$this->plugin_activated();
		} elseif ( $this->backup_exists() && $this->file_exists() ) {
			if ( is_array( $lines ) ) {
				$lines = implode( "\n", $lines );
			}

			$this->filesystem->put_contents( $this->file_path, $lines );
		}
	}

	public function plugin_activated() {
		parent::plugin_activated();

		$file_exists = $this->file_exists();
		Marfeel_Press_App::make( 'settings_service' )->set( self::HAD_FILE_KEY, $file_exists );

		if ( $file_exists ) {
			$this->filesystem->copy( $this->file_path, $this->backup_file, true );
		} else {
			$this->filesystem->touch( $this->backup_file );
		}

		$this->filesystem->put_contents( $this->file_path, Marfeel_Press_App::make( 'settings_service' )->get( 'ads.ads_txt.content_merged' ) );
	}

	public function plugin_deactivated() {
		$had_file = Marfeel_Press_App::make( 'settings_service' )->get( self::HAD_FILE_KEY );

		if ( $had_file !== null ) {
			if ( ! $had_file ) {
				$this->filesystem->delete( $this->file_path );
			} else {
				$this->filesystem->put_contents( $this->file_path, Marfeel_Press_App::make( 'settings_service' )->get( self::CONTENT_KEY ) );
			}
		}
	}
}
