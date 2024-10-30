<?php


namespace Base\Utils;

use WP_Filesystem_Base;

class Mrf_Filesystem_Wrapper {

	/** @var WP_Filesystem_Base */
	private $wp_filesystem;

	public function __construct() {
		global $wp_filesystem;
		if ( empty( $wp_filesystem ) ) {
			require_once( ABSPATH . '/wp-admin/includes/file.php' );
			WP_Filesystem();
		}
		$this->wp_filesystem = $wp_filesystem;
	}

	protected function is_direct() {
		return get_class( $this->wp_filesystem ) == 'WP_Filesystem_Direct';
	}

	public function abspath() {
		return $this->abspath();
	}

	public function wp_content_dir() {
		return $this->wp_filesystem->wp_content_dir();
	}

	public function wp_plugins_dir() {
		return $this->wp_filesystem->wp_plugins_dir();
	}

	public function wp_themes_dir( $theme = false ) {
		return $this->wp_filesystem->wp_themes_dir( $theme );
	}

	public function wp_lang_dir() {
		return $this->wp_filesystem->wp_lang_dir();
	}

	public function find_folder( $folder ) {
		return $this->wp_filesystem->find_folder( $folder );
	}

	public function search_for_folder( $folder, $base = '.', $loop = false ) {
		return $this->wp_filesystem->search_for_folder( $folder, $base, $loop );
	}

	public function getchmod( $file ) {
		return $this->wp_filesystem->getchmod( $file );
	}

	public function getnumchmodfromh( $mode ) {
		return $this->wp_filesystem->getnumchmodfromh( $mode );
	}

	public function is_binary( $text ) {
		return $this->wp_filesystem->is_binary( $text );
	}

	public function chown( $file, $owner, $recursive = false ) {
		return $this->wp_filesystem->chown( $file, $owner, $recursive );
	}

	public function connect() {
		return $this->wp_filesystem->connect();
	}

	public function get_contents( $file ) {
		if ( file_exists( $file ) ) {
			if ( $this->is_direct() ) {
				return $this->wp_filesystem->get_contents( $file );
			}

			return file_get_contents( $file ); // @codingStandardsIgnoreLine
		}

		return null;
	}

	public function get_contents_array( $file ) {
		if ( $this->is_direct() ) {
			return $this->wp_filesystem->get_contents_array( $file );
		}

		return explode( "\n", $this->get_contents( $file ) );
	}

	public function put_contents( $file, $contents, $mode = false ) {
		if ( $this->is_direct() ) {
			return $this->wp_filesystem->put_contents( $file, $contents, $mode );
		}

		return file_put_contents( $file, $contents, $mode ); // @codingStandardsIgnoreLine
	}

	public function cwd() {
		return $this->wp_filesystem->cwd();
	}

	public function chdir( $dir ) {
		return $this->wp_filesystem->chdir( $dir );
	}

	public function chgrp( $file, $group, $recursive = false ) {
		return $this->wp_filesystem->chgrp( $file, $group, $recursive );
	}

	public function chmod( $file, $mode = false, $recursive = false ) {
		return $this->wp_filesystem->chmod( $file, $mode, $recursive );
	}

	public function owner( $file ) {
		return $this->wp_filesystem->owner( $file );
	}

	public function group( $file ) {
		return $this->wp_filesystem->group( $file );
	}

	public function copy( $source, $destination, $overwrite = false, $mode = false ) {
		return $this->wp_filesystem->copy( $source, $destination, $overwrite, $mode );
	}

	public function move( $source, $destination, $overwrite = false ) {
		return $this->wp_filesystem->move( $source, $destination, $overwrite );
	}

	public function delete( $file, $recursive = false, $type = false ) {
		return $this->wp_filesystem->delete( $file, $recursive, $type );
	}

	public function exists( $file ) {
		if ( $this->is_direct() ) {
			return $this->wp_filesystem->exists( $file );
		}

		return file_exists( $file );
	}

	public function is_file( $file ) {
		if ( $this->is_direct() ) {
			return $this->wp_filesystem->is_file( $file );
		}

		return is_file( $file );
	}

	public function is_dir( $path ) {
		if ( $this->is_direct() ) {
			return $this->wp_filesystem->is_dir( $path );
		}

		return is_dir( $path );
	}

	public function is_readable( $file ) {
		return $this->wp_filesystem->is_readable( $file );
	}

	public function is_writable( $file ) {
		return $this->wp_filesystem->is_writable( $file );
	}

	public function atime( $file ) {
		return $this->wp_filesystem->atime( $file );
	}

	public function mtime( $file ) {
		return $this->wp_filesystem->mtime( $file );
	}

	public function size( $file ) {
		return $this->wp_filesystem->size( $file );
	}

	public function touch( $file, $time = 0, $atime = 0 ) {
		if ( $this->is_direct() ) {
			return $this->wp_filesystem->touch( $file, $time, $atime );
		}

		return touch( $file, $time, $atime );
	}

	public function mkdir( $path, $chmod = false, $chown = false, $chgrp = false ) {
		return $this->wp_filesystem->mkdir( $path, $chmod, $chown, $chgrp );
	}

	public function rmdir( $path, $recursive = false ) {
		return $this->wp_filesystem->rmdir( $path, $recursive );
	}

	public function dirlist( $path, $include_hidden = true, $recursive = false ) {
		return $this->wp_filesystem->dirlist( $path, $include_hidden, $recursive );
	}

}
