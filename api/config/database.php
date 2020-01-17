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
	{self::$con =Null;}
}
?>