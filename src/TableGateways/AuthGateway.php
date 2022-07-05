<?php
namespace Src\TableGateways;

class AuthGateway {

    private $db = null;
    private $clientId  = null;
    private             $clientSecret = null;
    private             $scope        = null;
    private             $issuer       =null;
    public function __construct($db)
    {
        $this->db = $db;
        $this->clientId = getenv('OKTACLIENTID');
        $this->clientSecret = getenv('OKTASECRET');
        $this->scope =getenv('SCOPE');
        $this->issuer = getenv('OKTAISSUER');
    }
    
    public function login(Array $input)
    {
        //aqui hay que verificar que la contraseña coincida y toda la logica del login
        $statement = "SELECT
            u.id, u.name, u.username,g.name as group_name, u.group_id, u.password
            FROM
            users u
            LEFT JOIN groups g ON u.group_id = g.id
            WHERE u.username =?;";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array( $input['username']));
            $result = $statement->fetch(\PDO::FETCH_ASSOC);
            if(!$result)
             return false;
            $hash =$result['password'];

            if (password_verify( $input['password'], $hash)) {
                
                  $token =$this-> obtainToken();
                //   $token =  $this-> revokeToken('eyJraWQiOiJjczdTa280OVF5bXFyOTBLUG1OMFpiazFGd09WRUhxM0NUNVpNaFZzeVlZIiwiYWxnIjoiUlMyNTYifQ.eyJ2ZXIiOjEsImp0aSI6IkFULkNvWTg0M05iUThXY09BTjlsdjdrYWVxR0xYUFIzUDYzNUVOemxXbWVNWDAiLCJpc3MiOiJodHRwczovL2Rldi0xMjYxNDc5My5va3RhLmNvbS9vYXV0aDIvZGVmYXVsdCIsImF1ZCI6ImFwaTovL2RlZmF1bHQiLCJpYXQiOjE2NTY5NjYwMTcsImV4cCI6MTY1Njk2OTYxNywiY2lkIjoiMG9hNW1ueG03d3RXaTNoSHM1ZDciLCJzY3AiOlsia2MiXSwic3ViIjoiMG9hNW1ueG03d3RXaTNoSHM1ZDcifQ.b6WlOhpTjPQoyt9yOTab-WxRBF64CULuxdqGU-uEGOaIAXc5ui3arxfFi-EH23U-8brG5E3FGVXXmaRrMLiAebfVi_IueMNrrlRdqTui7qaJ-NLNr3Z6cEQ_iWlUPM_GlBCuMq3O92UdGXvR1nvEVwJmiiOi2xvzrYtUPBlwYHFUbbz95v46oFppMLTMBU55ET501ClMI6IVdEolL5_20VE7WIA0eb7VsLQI8hJve7UFKooamqasHLQkCNekLp9z3nnegQ7zg0JRCi5950fLA3_sZf4oc8aNxZWxmCcTltwAMNqjZE2bEe7twAXrSg7Amqtsxte-ntYFA7cQAMKuoA');
                return $token;
            } else {
                return false;
            }
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }
    function revokeToken($btoken) {
        
        // prepare the request
        $uri ='https://dev-12614793.okta.com/oauth2/v1/revoke';
        $token = base64_encode("$this->clientId:$this->clientSecret");
        $payload = http_build_query([
            'token' => $btoken,
            'token_type_hint'=> 'access_token'
        ]);
        // build the curl request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt( $ch, CURLOPT_HTTPHEADER, [
            'accept:application/json ',
            'cache-control:no-cache',
            'Content-Type: application/x-www-form-urlencoded',
            "Authorization: Basic $token"
        ]);

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
            $response = curl_exec($ch);
            
if($response === false)
{
    echo 'Curl error: ' . curl_error($ch);
}
else
{
    // echo 'Operación completada sin errores';
}
        
         $response = json_decode($response, true);
        // if (! isset($response['access_token'])
        //     || ! isset($response['token_type'])) {
        //     exit('failed, exiting.');
        // }
    
        // echo "success!\n ". $response['token_type'] . " " . $response['access_token'];
        // here's your token to use in API requests
        // return $response['token_type'] . " " . $response['access_token'];
        return  $response;
    }
    function obtainToken() {
        
        // prepare the request
        $uri = $this->issuer  . '/v1/token';
        $token = base64_encode("$this->clientId:$this->clientSecret");
        $payload = http_build_query([
            'grant_type' => 'client_credentials',
            'scope'      =>$this->scope
        ]);
    
        // build the curl request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt( $ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded',
            "Authorization: Basic $token"
        ]);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
        // process and return the response
        $response = curl_exec($ch);
        $response = json_decode($response, true);
        if (! isset($response['access_token'])
            || ! isset($response['token_type'])) {
            exit('failed, exiting.');
        }
    
        // echo "success!\n ". $response['token_type'] . " " . $response['access_token'];
        // here's your token to use in API requests
        // return $response['token_type'] . " " . $response['access_token'];
        return  $response;
    }
    
}