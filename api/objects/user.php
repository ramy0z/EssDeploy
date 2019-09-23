<?php // 'user' object
class User{
 
    // database connection and table name
    private $conn;
    private $table_name = "users";
 
    // object properties
    public $id;
    public $firstname;
    public $lastname;
    public $email;
    public $password;
 
    // constructor
    public function __construct($db){
        $this->conn = $db;
    }


    function create(){
 
        try {
            $conn= DBService::getCon();
            $sql="INSERT INTO test  VALUES ('555555' ,'44444')";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $last_id = $conn->lastInsertId();

             // sanitize
            $this->firstname=htmlspecialchars(strip_tags($this->firstname));
            $this->lastname=htmlspecialchars(strip_tags($this->lastname));
            $this->email=htmlspecialchars(strip_tags($this->email));
            $this->password=htmlspecialchars(strip_tags($this->password));
        
            // bind the values
            $stmt->bindParam(':firstname', $this->firstname);
            $stmt->bindParam(':lastname', $this->lastname);
            $stmt->bindParam(':email', $this->email);
        
            // hash the password before saving to database
            $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
            $stmt->bindParam(':password', $password_hash);
        
            // execute the query, also check if query was successful
            if($stmt->execute()){
                return true;
            }
        
            return false;


            DBService::closeCon();
            $conn = null;
            return $last_id;
        }
        catch(PDOException $e){
            return $e->getMessage();
            conn::closeCon();
            $con = null;
            return "error";
        } 
     
       
    }
     
    // emailExists() method will be here 
}
?>