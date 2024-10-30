<?php
if (!class_exists('Ecomfit_Api')) {
    class Ecomfit_Api
    {

        const METHOD_GET = 'GET';
        const METHOD_POST = 'POST';
        const METHOD_PUT = 'PUT';
        const METHOD_DELETE = 'DELETE';

        /**
         * The url api.
         *
         * @since    1.0.0
         * @access   protected
         * @var      string $url
         */
        protected $url;

        /**
         * The token using auth when send request.
         *
         * @since    1.0.0
         * @access   protected
         * @var      string $token
         */
        protected $token;

        /**
         * Initialize the class and set its properties.
         *
         * @param string $url The url api.
         * @param string $token The token using auth when send request.
         * @since    1.0.0
         */
        public function __construct($url, $token)
        {
            $this->url = $url;
            $this->token = $token;
        }

        /**
         * Send get request
         *
         * @param $uri
         * @param array $params
         *
         * @return array|bool|stdClass
         */
        public function get($uri, $params = array())
        {
            return self::send_request(self::METHOD_GET, $uri, $params);
        }

        /**
         * Send post request
         *
         * @param $uri
         * @param array $params
         * @param array $headers
         *
         * @return array|bool|stdClass
         */
        public function post($uri, $params = array(), $headers = array())
        {
            return self::send_request(self::METHOD_POST, $uri, $params, $headers);
        }

        /**
         * Send put request
         *
         * @param $uri
         * @param array $params
         * @param array $headers
         *
         * @return array|bool|stdClass
         */
        public function put($uri, $params = array(), $headers = array())
        {
            return self::send_request(self::METHOD_PUT, $uri, $params, $headers);
        }

        /**
         * Send delete request
         *
         * @param $uri
         * @param array $param
         * @param array $headers
         *
         * @return array|bool|stdClass
         */
        public function delete($uri, $param = array(), $headers = array())
        {
            return self::send_request(self::METHOD_DELETE, $uri, $param, $headers);
        }

        public function send_request($type, $uri, $content, $headers = array())
        {
            global $wp_version;
            if ($uri == '/v3/wordpress/getApiToken') {
                $headers = array_merge(array(
                    'Content-Type' => 'application/json'
                ), $headers);
            } else {
                $headers = array_merge(array(
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->token
                ), $headers);
            }
            $args = array(
                'method' => $type,
                'timeout' => 20,
                'redirection' => 5,
                'httpversion' => '1.0',
                'user-agent' => 'WordPress/' . $wp_version . '; ' . home_url(),
                'blocking' => true,
                'headers' => $headers,
                'body' => sizeof($content) ? json_encode($content) : '',
                'cookies' => array()
            );
            $url = $this->url . $uri;
            if ($type === self::METHOD_GET) {
                $response = wp_remote_get($url, $args);
                return wp_remote_retrieve_body($response);
            } else {
                $response = wp_remote_post($url, $args);
                return json_decode(wp_remote_retrieve_body($response));
            }

        }
    }
}
