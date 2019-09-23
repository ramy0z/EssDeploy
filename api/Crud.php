<?php

header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

error_reporting(E_ERROR | E_PARSE);

include_once 'DbConfig.php';

class Crud extends DbConfig{
    public function __construct(){
        parent::__construct();
    }
    
    public function getData($query){
        $result=$this->connection->query($query);
        if($result==false){
            echo $this->connection->error;
            return false;
        }
        
        $rows=array();
        while($row=$result->fetch_assoc()){
            $rows[]=$row;
        }
        return $rows;
    }
    
    public function execute($query){
        $result=$this->connection->query($query);
        if($result==false){
            echo $this->connection->error;
            return false;
        }else{
            return true;
        }
    }
        
    public function escape_string($value){
        return $this->connection->real_escape_string($value);
    }
}