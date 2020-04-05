<?php

class mysql {

    public $connection = false;
    public function __construct(){
        if($this->connection === false){
            $this->connection = mysqli_connect(
                core::app()->db['host'],
                core::app()->db['user'],
                core::app()->db['password'],
                core::app()->db['database'],
            );
        }
    }

     public function query($connect, $sql)
    {
        $result = mysqli_query($connect, $sql);    
        if(mysqli_insert_id($connect) > 0){
             echo mysqli_insert_id($connect);
         } else{ 
             echo "<br> OK";
            while($row = mysqli_fetch_array($result , MYSQLI_ASSOC)){
                echo "<pre>";
                var_dump($row);
                echo "</pre>";
             }  
            
        }  
     
    } 

  
}