<?php
include_once './config/database.php';
include_once './objects/user.php';
require "./vendor/autoload.php";
use \Firebase\JWT\JWT;

header("Access-Control-Allow-Origin: * ");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
error_reporting(E_ERROR | E_PARSE);

    $conn = null;
    $data =json_decode(file_get_contents("php://input"),TRUE) ;
    if(sizeof($data)>0){
        try { 
            $conn= DBService::getCon();
            $conn->beginTransaction();

            $sql="INSERT INTO acc_entry (entryNm , parent_id) VALUES (:uName ,1)";
            $stmt = $conn->prepare($sql);
            $userName=htmlspecialchars(strip_tags($data["uName"]));
            $stmt->bindParam(':uName', $userName);
            $stmt->execute();
            $entry_id = $conn->lastInsertId();

            $sql="INSERT INTO users (email, usrPass ,entry_id) VALUES (:email , :usrPass , :entry_id)";
            $stmt = $conn->prepare($sql);
            $email=htmlspecialchars(strip_tags($data["email"]));
            $userPass=htmlspecialchars(strip_tags($data["password"]));
            $password_hash = password_hash($userPass, PASSWORD_BCRYPT);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':usrPass', $password_hash);
            $stmt->bindParam(':entry_id', $entry_id);
            $stmt->execute();
            $uId = $conn->lastInsertId();
            $conn->commit();
            DBService::closeCon();
            $conn = null;

            $secret_key = "YOUR_SECRET_KEY";
            $issuer_claim = "THE_ISSUER"; // this can be the servername
            $audience_claim = "THE_AUDIENCE";
            $issuedat_claim = time(); // issued at
            $notbefore_claim = $issuedat_claim + 10; //not before in seconds
            $expire_claim = $issuedat_claim + 60; // expire time in seconds
            $token = array(
                "iss" => $issuer_claim,
                "aud" => $audience_claim,
                "iat" => $issuedat_claim,
                "nbf" => $notbefore_claim,
                "exp" => $expire_claim,
                "data" => array(
                    "id" => $uId,
                    "uName" => $userName,
                    "email" => $email
            ));
            http_response_code(200);
            $jwt = JWT::encode($token, $secret_key);
            echo json_encode(
                array(
                    "message" => "Successful login.",
                    "jwt" => $jwt,
                    "email" => $email,
                    "expireAt" => $expire_claim
                ));
        }
        catch(PDOException $e){
            $conn->rollback();
            DBService::closeCon();
            $conn = null;
            http_response_code(401);
            echo json_encode(array("message" => "Login failed.", "password" => $password));

            return $e->getMessage();
            //return "error";
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
//     $row = $stmt->fetch(PDO::FETCH_ASSOC);
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