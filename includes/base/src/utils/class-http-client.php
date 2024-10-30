<?php


namespace Base\Utils;

class Http_Client {

	const METHOD_GET = 'GET';
	const METHOD_POST = 'POST';

	const UA_DESKTOP = 'Mozilla/5.0 (Macintosh; Intel Mac OS X x.y; rv:10.0) Gecko/20100101 Firefox/10.0';
	const UA_MOBILE = 'Mozilla/5.0 (iPhone; CPU iPhone OS 9_1 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13B137 Safari/601.1';

	protected function get_data( $data, $method ) {
		$default_data = array(
			'headers' => array(
				'Content-Type' => $method == 'GET' ? 'text/html' : 'application/x-www-form-urlencoded',
			),
			'params'  => array(),
		);

		return array_replace_recursive( $default_data, $data );
	}

	protected function get_params( $method, $url, $data ) {
		$parts       = wp_parse_url( $url );
		$params      = array();
		$post_string = '';
		$get_string  = isset( $parts['query'] ) ? $parts['query'] : '';

		if ( ! empty( $data['params'] ) || ! empty( $data['body'] ) ) {
			foreach ( $data['params'] as $key => $val ) {
				$params[] = $key . '=' . rawurlencode( $val );
			}

			$params_string = implode( '&', $params );

			if ( $method == 'GET' ) {
				$get_string .= '&' . $params_string;
			} else {
				$post_string = $params_string;

				if ( isset( $data['body'] ) ) {
					$get_string  .= '&' . $params_string;
					$post_string = is_array( $data['body'] ) ? wp_json_encode( $data['body'] ) : $data['body'];
				}
			}
		}

		return array(
			'get'  => '?' . trim( $get_string, '&' ),
			'post' => $post_string,
		);
	}

	protected function open_socket( $host, $port, $output ) {
		$fp = @fsockopen( $host, $port, $errno, $errstr, 30 ); // @codingStandardsIgnoreLine
		@fwrite( $fp, $output ); // @codingStandardsIgnoreLine
		@fclose( $fp );
	}

	public function request( $method, $url, $data = array() ) {
		$function = 'wp_remote_' . strtolower( $method );

		return $function( $url, $data );
	}

	public function fire_and_forget( $method, $url, $data = array() ) {
		$data   = $this->get_data( $data, $method );
		$params = $this->get_params( $method, $url, $data );

		$parts = wp_parse_url( $url );
		$port = isset( $parts['port'] ) ? $parts['port'] : 80;

		if ( $parts['scheme'] == 'https' ) {
			$parts['scheme'] = 'ssl';
			$port = 443;
		}

		$output  = $method . " " . $parts['path'] . $params['get'] . " HTTP/1.1\r\n";
		$output .= "Host: " . $parts['host'] . "\r\n";
		$output .= "Content-Type: " . $data['headers']['Content-Type'] . "\r\n";
		$output .= "Content-Length: " . strlen( $params['post'] ) . "\r\n";
		$output .= "Connection: Close\r\n\r\n";
		$output .= $params['post'];

		$this->open_socket( $parts['scheme'] . '://' . $parts['host'], $port, $output );
	}
}
