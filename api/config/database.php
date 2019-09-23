<?php
// used to get mysql database connection
class DBService{
    private static $con = Null;
	public static function getCon()
	{
		$servername = "localhost";
		$username = "root";
		$password = "";
		$dbname = "essdb";
		if(!isset(self::$con))
		{
			self::$con = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
			self::$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


		}
		return self::$con;
	}
	
	public static function closeCon()
	{
		 self::$con =Null;
	}
    // private $db_host = "localhost";
    // private $db_name = "essdb";
    // private $db_user = "root";
    // private $db_password = "";
    // private $connection;

    // public function getConnection(){

    //     $this->connection = null;

    //     try{
    //         $this->connection = new PDO("mysql:host=" . $this->db_host . ";dbname=" . $this->db_name, $this->db_user, $this->db_password);
    //     }catch(PDOException $exception){
    //         echo "Connection failed: " . $exception->getMessage();
    //     }

    //     return $this->connection;
    // }
}
?>