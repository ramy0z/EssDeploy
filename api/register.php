<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Max-Age: 3600");
require_once('constants.php');
include_once './config/database.php';
require "./vendor/autoload.php";
use \Firebase\JWT\JWT;
 error_reporting(E_ERROR | E_PARSE);

 require "./vendor/PHPMailer/PHPMailer.php";
 require "./vendor/PHPMailer/SMTP.php";
 require "./vendor/PHPMailer/Exception.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

    try{
        $reqData =json_decode(file_get_contents("php://input"),TRUE) ;
        $register= new register();
        if($_SERVER['REQUEST_METHOD'] == 'GET') {
            if( isset($_GET['code']) ){
                if($register->verifyTempCustomer()){
                   //register that customer 
                  if($register->signUpUsers($data)){
                   // >>>>>>>>>>>>>>>>>>>>
                  }
                  else{
                    $errorMsg= $register->createHtmlMail( 'Verification Error' , "Error While Create Your Account" ,"Sorry, Faild To Create Your Account. Please, contact Your Provider.",true);
                    header("content-type:text/html");echo $errorMsg;exit;
                  }
                }else {
                    $errorMsg= $register->createHtmlMail( 'Verification code Error' , "Error While Verify Account" ,"Sorry, Faild To Verify Your Account. Please, contact Your Provider.",true);
                    header("content-type:text/html");echo $errorMsg;exit;
                }
            }
            else {
                $errorMsg= $register->createHtmlMail( 'Verification code Error' , "Verification code Dosnt Matched" ,"Sorry, Faild To Verify Your Account. Please, contact Your Provider.",true);
                header("content-type:text/html"); echo $errorMsg;exit;
                //$register->throwError(REQUEST_METHOD_NOT_FOUND, " THIS REQUEST NOT VALID");
            }
        }
        else if($_SERVER['REQUEST_METHOD'] == 'POST') {
            if($reqData['name']=='EssRegisterCust'){
                if(!is_array($reqData['param'])) {$register->throwError(API_PARAM_REQUIRED, "PARAM is required.");}
                else{
                    $register->param = $reqData['param'];
                    $register->signUpTempCustomer();
                }
            }
            else {$register->throwError(REQUEST_METHOD_NOT_FOUND, " THIS REQUEST NOT VALID");}
        }
    }
    catch (Exception $e) {
        $register->throwError(REQUEST_METHOD_NOT_FOUND, $e->getMessage());
    }

 class register {
    protected $dbConn;
    public $param;

    public function signUpUsers($data){
        $reqData =json_decode(file_get_contents("php://input"),TRUE) ;
        try {
            $uName = $this->validateParameter('uName', $this->data['uName'], STRING);
            $email = $this->validateParameter('email', $this->data['email'], STRING);
            $userPass = $this->validateParameter('password', $this->data['password'], STRING);

            $conn= DBService::getCon();
            $conn->beginTransaction();

            $sql="INSERT INTO acc_entry (entryNm , parent_id) VALUES (:uName ,1)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':uName', $uName);
            $stmt->execute();
            $entry_id = $conn->lastInsertId();

            $sql="INSERT INTO users (email, usrPass ,entry_id) VALUES (:email , :usrPass , :entry_id)";
            $stmt = $conn->prepare($sql);
            $password_hash = password_hash($userPass, PASSWORD_BCRYPT);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':usrPass', $password_hash);
            $stmt->bindParam(':entry_id', $entry_id);
            $stmt->execute();
            $uId = $conn->lastInsertId();
            $conn->commit();
            DBService::closeCon();
            $conn = null;
            return true;
        }
        catch(PDOException $e){
            $conn->rollback();
            DBService::closeCon();
            $conn = null;
            return false;
        } 
    }
    
    public function sendMail($mailTo , $Head , $Subject , $Body){
        // Instantiation and passing `true` enables exceptions
        $mail = new PHPMailer(true);
        try {
            //Server settings
           // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
            $mail->isSMTP();                                            // Send using SMTP
            $mail->Host       = 'smtp.gmail.com';                    // Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
            $mail->Username   = 'admonsocyle2019@gmail.com';                     // SMTP username
            $mail->Password   = 'SOCYLEgymin123!@#';                               // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
            $mail->Port       = 587;                                    // TCP port to connect to

            //Recipients
            $mail->setFrom('info@ess.com', 'Ess Company');
            $mail->addAddress($mailTo);     // Add a recipient
            //$mail->addAddress('ramyezz');               // Name is optional
            //$mail->addReplyTo('info@example.com', 'Information');
            //$mail->addCC('cc@example.com');
            //$mail->addBCC('bcc@example.com');

            // Attachments
            //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
            //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
            $myHtmlRetun =$this->createHtmlMail( $Head, $Subject , $Body);
            // Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = $Subject;
            $mail->Body    = $myHtmlRetun;
            $mail->AltBody = $myHtmlRetun;

            $mail->send();
            return true;
        } catch (Exception $e) {
            $this->throwError(CATCH_DB_ERROR, "Message could not be sent. Mailer Error: ".$mail->ErrorInfo);
        }
    }
    public function createHtmlMail( $Head , $Subject , $Body , $error=false){
        $html = new DOMDocument(); 
        if($error){
            //there are error <div class="faild"></div>
            $html->loadHTMLFile('./uploads/templates/emailFaild.html');
        }
        else{
            $html->loadHTMLFile('./uploads/templates/emailSuccsess.html');
        }
        $html->getElementById('mail_head')->nodeValue = $Head;
        $html->getElementById('mail_title')->nodeValue =  $Subject;
        $html->getElementById('mail_body')->nodeValue = $Body;
        
        // $html = $html.preg_replace('mail_head', $Head);
        // $html = $html.preg_replace('mail_title', $Subject);
        // $html = $html.preg_replace('mail_body',  $Body);
        return $html->saveHTML();
    }

    public function signUpTempCustomer() {
        try{
            $returnVal= $this->checkMailUserExist( $this->param['email'] , $this->param['uName']);
            if( is_array($returnVal) ){
                $this->throwError(FAILD_RESPONSE, $returnVal[1]);
            }
            else{
                $conn= DBService::getCon();
                $conn->beginTransaction();
                $uName = $this->validateParameter('uName', $this->param['uName'], STRING);
                $email = $this->validateParameter('email', $this->param['email'], STRING);
                $userPass = $this->validateParameter('password', $this->param['password'], STRING);
                $password_hash = password_hash($userPass, PASSWORD_BCRYPT);
                $roleId=5;//$role=="Customer"
                $entryParentID=1;
                $active_code=$this->generate_rand();
                $sql="INSERT INTO users_temp (uName,usrPass,email,role_id,entry_parent_id,active_code) VALUES 
                        (:uName,:usrPass,:email,:role_id,:entry_parent_id ,:active_code)";
                $stmt =  $this->dbConn->prepare($sql);
                $stmt->bindParam(':uName', $uName);
                $stmt->bindParam(':usrPass', $password_hash);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':role_id', $roleId);
                $stmt->bindParam(':entry_parent_id', $entryParentID);
                $stmt->bindParam(':active_code', $active_code);
                $stmt->execute();
                //send Email to user with varification code
                //>>>>>>>>>>>>>>> 
                $mailTo=$email;
                $Head='Welcom To Ess Company';
                $Subject='Activation Code For ESS';
                //$btnapprove = '<a class="btnInvite" href="'.BASE_URL.'/register.php?code='. $active_code.'" target="_blank">Approve Invitation</a>';
                $btnapprove =BASE_URL.'/register.php?code='. $active_code;
                $Body= 'Please Click to Active your Account : '.$btnapprove .'';
                // $mailTo=$reqData['param']['mailTo'];
                // $Subject = $reqData['param']['Subject'];
                // $Body    = $reqData['param']['Body'];
                // $AltBody = $reqData['param']['AltBody'];
                $message ='';
                $return=$this->sendMail($mailTo ,$Head , $Subject , $Body);
                if($return){
                    $conn->commit();
                    $message ='Thank You For Signup. An ACtivation Code Has Been Sent To You. Please,Check Your Email.'; 
                    $this->returnResponse(SUCCESS_RESPONSE, $message);
                }
                else{
                    $conn->rollback();
                    $message ='Error While Sending Verifiaction Code To Your Email.';
                    $this->throwError(FAILD_RESPONSE, $message);
                }
            }
        }
        catch (PDOException $e) {
            $conn->rollback();
            $message ='Sorry ,Server Faild To Create Your Account. Please, contact Your Provider.';
            $this->throwError(CATCH_DB_ERROR, $message);
        }
    }
    public function verifyTempCustomer() {
        if (isset($_GET['code'])) {
            $active_code=$_GET['code'];
            $this->dbConn = DBService::getCon();
            $sql="SELECT * FROM users_temp WHERE active_code = :active_code";
            $stmt = $this->dbConn->prepare($sql);
            $stmt->bindParam(':active_code', $active_code);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            // {
            //     "id": "0",
            //     "uName": "ramyezzaa",
            //     "usrPass": "$2y$10$g4Cj5qdG6BxLpnl4BCDTauT2HJ6HR4TkfIG2ZiPM8Sg.Y7rMyh83.",
            //     "email": "ra@my.ezz9",
            //     "role_id": "5",
            //     "entry_parent_id": "1",
            //     "active_code": "hPdqMNr7a9oGuFItAOXLKSsEQ2ZJvk0w36WbmfYRBcngxy8UTV"
            // }
            //header("content-type: application/json");
            //http_response_code(200);
            //$response = json_encode(['resonse' => ['status' => 200, "result" => $data]]);
            //echo $response; exit;
            if($data['active_code']==$active_code){
                return true;
            }
            else return false;
        }
    }
    public function checkMailUserExist($mail ,$userName) {
        if (isset($mail) && isset($userName)) {
            $this->dbConn = DBService::getCon();
            $sql="SELECT email ,uName , status FROM users_temp WHERE uName=:userName OR email =:email";
            $stmt = $this->dbConn->prepare($sql);
            $stmt->bindParam(':email', $mail);
            $stmt->bindParam(':userName', $userName);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo count($data);
            if( count($data)==0 ){ return false;}
            elseif( count($data)==1 ){
                $data=$data[0];
                if($data['status']==1){
                    if( $data['email'] ==$mail && $data['nName']==$userName){
                        $message ='There Is Active Account Exist Before.';
                    }
                    elseif($data['email'] ==$mail){
                        $message ='This Active Email Exist Before. Please, Change Email And Try Again.';
                    }
                    else{
                        $message ='This Active User Name Exist Before. Please, Change User Name And Try Again.';
                    }
                    return [true , $message];
                }
                else if($data['status']==0){ //stats false ;
                    if($data['email'] ==$mail && $data['nName']==$userName){
                        $message ='This Account Exist Before. But It Not Active Yet, Check Your Email To Complete Activation.';
                    }
                    elseif($data['email'] ==$mail){
                        $message ='This Inactive Email Exist Before. Please, Change Email And Try Again.';
                    }
                    else{
                        $message ='This Inactive User Name Exist Before. Please, Change User Name And Try Again.';
                    }
                    return [true , $message];
                }
            }
            elseif( count($data)>1 ) {
                //count($data)> 1
                $message ='This Email And User Name Exist Before. Please, Change Email And User Name Then Try Again.';
                return [true , $message];
            }
        }
        else{
            return false;
        }
       
    }
    
    public function generate_rand(int $length=50){
        $length=($length<50 || $length>100)?50:$length;
        return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
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
    public function throwError($code, $message) {
        header("content-type: application/json");
        http_response_code($code);
        $errorMsg = json_encode(['error' => ['status'=>$code, 'message'=>$message]]);
        echo $errorMsg;
        exit;
    }
    public function returnResponse($code, $data) {
        header("content-type: application/json");
        http_response_code($code);
        $response = json_encode(['resonse' => ['status' => $code, "result" => $data]]);
        echo $response;
        exit;
    }
 }


?>


<?php
// include_once './config/database.php';
// require "../vendor/autoload.php";
// use \Firebase\JWT\JWT;

// header("Access-Control-Allow-Origin: *");
// header("Content-Type: application/json; charset=UTF-8");
// header("Access-Control-Allow-Methods: POST");
// header("Access-Control-Max-Age: 3600");
// header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


// $email = '';
// $password = '';

// $databaseService = new DatabaseService();
// $conn = $databaseService->getConnection();



// $data = json_decode(file_get_contents("php://input"));

// $email = $data->email;
// $password = $data->password;

// $table_name = 'Users';

// $query = "SELECT id, first_name, last_name, password FROM " . $table_name . " WHERE email = ? LIMIT 0,1";

// $stmt = $conn->prepare( $query );
// $stmt->bindParam(1, $email);
// $stmt->execute();
// $num = $stmt->rowCount();

// if($num > 0){
//     $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
//     $id = $row['id'];
//     $firstname = $row['first_name'];
//     $lastname = $row['last_name'];
//     $password2 = $row['password'];

//     if(password_verify($password, $password2))
//     {
//         $secret_key = "YOUR_SECRET_KEY";
//         $issuer_claim = "THE_ISSUER"; // this can be the servername
//         $audience_claim = "THE_AUDIENCE";
//         $issuedat_claim = time(); // issued at
//         $notbefore_claim = $issuedat_claim + 10; //not before in seconds
//         $expire_claim = $issuedat_claim + 60; // expire time in seconds
//         $token = array(
//             "iss" => $issuer_claim,
//             "aud" => $audience_claim,
//             "iat" => $issuedat_claim,
//             "nbf" => $notbefore_claim,
//             "exp" => $expire_claim,
//             "data" => array(
//                 "id" => $id,
//                 "firstname" => $firstname,
//                 "lastname" => $lastname,
//                 "email" => $email
//         ));

//         http_response_code(200);

//         $jwt = JWT::encode($token, $secret_key);
//         echo json_encode(
//             array(
//                 "message" => "Successful login.",
//                 "jwt" => $jwt,
//                 "email" => $email,
//                 "expireAt" => $expire_claim
//             ));
//     }
//     else{

//         http_response_code(401);
//         echo json_encode(array("message" => "Login failed.", "password" => $password));
//     }
// }
?>