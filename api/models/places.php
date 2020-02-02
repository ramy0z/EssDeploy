<?php 
	class Places {
		private $id;		
		function setId($id) { $this->id = $id; }
		function getId() { return $this->id; }

		public function __construct() {
			$this->dbConn = DBService::getCon();
		}
		public function getAllCountries() {
			$stmt = $this->dbConn->prepare("SELECT * FROM countries ORDER BY name ASC");
			$stmt->execute();
			$countries = $stmt->fetchAll(PDO::FETCH_ASSOC);
			return $countries;
		}
		public function getStatesById() {
			$stmt = $this->dbConn->prepare("SELECT id,name FROM states WHERE country_id=:country_id ");
			$stmt->bindParam(':country_id', $this->id);
			$stmt->execute();
			$states = $stmt->fetchAll(PDO::FETCH_ASSOC);
			return $states;
		}
		public function getCitiesById() {
			$stmt = $this->dbConn->prepare("SELECT id,name FROM cities WHERE state_id=:state_id ");
			$stmt->bindParam(':state_id', $this->id);
			$stmt->execute();
			$states = $stmt->fetchAll(PDO::FETCH_ASSOC);
			return $states;
		}
	
	}
 ?>