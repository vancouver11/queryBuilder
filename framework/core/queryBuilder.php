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

            if(isset($this->parts['keysInsert']) && isset($this->parts['valuesInsert'])){
                $this->sql = 'INSERT INTO '.
                 ' ('. $this->parts['keysInsert'] . ') VALUES ('.$this->parts['valuesInsert'].')'; 
                unset($this->parts['keysInsert']);
                unset($this->parts['valuesInsert']);
               
             }

             if (isset($this->parts['intoTable'])){
                $start = strpos($this->sql, 'INTO ');
                $start +=5;
                $this->sql = substr_replace($this->sql, $this->parts['intoTable'], $start,0);
                unset($this->parts['intoTable']);
             }
             
             if(isset($this->parts['updateTable'])){
                $this->sql = 'UPDATE '. $this->parts['updateTable'];
                unset($this->parts['updateTable']);
             }
    
             if( isset($this->parts['set'])){
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
                     unset($this->parts['set']);
             }

            
            if(isset($this->parts['select'])){
               $this->sql = 'SELECT '. $this->parts['select'] ;
               unset($this->parts['select']);    
            }
            if(isset($this->parts['delete'])){
                $this->sql = 'DELETE ';
                unset($this->parts['delete']); 
             }
            if( isset($this->parts['from'])){
                $this->sql .= ' FROM '. $this->parts['from'];
                unset($this->parts['from']);
            }
            if(isset($this->parts['joinTable'])){

                $this->sql .= " JOIN ". reset($this->parts['joinTable']);
              
                array_shift($this->parts['joinTable']);
                if(count($this->parts['joinTable']) == 0){
                    unset($this->parts['joinTable']); 
                }  
            }

            if(isset($this->parts['on'])){
                $this->sql .= " ON ". reset($this->parts['on']);
            
                array_shift($this->parts['on']);
                if(count($this->parts['on']) == 0){
                    unset($this->parts['on']); 
                }  
            }
            if(isset($this->parts['where'])){
                $this->sql .= ' WHERE '.$this->parts['where'] ;
                unset($this->parts['where']);
            }
            if(isset($this->parts['order'])){
                $this->sql .= ' ORDER BY '. $this->parts['order'];
                unset($this->parts['order']);
            }
            if(isset($this->parts['limit'])){
                $this->sql .= ' LIMIT '. $this->parts['limit'];
                unset($this->parts['limit']);
            }


           if(count($this->parts) !== 0){
            $this->getText();
           }


           return $this->sql;
           
   
        }




}