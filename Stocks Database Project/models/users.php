<?php

class User{
    
    private $email_address, $name, $cash_balance, $id;
    
    public function __construct($email_address, $name, $cash_balance, $id = 0) {
        $this->set_email_address($email_address);
        $this->set_name($name);
        $this->set_cash_balance($cash_balance);
        $this->set_id($id);  
    }
    
    public function get_email_address() {
        return $this->email_address;
    }

    public function get_name() {
        return $this->name;
    }

    public function get_cash_balance() {
        return $this->cash_balance;
    }

    public function get_id() {
        return $this->id;
    }

    public function set_email_address($email_address): void {
        $this->email_address = $email_address;
    }

    public function set_name($name): void {
        $this->name = $name;
    }

    public function set_cash_balance($cash_balance): void {
        $this->cash_balance = $cash_balance;
    }

    public function set_id($id): void {
        $this->id = $id;
    }
}

function list_users(){
    
    global $database; 
    
    $new_query = "SELECT name, email_address, cash_balance, id FROM users"; 
    $new_statement = $database->prepare($new_query);
    $new_statement->execute(); 
    $users = $new_statement->fetchAll();
    $new_statement->closeCursor();
    
   $users_array = array(); 
    
    foreach($users as $stock){
        $users_array[] = new User($stock['email_address'], $stock['name'], $stock['cash_balance'], $stock['id']);  
    }
    
    return $users_array; 
}

function insert_user($stock){
    
    global $database; 
    
           $query = "INSERT INTO users (name, email_address, cash_balance) " .
                  "VALUES (:name, :email_address, :cash_balance)";

    // value binding in PDO protects against sql injection
    $statement = $database->prepare($query);
    $statement->bindValue(":name", $stock->get_name());
    $statement->bindValue(":email_address", $stock->get_email_address());
    $statement->bindValue(":cash_balance", $stock->get_cash_balance());

    $statement->execute(); 

    $statement->closeCursor();
}
function update_users($stock){
    
    global $database; 
    
         $query = "UPDATE users SET cash_balance = :cash_balance, email_address = :email_address " 
            . " WHERE name = :name"; 
    
    // value binding in PDO protects against sql injection
    $statement = $database->prepare($query);
    $statement->bindValue(":name", $stock->get_name());
    $statement->bindValue(":email_address", $stock->get_email_address());
    $statement->bindValue(":cash_balance", $stock->get_cash_balance());

    $statement->execute(); 

    $statement->closeCursor();
}
function delete_users($stock){
    
    global $database; 

    $query = "delete from users "
            . " where name = :name"; 
    
    // value binding in PDO protects against sql injection
    $statement = $database->prepare($query);
    $statement->bindValue(":name", $stock->get_name());
    
    $statement->execute(); 
    
    $statement->closeCursor(); 
}