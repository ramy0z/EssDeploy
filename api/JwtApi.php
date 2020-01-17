<?php
error_reporting(E_ALL);
error_reporting(E_ERROR | E_WARNING | E_PARSE);

include_once './config/database.php';
include_once "./vendor/autoload.php";
use \Firebase\JWT\JWT;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once('constants.php');
class Rest {
    
    protected $request;
    protected $serviceName;
    protected $param;
    protected $dbConn;
    protected $userId;
    public function __construct() {
        if($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->throwError(REQUEST_METHOD_NOT_VALID, 'Request Method is not valid.');
        }
        $handler = fopen('php://input', 'r');
        $this->request = stream_get_contents($handler);
        $this->validateRequest();
        $this->dbConn = DBService::getCon();
        $sNm=strtolower( $this->serviceName);
        if( $sNm=='login' || $sNm =='userAuth' ) {}
        else {
            $this->validateToken();
        }
    }
    public function getAuthorizationHeader(){
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        }
        else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }
    public function getBearerToken() {
        $headers = $this->getAuthorizationHeader();
        // HEADER: Get the access token from the header
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }
        $this->throwError( ATHORIZATION_HEADER_NOT_FOUND, 'Access Token Not found');
    }
    public function validateParameter($fieldName, $value, $dataType, $required = true) {
        if($required == true && empty($value) == true) {
            $this->throwError(VALIDATE_PARAMETER_REQUIRED, $fieldName . " parameter is required.");
        }
        $value=htmlspecialchars(strip_tags($value));
        switch ($dataType) {
            case BOOLEAN:
                if(!is_bool($value)) {
                    $this->throwError(VALIDATE_PARAMETER_DATATYPE, "Datatype is not valid for " . $fieldName . '. It should be boolean.');
                }
                break;
            case INTEGER:
                if(!is_numeric($value)) {
                    $this->throwError(VALIDATE_PARAMETER_DATATYPE, "Datatype is not valid for " . $fieldName . '. It should be numeric.');
                }
                break;
            case STRING:
                if(!is_string($value)) {
                    $this->throwError(VALIDATE_PARAMETER_DATATYPE, "Datatype is not valid for " . $fieldName . '. It should be string.');
                }
                break;
            
            default:
                $this->throwError(VALIDATE_PARAMETER_DATATYPE, "Datatype is not valid for " . $fieldName);
                break;
        }
        return $value;
    }
    Public function validateRequest() {
        $data = json_decode($this->request, true);
        if(isset($_SERVER['CONTENT_TYPE']) == ' multipart/form-data' &&  $data['name'] == "upload_image" ) {
        }
        else if(isset($_SERVER['CONTENT_TYPE']) != 'application/json') {
            $this->throwError(REQUEST_CONTENTTYPE_NOT_VALID, 'Request content type is not valid');
        }
        else {
            if(!isset($data['name']) || $data['name'] == "") {
            $this->throwError(API_NAME_REQUIRED, "API name is required.");
            }
            $this->serviceName = $data['name'];
            if(!isset($data['param']) || !is_array($data['param'])) {
                $this->throwError(API_PARAM_REQUIRED, "API PARAM is required.");
            }
            $this->param = $data['param'];
        }
    }
    public function validateToken() {
        try {
            $token = $this->getBearerToken();
            $payload = JWT::decode($token, SECRETE_KEY, ['HS256']);
            $stmt = $this->dbConn->prepare("SELECT  id,active FROM users WHERE id = :userId");
            $stmt->bindParam(":userId", $payload->userId);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if(!is_array($user)) {
                $this->returnResponse(INVALID_USER_PASS, "This user is not found in our database.");
            }
            if( $user['active'] == 0 ) {
                $this->returnResponse(USER_NOT_ACTIVE, "This user may be decactived. Please contact to admin.");
            }
            $this->userId = $payload->userId;
            //DBService::closeCon();
        } catch (Exception $e) {
             //DBService::closeCon();
            $this->throwError(ACCESS_TOKEN_ERRORS, $e->getMessage());
        }
    }
    public function generateToken($user){
        $issuer_claim = "THE_ISSUER servername"; // this can be the 
        $audience_claim = "THE_AUDIENCE"; //the audience
        $issuedat_claim = time(); // issued at
        $notbefore_claim = $issuedat_claim + 10; //not before in seconds
        $expire_claim = $issuedat_claim + (60*60); // expire time in seconds
        $expire_claim2 = $issuedat_claim + (60*60*24*3); // expire time in seconds 3days
        $paylod = [
            "iss" => $issuer_claim,"aud" => $audience_claim,
            "iat" => $issuedat_claim,"nbf" => $notbefore_claim,
            "exp" => $expire_claim,'userId' => $user['id']
        ];
        $paylod2 = [
            "iss" => $issuer_claim,"aud" => $audience_claim,
            "iat" => $issuedat_claim,"nbf" => $notbefore_claim,
            "exp" => $expire_claim2,'userId' => $user['id'],
        ];
        $token = JWT::encode($paylod, SECRETE_KEY);
        $refreshtToken = JWT::encode($paylod2,(SECRETE_KEY.$user['id'].SECRETE_KEY));
        
        return $data = ['token' => $token,'refreshtToken' => $refreshtToken,
                'uid'=>$user['id'],'urole'=>$user['role'] ];
    }
    public function processApiAdmin() {
        try {
            $api_admin = new ApiAdmin;
            $rMethod = new reflectionMethod('APIADMIN', $this->serviceName);
            if(!method_exists($api_admin, $this->serviceName)) {
                $this->throwError(API_DOST_NOT_EXIST, "This API does not exist.");
            }
            $rMethod->invoke($api_admin);
        } catch (Exception $e) {
            $this->throwError(API_DOST_NOT_EXIST, "This API does not exist.");
        }
        
    }
    public function processApi() {
        try {
            $api = new Api;
            $rMethod = new reflectionMethod('API', $this->serviceName);
            if(!method_exists($api, $this->serviceName)) {
                $this->throwError(API_DOST_NOT_EXIST, "API does not exist.");
            }
            $rMethod->invoke($api);
        } catch (Exception $e) {
            $this->throwError(API_DOST_NOT_EXIST, "API does not exist.");
        }
        
    }
    public function throwError($code, $message) {
        header("content-type: application/json");
        http_response_code($code);
        $errorMsg = json_encode(['error' => ['status'=>$code, 'message'=>$message]]);
        echo $errorMsg; exit;
    }
    public function returnResponse($code, $data) {
        header("content-type: application/json");
        http_response_code($code);
        $response = json_encode(['resonse' => ['status' => $code, "result" => $data]]);
        echo $response; exit;
    }

    public function login() {
        $email = $this->validateParameter('email', $this->param['email'], STRING);
        $pass = $this->validateParameter('pass', $this->param['pass'], STRING);
        try {
            $stmt = $this->dbConn->prepare("SELECT users.id AS id ,users.usrPass AS usrPass,users.active AS active ,user_role.roleNm AS role ,acc_entry.entryNm AS usrName FROM users JOIN acc_entry join user_role on acc_entry.id=users.entry_id and user_role.id=users.role_id WHERE users.email = :email");
            $stmt->bindParam(":email", $email);
            //$stmt->bindParam(":pass", $pass);
            $stmt->execute(); //$password_hash = password_hash($password, PASSWORD_BCRYPT);AND usrPass = :pass
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if(!is_array($user)) {
                $this->returnResponse(INVALID_USER_PASS, "Email is incorrect.");
            }
            if(!password_verify($pass,$user['usrPass'])) {
                $this->returnResponse(INVALID_USER_PASS, "Password is incorrect.");
            }
            if( $user['active'] == 0 ) {
                $this->returnResponse(USER_NOT_ACTIVE, "User is not activated. Please contact to admin.");
            }
            $data=$this->generateToken($user);
            $this->returnResponse(SUCCESS_RESPONSE, $data);
        } catch (Exception $e) {
            $this->throwError(JWT_PROCESSING_ERROR, $e->getMessage());
        }
    }
    public function userAuth() {
        try {
            $user_id = $this->param['uid'];
            $token = $this->getBearerToken();
            $payload = JWT::decode($token, (SECRETE_KEY.$user_id.SECRETE_KEY), ['HS256']);
            //$stmt = $this->dbConn->prepare("SELECT  id,active FROM users WHERE id = :userId");
            $stmt = $this->dbConn->prepare("SELECT users.id AS id ,users.usrPass AS usrPass,users.active AS active ,user_role.roleNm AS role ,acc_entry.entryNm AS usrName FROM users JOIN acc_entry join user_role on acc_entry.id=users.entry_id and user_role.id=users.role_id WHERE users.id = :userId");
            $stmt->bindParam(":userId", $payload->userId);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if(!is_array($user)) {
                $this->returnResponse(INVALID_USER_PASS, "This user is not found in our database.");
            }
            if( $user['active'] == 0 ) {
                $this->returnResponse(USER_NOT_ACTIVE, "This user may be decactived. Please contact to admin.");
            }
            $this->userId = $payload->userId;
            $data=$this->generateToken($user);
            $this->returnResponse(SUCCESS_RESPONSE, $data);
            //DBService::closeCon();
        } catch (Exception $e) {
             //DBService::closeCon();
            $this->throwError(ACCESS_TOKEN_ERRORS, $e->getMessage());
        }
    }
 
}
?>