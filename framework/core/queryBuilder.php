<?php

class queryBuilder{

    protected $parts = [];
    protected $sql = "";
    protected $dbcon;
    public function __construct(){
        $this->dbcon = new mysql();
    }
    
    public function select( $fields = "*"){
        
        $this->parts['select'] = $fields;
        return $this;
     }
     
     public function where( $conditions){
         $this->parts['where'] = $conditions;
         return $this;
     }
 
     public function from ($table){
         $this->parts['from']= $table;
         return $this;
     }


     public function insert(array $fields){
         $keys = [];
         $values = [];
        foreach ($fields as $key => $value) {
            $keys[] = $key;
            if(!is_int($value)){
                $values[] = '\''.$value.'\'';
            }else{
                $values[] = $value;
            }
            
        }
        $this->parts['keysInsert'] = implode(', ', $keys);
        $this->parts['valuesInsert'] = implode(', ', $values);
        return $this;
     }

     public function into( string $table){
        $this->parts['intoTable']= $table;
        return $this;
     }

     public function delete(){
         $this->parts['delete'] = "delete";
         return $this;
     }

     public function limit(int $num){
        $this->parts['limit'] = $num;
        return $this;
     }

     public function order(string $expression){
        $this->parts['order'] = $expression;
        return $this;
     }

     public function update(string $table){
        $this->parts['updateTable'] = $table;
        return $this;
     }
     public function set(array $expression){
        $this->parts['set'] = $expression;
        return $this;
     }

     public function join(string$table){
        $this->parts['joinTable'][$table] = $table;
        return $this;
     }

    public function on (string $conditition){
        $this->parts['on'][$conditition] = $conditition;
        return $this;
    }
 
     public function execute(){
          $this->dbcon->query($this->dbcon->connection, $this->getText()); 
         /* mysqli_close($this->dbcon->connection); */    
        
     }



        public function getText(){
            //INSERT
            if(isset($this->parts['keysInsert']) && isset($this->parts['valuesInsert']) && isset($this->parts['intoTable'])){
                $this->sql = 'INSERT INTO '. $this->parts['intoTable'].
                 ' ('. $this->parts['keysInsert'] . ') VALUES ('.$this->parts['valuesInsert'].')'; 
               unset($this->parts['keysInsert']);
                unset($this->parts['valuesInsert']);
                unset($this->parts['intoTable']); 
             }

        
 //---------------------------------------------------------------------------------------------  
 
            //UPDATE
             if(isset($this->parts['updateTable']) && isset($this->parts['set'])){
                $this->sql = 'UPDATE '. $this->parts['updateTable'];

                $setExpession ='';
                foreach ($this->parts['set'] as $key => $value) {
                   if($value == end($this->parts['set'])) {
                       $setExpession .= $key .'='. '\''. $value .'\' ';
                 }
                 else {
                   $setExpession .= $key .'='. '\''. $value .'\', ';
                 }
                 
                }
                $this->sql .= ' SET ' . $setExpession;  

                if(isset($this->parts['where'])){
                    $this->sql .=' WHERE '.$this->parts['where'];
                   }
               unset($this->parts['updateTable']);
               unset($this->parts['set']); 
               unset($this->parts['where']);
             }

//----------------------------------------- 

        //SELECT
            if(isset($this->parts['select']) && isset($this->parts['from'])){
               $this->sql = 'SELECT '. $this->parts['select'] . ' FROM '. $this->parts['from'] ;
               if(isset($this->parts['where'])){
                $this->sql .=' WHERE '.$this->parts['where'];
               }
                unset($this->parts['select']);  
                unset($this->parts['from']) ; 
                unset($this->parts['where']);
            }
//-------------------------------------------------------
        //DELETE

            if(isset($this->parts['delete']) &&  isset($this->parts['from'])){
                $this->sql = 'DELETE '. ' FROM '. $this->parts['from'];
                if(isset($this->parts['where'])){
                    $this->sql .=' WHERE '.$this->parts['where'];
                   }
               unset($this->parts['delete']); 
               unset($this->parts['from']); 
               unset($this->parts['where']);
             }
//-----------------------------------------------------------
            //JOIN
            if(isset($this->parts['joinTable']) && isset($this->parts['on'])){

                $this->sql .= " JOIN ". reset($this->parts['joinTable']) ." ON ". reset($this->parts['on']);
             
                array_shift($this->parts['joinTable']);
                array_shift($this->parts['on']);
                if(count($this->parts['joinTable']) == 0 && $this->parts['on'] ==0){
                    unset($this->parts['joinTable']);
                    unset($this->parts['on']);  
                }  

 
            }
//---------------------------------------------------------
            //ORDER
            if(isset($this->parts['order'])){
                $this->sql .= ' ORDER BY '. $this->parts['order'];
                //unset($this->parts['order']);
            }
//---------------------------------------------------------
            //LIMIT
            if(isset($this->parts['limit'])){
                $this->sql .= ' LIMIT '. $this->parts['limit'];
               // unset($this->parts['limit']);
            }
//---------------------------------------------------------

           if(isset($this->parts['joinTable']) || isset($this->parts['on']) ){
                if(count($this->parts['joinTable']) !== 0 && count($this->parts['on']) !==0){
                    $this->getText();
                }  
                else{
                    unset($this->parts);
                } 
           }
 


           return $this->sql;
           
   
        }




}