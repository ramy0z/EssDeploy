<?php 
require_once './JwtApi.php';
require_once "./vendor/autoload.php";
use \Firebase\JWT\JWT;
	class Api  extends Rest {
		
		public function __construct() {
			parent::__construct();
		}
		
		public function upload_image(){
			$uploadFile = new uploadFile();
			$uploadFile->Uploader_Image(13,$_File);
			echo "Dataaaaaaaaaa of app";
		}

		public function get_App_init(){
			echo "Dataaaaaaaaaa of app get_App_init";
		}

		public function ProcessNewUser(){
			require_once('./models/user.php');
			//print_r($this->param);
			$uName = $this->validateParameter('User Name', $this->param['uName'], STRING, true);
			$email = $this->validateParameter('Email', $this->param['email'], EMAIL, true);
			$userPhone = $this->validateParameter('Phone', $this->param['phone'], INTEGER, true);
			$userRole = $this->validateParameter('Role', $this->param['role'], INTEGER, true);
			$userPass = $this->validateParameter('User Pass', $this->param['uPass'], STRING, true);
			$user_type = $this->validateParameter('User Type', $this->param['uType'], STRING, true);//cust , sup , emp ;
			
			$user= new User();
			$returnVal= $user->checkMailUserExist( $email ,$uName ,$userPhone);
            if( is_array($returnVal) ){
                $this->throwError(FAILD_RESPONSE, $returnVal[1]);
            }
            else{
				
				$user->setuName($uName);
				$user->setEmail($email);
				$user->setPhone($userPhone);
				$user->setRole($userRole);
				$user->setuPass($setuPass);
				$user->setUserType($user_type);
				if(!$user->createUser()) {
					$message = 'Failed to Process NEW User Data.';
				} else {
					$message = "Process Done Successfully.";
				}
				$this->returnResponse(SUCCESS_RESPONSE, $message);
			}
		}
		public function ProcessUserMeta(){
			require_once('./models/user.php');
			//print_r($this->param);
			$user_id = $this->validateParameter('uid', $this->param['uid'], INTEGER, true);
			$name = $this->validateParameter('name', $this->param['name'], STRING, false);
			$addr = $this->validateParameter('addr', $this->param['addr'], STRING, false);
			$mobile = $this->validateParameter('mobile', $this->param['mobile'], INTEGER, false);
			// echo "Dataaaaaaaaaa of app get_App_init";
			$user= new User();
			$user->setId($user_id);
			$user->setName($name);
			$user->setEmail($email);
			$user->setAddress($addr);
			$user->setMobile($mobile);
			if(!$user->updateOrAdd()) {
				$message = 'Failed to Process User Data.';
			} else {
				$message = "Process Done Successfully.";
			}
			$this->returnResponse(SUCCESS_RESPONSE, $message);
		}

		public	function ProcessPlaces(){
			require_once('./models/places.php');
			$type = $this->validateParameter('type', $this->param['type'], STRING, true);
			$places= new Places();
			if($type=='country'){
				$Arr=$places->getAllCountries();
				$this->returnResponse(SUCCESS_RESPONSE, $Arr);

			}
			elseif ($type=='state') {
				$id = $this->validateParameter('id', $this->param['id'], INTEGER, true);
				$places->setId($id);
				$Arr=$places->getStatesById();
				$this->returnResponse(SUCCESS_RESPONSE, $Arr);
			}
			elseif ($type=='city') {
				$id = $this->validateParameter('id', $this->param['id'], INTEGER, true);
				$places->setId($id);
				$Arr=$places->getCitiesById();
				$this->returnResponse(SUCCESS_RESPONSE, $Arr);
			}
			else {
				$this->returnResponse(FAILD_RESPONSE, "Invalid Type Value");
			}

		}



	
		public function addCustomer() {
			$name = $this->validateParameter('name', $this->param['name'], STRING, false);
			$email = $this->validateParameter('email', $this->param['email'], EMAIL, false);
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
	