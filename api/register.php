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
        $register= new register();
        if($_SERVER['REQUEST_METHOD'] == 'GET') {
            if( isset($_GET['code']) ){
                $returnVerify=$register->verifyTempCustomer();
                if($returnVerify[0]){
                   //register that customer 
                   $data=$returnVerify[1];
                  if($register->signUpCustomers($data)){
                   // >>>>>>>>>>>>>>>>>>>>
                   $urlApprove =SITE_URL.' ';
                   $succssessRespons= $register->createHtmlMail( 'Account Verification' , 
                   "Your Account Approved Successfully" ,
                   "Now, You Can Check Our Site Now From Here: ".$urlApprove." .");
                    header("content-type:text/html");echo $succssessRespons;exit;
                  }
                  else{
                    $errorMsg= $register->createHtmlMail( 'Verification Error' , "Error While Create Your Account" ,"Sorry, Faild To Create Your Account. Please, contact Your Provider.",true);
                    header("content-type:text/html");echo $errorMsg;exit;
                  }
                }else {
                    $message=$returnVerify[1];
                    $errorMsg= $register->createHtmlMail( 'Verification Error' , "Error While Verify Your Account" ,$message,true);
                    header("content-type:text/html");echo $errorMsg;exit;
                }
            }
            // else {
            //     $errorMsg= $register->createHtmlMail( 'Verification code Error' , "Error In Verification code" ,"Sorry, Faild To Verify Your Account. Please, contact Your Provider.",true);
            //     header("content-type:text/html"); echo $errorMsg;exit;
            //     //$register->throwError(REQUEST_METHOD_NOT_FOUND, " THIS REQUEST NOT VALID");
            // }
        }
        else if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $reqData =json_decode(file_get_contents("php://input"),TRUE) ;
            if($reqData['name']=='EssRegisterCust'){
                if(!is_array($reqData['param'])) {$register->throwError(API_PARAM_REQUIRED, "PARAM is required.");}
                else{
                    $register->param = $reqData['param'];
                    $register->signUpTempCustomer();
                }
            }
            else {$register->throwError(REQUEST_METHOD_NOT_FOUND, "THIS REQUEST NOT VALID");}
        }
    }
    catch (Exception $e) {
        $register->throwError(REQUEST_METHOD_NOT_FOUND, $e->getMessage());
    }

 class register {
    protected $dbConn;
    public $param;

    public function signUpCustomers($data){
        try {
            $uName = $this->validateParameter('uName', $data['uName'], STRING,true);
            $email = $this->validateParameter('email', $data['email'], STRING,true);
            $userPhone = $this->validateParameter('phone', $data['phone'], INTEGER ,true);
            $userPass = $this->validateParameter('password', $data['usrPass'], STRING,true);
            $conn= DBService::getCon();
            $conn->beginTransaction();

            $sql="INSERT INTO acc_entry (entryNm , parent_id) VALUES (:uName ,1)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':uName', $uName);
            $stmt->execute();
            $entry_id = $conn->lastInsertId();

            $sql="INSERT INTO users (email, usrPass,phone,entry_id ,role_id ,active) VALUES (:email , :usrPass ,:phone, :entry_id,5 ,1)";
            $stmt = $conn->prepare($sql);
            //$password_hash = password_hash($userPass, PASSWORD_BCRYPT);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':usrPass', $userPass);
            $stmt->bindParam(':phone', $userPhone);
            $stmt->bindParam(':entry_id', $entry_id);
            $stmt->execute();
            $uId = $conn->lastInsertId();

            // upsate users temp
            $sql="UPDATE users_temp SET status=1 ,joinDt=now() WHERE id=:id";
            $stmt =  $this->dbConn->prepare($sql);
            $stmt->bindParam(':id', $data['id']);
            $stmt->execute();

            $conn->commit();
            DBService::closeCon();
            $conn = null;
            return true;
        }
        catch(PDOException $e){
            echo $e;
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
            $mail->Username   = 'info.ess365@gmail.com';                     // SMTP username
            $mail->Password   = 'ELLMOHAGERESS1/1*12!@#';                               // SMTP password
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
        $html->getElementById('mail_body')->style->color = "red";
        return $html->saveHTML();
    }

    public function signUpTempCustomer() {
        try{
            $uName = $this->validateParameter('uName', $this->param['uName'], STRING ,true);
            $email = $this->validateParameter('email', $this->param['email'], EMAIL ,true);
            $phone = $this->validateParameter('phone', $this->param['phone'], INTEGER ,true);
            $userPass = $this->validateParameter('password', $this->param['password'], STRING ,true);

            $returnVal= $this->checkMailUserExist( $this->param['email'] , $this->param['uName'] ,$this->param['phone'] );
            if( is_array($returnVal) ){
                $this->throwError(FAILD_RESPONSE, $returnVal[1]);
            }
            else{
                $conn= DBService::getCon();
                $conn->beginTransaction();
                $password_hash = password_hash($userPass, PASSWORD_BCRYPT);
                $active_code=$this->generate_rand();
                $sql="INSERT INTO users_temp (uName,usrPass,email,phone,active_code) VALUES 
                        (:uName,:usrPass,:email,:phone,:active_code)";
                $stmt =  $this->dbConn->prepare($sql);
                $stmt->bindParam(':uName', $uName);
                $stmt->bindParam(':usrPass', $password_hash);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':phone', $phone);
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
            echo $e;
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
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            if($data['active_code']==$active_code && $data['status']==1){
                return [false , "Sorry, This Account Has Been Verified Before."];
            }
            else if($data['active_code']==$active_code){
                return [true , $data];
            }
            else return [false , "Sorry, Faild To Verify Your Account. Please, contact Your Provider."];
        }
    }
    public function checkMailUserExist($mail ,$userName ,$phone) {
        if (isset($mail) && isset($userName) && isset($phone)) {
            $this->dbConn = DBService::getCon();
            $sql="SELECT email ,uName ,phone, status FROM users_temp WHERE uName=:userName OR email =:email OR phone=:phone";
            $stmt = $this->dbConn->prepare($sql);
            $stmt->bindParam(':email', $mail);
            $stmt->bindParam(':userName', $userName);
            $stmt->bindParam(':phone', $phone);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if( count($data)==0 ){ return false;}
            elseif( count($data)==1 ){
                $data=$data[0];
                $message='';        $mdg=[];
                if( $data['email'] ==$mail && $data['uName']==$userName && $data['phone']==$phone){
                    $message =($data['status']==1)?'There Is ACTIVE Account Exist Before Has This Data.':'This INACTIVE Account Exist Before. But It Not Active Yet, Check Your Email To Complete Activation.';
                }else{
                    if($data['uName'] ==$userName){
                        $msg[] =' USER NAME ';
                    }
                    if($data['email'] ==$mail){
                        $msg[] =' EMAIL ';
                    }
                    if($data['phone'] ==$phone){
                        $msg[] =' PHONE ';
                    }
                    $message ='This '.implode(",", $msg).' Exist Before. Please, Change Invalid Data And Try Again.';
                }
                return [true , $message];
            }
            elseif( count($data)>1 ) {
                $dataArr=[];        $msg='';
                foreach($data as $valueobj){
                    foreach($valueobj as $key => $value){
                        if(!in_array($key, $dataArr, true)){
                            $dataArr[$key]= $key;
                        }
                    }
                }
                if($dataArr['uName'] =='uName'){
                    $msg .=' USER NAME -';
                }
                if($dataArr['email'] =='email'){
                    $msg .=' EMAIL -';
                }
                if($dataArr['phone'] =='phone'){
                    $msg .=' PHONE -';
                }
                $message ='This '. $msg .'> Exist Before. Please, Change Invalid Data And Try Again.';
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
        DBService::closeCon();
        echo $errorMsg;
        exit;
    }
    public function returnResponse($code, $data) {
        header("content-type: application/json");
        http_response_code(200);
        $response = json_encode(['response' => ['status' => $code, "message" => $data]]);
        DBService::closeCon();
        echo $response;
        exit;
    }
 }


?>