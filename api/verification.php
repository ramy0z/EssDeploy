<?php
include_once './config/database.php';
require "./vendor/autoload.php";

$dbConn = DBService::getCon();
$message="";
if (isset($_GET['code'])) {
    $active_code=$_GET['code'];
    $sql="SELECT * FROM users_temp WHERE active_code = :active_code";
    $stmt = $dbConn->prepare($sql);
    $stmt->bindParam(':active_code', $active_code);
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    // {
    //     "id": "0",
    //     "uName": "ramyezzaa",
    //     "usrPass": "$2y$10$g4Cj5qdG6BxLpnl4BCDTauT2HJ6HR4TkfIG2ZiPM8Sg.Y7rMyh83.",
    //     "email": "ra@my.ezz9",
    //     "role_id": "5",
    //     "entry_parent_id": "1",
    //     "active_code": "hPdqMNr7a9oGuFItAOXLKSsEQ2ZJvk0w36WbmfYRBcngxy8UTV"
    // }
    header("content-type: application/json");
    http_response_code(200);
    $response = json_encode(['resonse' => ['status' => 200, "result" => $data]]);
    echo $response; exit;
}

?>