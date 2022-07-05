<?php
namespace Src\Controller;

use Src\TableGateways\AuthGateway;

class AuthController {

    private $db;
    private $requestMethod;

    private $authGateway;

    public function __construct($db, $requestMethod)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;

        $this->authGateway = new AuthGateway($db);
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'POST':
                    $response = $this->login();
                break;
            case 'DELETE':
                    $response = $this->logout();
                break;
            default:
                $response = $this->notFoundResponse();
                break;
        }
        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
        }
    }

    private function login()
    {
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (! $this->validate($input)) {
            return $this->unprocessableEntityResponse();
        }
        
        $result =$this->authGateway->login($input);
        if($result === FALSE) {
            $response['status_code_header'] = 'HTTP/1.1 422 Unprocessable Entity';
            $response['body'] = json_encode([
                'error' => 'Invalid user or password'
            ]);
            return $response;
        }
        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['body']  = json_encode($result);
        return $response;
    }
    function getRequestHeaders() {
        $headers = array();
        foreach($_SERVER as $key => $value) {
            if (substr($key, 0, 5) <> 'HTTP_') {
                continue;
            }
            $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
            $headers[$header] = $value;
        }
        return $headers;
    }
    private function logout()
    {
        $headers =$this-> getRequestHeaders();
        $result = $this->authGateway->revokeToken( substr($headers['Authorization'],7));
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }
    private function validate($input)
    {
        if (! isset($input['username'])) {
            return false;
        }
        if (! isset($input['password'])) {
            return false;
        }
        return true;
    }

    private function unprocessableEntityResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 422 Unprocessable Entity';
        $response['body'] = json_encode([
            'error' => 'Invalid input'
        ]);
        return $response;
    }

    private function notFoundResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = null;
        return $response;
    }
}
