<?php
namespace FitApp\api;

use FitApp\classes\Logins;
use FitApp\classes\JsonResponse;
use FitApp\exceptions\MethodNotAllowedException;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;

/**
 * class RestService
 *
 * @package FitApp\api
 *
 *
 *         expired: eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyX25hbWUiOiJ0ZXN0dXNlcjEiLCJjb3VudHJ5Q29kZSI6IlVTIiwic2NvcGUiOlsicmVhZDpjYW1wYWlnbiJdLCJpZCI6MSwiZXhwIjoxNTA0ODA1NzQxLCJhdXRob3JpdGllcyI6WyJBZG1pbiIsIkFkanVzdEJvb2tpbmdzIiwiRW5naW5lZXJpbmdBcHByb3ZhbCJdLCJqdGkiOiJjM2JlYmEzMS03YzNiLTQ4NzEtYmFhYS02NWQ2MmNjZWEyNzEiLCJjbGllbnRfaWQiOiJwdWxzZSJ9.k-Mn_4mXhVDv-ouTQ3fCuPYejidefAyd3Dcjo7bh4Q0
 *         longer : eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyX25hbWUiOiJ0ZXN0dXNlcjEiLCJjb3VudHJ5Q29kZSI6IlVTIiwic2NvcGUiOlsicmVhZDpjYW1wYWlnbiJdLCJpZCI6MSwiZXhwIjoxNTA2MDE3MDY4LCJhdXRob3JpdGllcyI6WyJBZG1pbiIsIkFkanVzdEJvb2tpbmdzIiwiRW5naW5lZXJpbmdBcHByb3ZhbCJdLCJqdGkiOiJiODQ1MjM5OS04ZDUwLTRhMGQtOTc4Mi0xYmQ1ZDQzMmQ1NDEiLCJjbGllbnRfaWQiOiJwdWxzZSJ9.SAAuncWpqGiYye3BdZQhUbjQpfEPSsyaPG7fXT5fOG4
 *
 */
class RestService {
    protected $supportedMethods = [];
    protected $nounslist;
    protected $method;
    protected $envelope;

    /**
     *
     * @param $supportedMethods
     */
    public function __construct($supportedMethods = NULL) {
        if (!isset($this->supportedMethods)) {
            $this->supportedMethods = $supportedMethods;
        }
        $this->envelope = 'usertoken';

    }

    /**
     * Gets the JSON Web Token off the $_SERVER array, and decodes it
     * @return boolean|array
     */
    public function getJWT() {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
        $secretKey = 'secretkey'; //@todo put into config?
        $token = FALSE;
        if ($authHeader) {
            list($jwt) = sscanf($authHeader, 'Bearer %s');
            if ($jwt) {
                try {
                    $token_decoded = JWT::decode($jwt, $secretKey, array('HS256'));
                } catch (ExpiredException $e) {
                    return FALSE;
                }
                $token = (array) $token_decoded;

            }
        }
        return $token;

    }

    /**
     */
    public function handleRawRequest() {
        list($server, $get, $post) = [$_SERVER, $_GET, $_POST];

        $this->method= ($server['REQUEST_METHOD']) ?: $_SERVER['REQUEST_METHOD'];
        $header = [];
        $arguments = [];
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $token = $this->getJWT();
            // add login if there isn't one running
            // login age stuff
            // duplicate as below
            $this->envelope = 'jwt';
            if (!$token) {
                $this->userTokenRequired($arguments);
            }
            $header['user_id'] = $token['id'];
            $header['user_permissions'] = $token['authorities'];
        } elseif (isset($_SERVER['HTTP_USER_TOKEN'])) {
            // @todo make sure expired tokens aren't valid
            $header['user_token'] = $_SERVER['HTTP_USER_TOKEN'];
            $user = Logins::getUserFromToken($header['user_token']);
            $header['user_id'] = $user['user']['id'];
            $header['user_permissions'] = $user['permissions'];
            $header['user_login_age'] = $user['login_age'];
            if ($user['login_age'] > 30) {
                $arguments['debug'] = TRUE;
                Logins::extendToken($header['user_token']);
            }
        } elseif ($this->method != 'OPTIONS') {
            $this->userTokenRequired($arguments);
        }

        if (isset($_SERVER['HTTP_API_KEY'])) {
            $header['api_key'] = $_SERVER['HTTP_API_KEY'];
        }
        switch ($this->method) {
            case 'POST' :
                // POST comes in as JSON in our model
                $payload = file_get_contents('php://input');
                if (is_array($payload)) {
                    foreach ($payload as $key=>$val) {
                        $post['data'] = json_decode($key, true);
                        break;
                    }
                } else {
                    $post['data'] = json_decode($payload, TRUE);
                }
                $arguments = array_merge($post, $header);
                break;
            case 'PUT' :
                // PUT comes in as JSON in our model, but it's like a GET in the params sent
                $payload = file_get_contents('php://input');
                $put['data'] = $payload;
                $arguments = array_merge($put, $header);
                $request_uri = $_SERVER['REQUEST_URI'];
                $query_string = $_SERVER['QUERY_STRING'];
                if (strpos($request_uri, '?') > 0) {
                    list($request_uri, $query_string) = explode('?', $request_uri);
                }
                $request = explode('/', trim($request_uri, '/'));
                while (count($request) > 0) {
                    $var = array_shift($request);
                    if (count($request) > 0 && in_array($var, $this->nounslist) && !in_array($request[0], $this->nounslist)) {
                        $val = array_shift($request);
                        $arguments[$var] = urldecode($val);
                    } elseif (substr($var, 0, 1) == '?') {
                        continue;
                    } else {
                        $arguments[$var] = null;
                    }
                }
                if ($query_string != '') {
                    $qspairs = explode("&", $query_string);
                    foreach ($qspairs as $pair) {
                        list($key, $val) = explode('=', $pair);
                        $arguments[$key] = urldecode($val);
                    }
                }
                $arguments = array_merge($arguments, $header);
                break;
            case 'GET' :
            case 'HEAD' :
            case 'DELETE' :
            default :
                $arguments = $get;
                $request_uri = $_SERVER['REQUEST_URI'];
                $request = explode('/', trim($request_uri, '/'));
                if ($request[0] == 'rest') {
                    @array_shift($request);
                }
                while (count($request) > 0) {
                    $var = array_shift($request);
                    if (count($request) > 0 && in_array($var, $this->nounslist) && !in_array($request[0], $this->nounslist)) {
                        $val = array_shift($request);
                        if (strpos(',', $val) > 0) {
                            $val = explode(',', $val);
                        }
                        $arguments[$var] = urldecode($val);
                    } elseif (substr($var, 0, 1) == '?') {
                        continue;
                    } else {
                        $arguments[$var] = null;
                    }
                }
                $arguments = array_merge($arguments, $header);
                break;
        }

        $accept = $server['HTTP_ACCEPT'];

        try {
            $this->handleRequest($this->method, $arguments, $accept);
        }
        catch (MethodNotAllowedException $e) {
            $this->methodNotAllowedResponse();
        }

    }

    /**
     *
     * @return null
     */
    public function getSupportedMethods() {
        return $this->supportedMethods;

    }

    /**
     *
     * @param string $http_method
     * @param array $arguments
     * @param string $accept
     */
    public function handleRequest($http_method, $arguments, $accept = '') {
        $class_method = 'perform' . $http_method;
        $supMethods = $this->getSupportedMethods();
        $allowed = in_array($http_method, $supMethods);
        $exists = method_exists($this, $class_method);
        if ($exists && $allowed) {
            call_user_func([$this, $class_method], $arguments, $accept);
        } else {
            throw new MethodNotAllowedException("Method: $http_method Not Supported");
        }

    }

    /**
     * Sends the 405 Method Not Allowed
     */
    protected function methodNotAllowedResponse() {
        /* 405 (Method Not Allowed) */
        $methods = implode(', ', $this->supportedMethods);
        header('Allow: ' . $methods, true, 405);

    }

    /**
     * Sends the 401 Unauthorized header
     * @param unknown $arguments
     */
    protected function userTokenRequired($arguments) {
        $data['header'] = 'HTTP/1.1 401 Unauthorized';
        $message = $this->method ." Called. User Token Not supplied or Expired. Please Login";
        $this->sendResponse(FALSE, $message, $data, $arguments);
    }

    /**
     * The method stays in the parent since it's used by almost all calls
     */
    public function performOptions() { // no arguments
        header('HTTP/1.1 200 OK');
        header('Allow: GET, POST, PUT, OPTIONS, DELETE');
        exit();
    }

    /**
     *
     * @param $success
     * @param $message
     * @param $data
     * @param $arguments
     */
    protected function sendResponse($success, $message, $data, $arguments) {
        if ($success) {
            $this->successHeader($data['header']);
        } else {
            $this->failureHeader($data['header']);
        }
        if (isset($data['header'])) {
            unset($data['header']);
        }
        if ($this->method == 'POST' || $this->method == 'OPTIONS') {
            unset($arguments);
        }
        $jsonResponse = ['status'=>$success ? 'success' : 'failure',
            'success'=>$success,
            'message'=>$message,
            'data'=>$data,
            'debug'=>$arguments];
        if ($success && !isset($arguments['debug'])) {
            unset($jsonResponse['debug']);
        }
        echo new JsonResponse($jsonResponse);
        exit();

    }

    protected function successHeader($header = 'HTTP/1.1 200 OK') {
        header($header);
    }

    protected function failureHeader($header = 'HTTP/1.1 404 Not Found') {
        header($header);
    }

}