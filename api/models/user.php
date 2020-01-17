<?php 
	class User {
		private $id;
		private $name;
		private $email;
		private $address;
		private $mobile;
		private $image;

		private $supp_name;
		private $supp_address;
		private $supp_mobile;
		private $supp_image;

		private $updatedBy;
		private $updatedOn;
		private $createdBy;
		private $createdOn;
		private $tableName = 'users';//user_metas
		function getTblName() { return $this->tableName; }
		function setTblName($email) { $this->tableName = $tableName; }
		private $dbConn;
		function setId($id) { $this->id = $id; }
		function getId() { return $this->id; }
		function setName($name) { $this->name = $name; }
		function getName() { return $this->name; }
		function setEmail($email) { $this->email = $email; }
		function getEmail() { return $this->email; }
		
		function setAddress($address) { $this->address = $address; }
		function getAddress() { return $this->address; }
		function setMobile($mobile) { $this->mobile = $mobile; }
		function getMobile() { return $this->mobile; }
		function setImage($image) { $this->image = $image; }
		function getImage() { return $this->image; }

		function setSuppName($supp_name) { $this->supp_name = $supp_name; }
		function getSuppName() { return $this->supp_name; }
		function setSuppAddress($supp_address) { $this->supp_address = $supp_address; }
		function getSuppAddress() { return $this->supp_address; }
		function setSuppMobile($supp_mobile) { $this->supp_mobile = $supp_mobile; }
		function getSuppMobile() { return $this->supp_mobile; }
		function setSuppImage($supp_image) { $this->supp_image = $supp_image; }
		function getSuppImage() { return $this->supp_image; }
		

		function setUpdatedBy($updatedBy) { $this->updatedBy = $updatedBy; }
		function getUpdatedBy() { return $this->updatedBy; }
		function setUpdatedOn($updatedOn) { $this->updatedOn = $updatedOn; }
		function getUpdatedOn() { return $this->updatedOn; }
		function setCreatedBy($createdBy) { $this->createdBy = $createdBy; }
		function getCreatedBy() { return $this->createdBy; }
		function setCreatedOn($createdOn) { $this->createdOn = $createdOn; }
		function getCreatedOn() { return $this->createdOn; }
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
		
		public function insert() {
			
			$sql = 'INSERT INTO ' . $this->tableName . '(id, name, email, address, mobile, created_by, created_on) VALUES(null, :name, :email, :address, :mobile, :createdBy, :createdOn)';
			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindParam(':name', $this->name);
			$stmt->bindParam(':email', $this->email);
			$stmt->bindParam(':address', $this->address);
			$stmt->bindParam(':mobile', $this->mobile);
			$stmt->bindParam(':createdBy', $this->createdBy);
			$stmt->bindParam(':createdOn', $this->createdOn);
			
			if($stmt->execute()) {
				return true;
			} else {
				return false;
			}
		}
		public function updateOrAdd() {
			
			$sql = "UPDATE $this->tableName SET";
			$valus_arr=[];
			if( null != $this->getName()) {
				array_push($valus_arr	,'('.$this->id.', "name" , "'. $this->name.'" )');
			}
			if( null != $this->getAddress()) {
				array_push($valus_arr	,'('.$this->id.', "address" , "'. $this->address.'" )');
			}
			if( null != $this->getMobile()) {
				array_push($valus_arr	,'('.$this->id.', "mobile" , "'. $this->mobile.'" )');
			}
			if( null != $this->getImage()) {
				array_push($valus_arr	,'('.$this->id.', "image" , "'. $this->image.'" )');
			}

			if( null != $this->getSuppName()) {
				array_push($valus_arr	,'('.$this->id.', "supp_name" , "'. $this->supp_name.'" )');
			}
			if( null != $this->getSuppAddress()) {
				array_push($valus_arr	,'('.$this->id.', "supp_address" , "'. $this->supp_address.'" )');
			}
			if( null != $this->getSuppMobile()) {
				array_push($valus_arr	,'('.$this->id.', "supp_mobile" , "'. $this->supp_mobile.'" )');
			}
			if( null != $this->getSuppImage()) {
				array_push($valus_arr	,'('.$this->id.', "supp_image" , "'. $this->supp_image.'" )');
			}

			if(count($valus_arr)==0){
				return false;
			}
			
			$arrSqlStr=implode(",",$valus_arr); // 'VALUES (13,'address', 'kafr-elshikh'),(13,'phone', '01065353143')'

			$sql .=	'INSERT INTO user_metas (user_id, user_key ,value)'.$arrSqlStr.
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

		//INSERT INTO user_metas (user_id, user_key ,value)
		// VALUES (13,'address', 'kafr-elshikh'), 
		//(13,'phone', '01065353143') ON DUPLICATE KEY UPDATE value = VALUES(value)

	}
 ?>