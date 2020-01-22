<?php 
	class User {
		private $id;
		private $email;
		private $uName;
		private $uPass;
		private $role;
		
		private $name;
		private $address;
		private $phone;
		private $image;

		private $supp_name;
		private $supp_address;
		private $supp_phone;
		private $supp_image;

		// private $updatedBy;
		// private $updatedOn;
		// private $createdBy;
		// private $createdOn;
		// private $tableName = 'users';//user_metas
		// function getTblName() { return $this->tableName; }
		// function setTblName($email) { $this->tableName = $tableName; }
		private $dbConn;
		function setId($id) { $this->id = $id; }
		function getId() { return $this->id; }

		function setEmail($email) { $this->email = $email; }
		function getEmail() { return $this->email; }
		function setuName($uName) { $this->uName = $uName; }
		function getuName() { return $this->uName; }
		function setuPass($uPass) { $this->uPass = $uPass; }
		function getuPass() { return $this->uPass; }
		function setRole($role) { $this->role = $role; }
		function getRole() { return $this->role; }
		function setUserType($userType) { $this->userType = $userType; }
		function getUserType() { return $this->userType; }

		function setName($name) { $this->name = $name; }
		function getName() { return $this->name; }
		function setAddress($address) { $this->address = $address; }
		function getAddress() { return $this->address; }
		function setPhone($phone) { $this->phone = $phone; }
		function getPhone() { return $this->phone; }
		function setImage($image) { $this->image = $image; }
		function getImage() { return $this->image; }

		function setSuppName($supp_name) { $this->supp_name = $supp_name; }
		function getSuppName() { return $this->supp_name; }
		function setSuppAddress($supp_address) { $this->supp_address = $supp_address; }
		function getSuppAddress() { return $this->supp_address; }
		function setSuppPhone($supp_phone) { $this->supp_phone = $supp_phone; }
		function getSuppPhone() { return $this->supp_phone; }
		function setSuppImage($supp_image) { $this->supp_image = $supp_image; }
		function getSuppImage() { return $this->supp_image; }
		

		// function setUpdatedBy($updatedBy) { $this->updatedBy = $updatedBy; }
		// function getUpdatedBy() { return $this->updatedBy; }
		// function setUpdatedOn($updatedOn) { $this->updatedOn = $updatedOn; }
		// function getUpdatedOn() { return $this->updatedOn; }
		// function setCreatedBy($createdBy) { $this->createdBy = $createdBy; }
		// function getCreatedBy() { return $this->createdBy; }
		// function setCreatedOn($createdOn) { $this->createdOn = $createdOn; }
		// function getCreatedOn() { return $this->createdOn; }
		public function __construct() {
			$this->dbConn = DBService::getCon();
		}
		public function getAllUsers() {
			$stmt = $this->dbConn->prepare("SELECT * FROM " . $this->tableName);
			$stmt->execute();
			$Users = $stmt->fetchAll(PDO::FETCH_ASSOC);
			return $Users;
		}
		public function getUserDetailsById() {
			$sql = "SELECT 
						c.*, 
						u.name as created_user,
						u1.name as updated_user
					FROM users c 
						JOIN users u ON (c.created_by = u.id) 
						LEFT JOIN users u1 ON (c.updated_by = u1.id) 
					WHERE 
						c.id = :userId";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindParam(':userId', $this->id);
			$stmt->execute();
			$user = $stmt->fetch(PDO::FETCH_ASSOC);
			return $user;
		}
		
		public function createUser() {
			try {
				$uName =$this->uName;
				$email = $this->email;
				$userPhone = $this->phone;
				$userRole = $this->role;
				$userPass= $this->userPass;
				$user_type=($this->userType =='cust')?1:($this->userType=='sup')?2:($this->userType=='emp')?3:-1;
				//customer , supplier , employee , admin -1;

				$conn= DBService::getCon();
				$conn->beginTransaction();
				$sql="INSERT INTO acc_entry (entryNm , parent_id) VALUES (:uName ,:user_type)";
				$stmt = $conn->prepare($sql);
				$stmt->bindParam(':uName', $uName);
				$stmt->bindParam(':user_type', $user_type);
				$stmt->execute();
				$entry_id = $conn->lastInsertId();

				$sql="INSERT INTO users (email, usrPass ,entry_id ,phone,role_id ,active) VALUES (:email , :usrPass , :entry_id , :phone ,:usrRole ,1)";
				$stmt = $conn->prepare($sql);
				$password_hash = password_hash($userPass, PASSWORD_BCRYPT);
				$stmt->bindParam(':email', $email);
				$stmt->bindParam(':usrPass', $password_hash);
				$stmt->bindParam(':phone', $userPhone);
				$stmt->bindParam(':usrRole', $userRole);
				$stmt->bindParam(':entry_id', $entry_id);
				$stmt->execute();
				$uId = $conn->lastInsertId();


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
		public function updateOrAdd() {
			$valus_arr=[];
			if( null != $this->getName()) {
				array_push($valus_arr	,'('.$this->id.', "name" , "'. $this->name.'" )');
			}
			if( null != $this->getAddress()) {
				array_push($valus_arr	,'('.$this->id.', "address" , "'. $this->address.'" )');
			}
			// if( null != $this->getPhone()) {
			// 	array_push($valus_arr	,'('.$this->id.', "phone" , "'. $this->phone.'" )');
			// }
			if( null != $this->getImage()) {
				array_push($valus_arr	,'('.$this->id.', "image" , "'. $this->image.'" )');
			}

			if( null != $this->getSuppName()) {
				array_push($valus_arr	,'('.$this->id.', "supp_name" , "'. $this->supp_name.'" )');
			}
			if( null != $this->getSuppAddress()) {
				array_push($valus_arr	,'('.$this->id.', "supp_address" , "'. $this->supp_address.'" )');
			}
			if( null != $this->getSuppPhone()) {
				array_push($valus_arr	,'('.$this->id.', "supp_phone" , "'. $this->supp_phone.'" )');
			}
			if( null != $this->getSuppImage()) {
				array_push($valus_arr	,'('.$this->id.', "supp_image" , "'. $this->supp_image.'" )');
			}

			if(count($valus_arr)==0){
				return false;
			}
			
			$arrSqlStr=implode(",",$valus_arr); // 'VALUES (13,'address', 'kafr-elshikh'),(13,'phone', '01065353143')'
			//echo $arrSqlStr;
			$sql =	'INSERT INTO user_metas (user_id, user_key ,value) VALUES '.$arrSqlStr.
			'ON DUPLICATE KEY UPDATE value = VALUES(value)';
			$stmt = $this->dbConn->prepare($sql);
			if($stmt->execute()) {
				return true;
			} else {
				return false;
			}
		}
		public function delete() {
			$stmt = $this->dbConn->prepare('DELETE FROM ' . $this->tableName . ' WHERE id = :userId');
			$stmt->bindParam(':userId', $this->id);
			
			if($stmt->execute()) {
				return true;
			} else {
				return false;
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

		//INSERT INTO user_metas (user_id, user_key ,value)
		// VALUES (13,'address', 'kafr-elshikh'), 
		//(13,'phone', '01065353143') ON DUPLICATE KEY UPDATE value = VALUES(value)

	}
 ?>