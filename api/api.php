<?php 
require_once './JwtApi.php';
require_once "./vendor/autoload.php";
use \Firebase\JWT\JWT;
	class Api  extends Rest {
		
		public function __construct() {
			parent::__construct();
		}
		public function generateToken() {
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
				$issuer_claim = "THE_ISSUER servername"; // this can be the 
				$audience_claim = "THE_AUDIENCE"; //the audience
				$issuedat_claim = time(); // issued at
				$notbefore_claim = $issuedat_claim + 10; //not before in seconds
				$expire_claim = $issuedat_claim + (60*100); // expire time in seconds
				$expire_claim2 = $issuedat_claim + (60*1000); // expire time in seconds
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
				$refreshtToken = JWT::encode($paylod2, (SECRETE_KEY.$user['usrPass']) );
				
				$data = ['token' => $token,'refreshtToken' => $refreshtToken,
						'uid'=>$user['id'],'uname'=>$user['id'],'urole'=>$user['role'] ];
				$this->returnResponse(SUCCESS_RESPONSE, $data);
			} catch (Exception $e) {
				$this->throwError(JWT_PROCESSING_ERROR, $e->getMessage());
			}
		}


		
		
		public function signUsers() {
			// $data =json_decode(file_get_contents("php://input"),TRUE) ;
			try {
				$uName = $this->validateParameter('uName', $this->param['uName'], STRING);
				$email = $this->validateParameter('email', $this->param['email'], STRING);
				$userPass = $this->validateParameter('password', $this->param['password'], STRING);
				$role = $this->validateParameter('role', $this->param['role'], STRING);
				
				$this->dbConn->beginTransaction();
				
				$roleId=($role=="Admin")?1:($role=="Accountant")?2:($role=="Delivery")?3:($role=="Supplier")?4:($role=="Customer")?5:-1;	
				$entryParentID=($role=="Customer")?1:(($role=="Admin") || ($role=="Accountant") ||($role=="Delivery") )?3:($role=="Supplier")?2:-1;

				$sql="INSERT INTO acc_entry (entryNm , parent_id) VALUES (:uName ,:parentId)";
				$stmt =  $this->dbConn->prepare($sql);
				$stmt->bindParam(':uName', $uName);
				$stmt->bindParam(':parentId', $entryParentID);
				$stmt->execute();
				$entry_id = $this->dbConn->lastInsertId();
	
				$sql="INSERT INTO users (email, usrPass ,entry_id,role_id) VALUES (:email , :usrPass , :entry_id ,:role_id)";
				$stmt =  $this->dbConn->prepare($sql);
				$password_hash = password_hash($userPass, PASSWORD_BCRYPT);
				$stmt->bindParam(':email', $email);
				$stmt->bindParam(':usrPass', $password_hash);
				$stmt->bindParam(':entry_id', $entry_id);
				$stmt->bindParam(':role_id', $roleId);
				$stmt->execute();
				$uId =  $this->dbConn->lastInsertId();
				$this->dbConn->commit();
				DBService::closeCon();
				// $this->dbConn = null;
	
				// $secret_key = "YOUR_SECRET_KEY";
				// $issuer_claim = "THE_ISSUER"; // this can be the servername
				// $audience_claim = "THE_AUDIENCE";
				// $issuedat_claim = time(); // issued at
				// $notbefore_claim = $issuedat_claim + 10; //not before in seconds
				// $expire_claim = $issuedat_claim + 60; // expire time in seconds
				// $token = array(
				//     "iss" => $issuer_claim,
				//     "aud" => $audience_claim,
				//     "iat" => $issuedat_claim,
				//     "nbf" => $notbefore_claim,
				//     "exp" => $expire_claim,
				//     "data" => array(
				//         "id" => $uId,
				//         "uName" => $userName,
				//         "email" => $email
				// ));
				$this->returnResponse(SUCCESS_RESPONSE, $data);
			} catch (Exception $e) {
				$this->dbConn->rollback();
				// $this->dbConn = null;
				$this->throwError(JWT_PROCESSING_ERROR, $e->getMessage());
			}
		}


	
		public function addCustomer() {
			$name = $this->validateParameter('name', $this->param['name'], STRING, false);
			$email = $this->validateParameter('email', $this->param['email'], STRING, false);
			$addr = $this->validateParameter('addr', $this->param['addr'], STRING, false);
			$mobile = $this->validateParameter('mobile', $this->param['mobile'], INTEGER, false);
			$cust = new Customer;
			$cust->setName($name);
			$cust->setEmail($email);
			$cust->setAddress($addr);
			$cust->setMobile($mobile);
			$cust->setCreatedBy($this->userId);
			$cust->setCreatedOn(date('Y-m-d'));
			if(!$cust->insert()) {
				$message = 'Failed to insert.';
			} else {
				$message = "Inserted successfully.";
			}
			$this->returnResponse(SUCCESS_RESPONSE, $message);
		}
		public function getCustomerDetails() {
			$customerId = $this->validateParameter('customerId', $this->param['customerId'], INTEGER);
			$cust = new Customer;
			$cust->setId($customerId);
			$customer = $cust->getCustomerDetailsById();
			if(!is_array($customer)) {
				echo $customerId;
				$this->returnResponse(SUCCESS_RESPONSE, ['message' => 'Customer details not found.']);
			}
			$response['customerId'] 	= $customer['id'];
			$response['cutomerName'] 	= $customer['name'];
			$response['email'] 			= $customer['email'];
			$response['mobile'] 		= $customer['mobile'];
			$response['address'] 		= $customer['address'];
			$response['createdBy'] 		= $customer['created_user'];
			$response['lastUpdatedBy'] 	= $customer['updated_user'];
			$this->returnResponse(SUCCESS_RESPONSE, $response);
		}
		public function updateCustomer() {
			$customerId = $this->validateParameter('customerId', $this->param['customerId'], INTEGER);
			$name = $this->validateParameter('name', $this->param['name'], STRING, false);
			$addr = $this->validateParameter('addr', $this->param['addr'], STRING, false);
			$mobile = $this->validateParameter('mobile', $this->param['mobile'], INTEGER, false);
			$cust = new Customer;
			$cust->setId($customerId);
			$cust->setName($name);
			$cust->setAddress($addr);
			$cust->setMobile($mobile);
			$cust->setUpdatedBy($this->userId);
			$cust->setUpdatedOn(date('Y-m-d'));
			if(!$cust->update()) {
				$message = 'Failed to update.';
			} else {
				$message = "Updated successfully.";
			}
			$this->returnResponse(SUCCESS_RESPONSE, $message);
		}
		public function deleteCustomer() {
			$customerId = $this->validateParameter('customerId', $this->param['customerId'], INTEGER);
			$cust = new Customer;
			$cust->setId($customerId);
			if(!$cust->delete()) {
				$message = 'Failed to delete.';
			} else {
				$message = "deleted successfully.";
			}
			$this->returnResponse(SUCCESS_RESPONSE, $message);
		}
	}
?>
	