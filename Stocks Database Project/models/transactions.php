<?php

Class Transaction{
    private $user_id, $stock_id, $quantity, $price, $id, $time_stamp; 
    
      public function __construct($user_id, $stock_id, $quantity, $price = 0, $time_stamp = 0, $id = 0){
        $this->set_stock_id($stock_id);
        $this->set_user_id($user_id);
        $this->set_quantity($quantity); 
        $this->set_price($price);
        $this->set_time_stamp($time_stamp);
        $this->set_id($id);
    }
        
    public function get_user_id() {
        return $this->user_id;
    }

    public function get_stock_id() {
        return $this->stock_id;
    }

    public function get_quantity() {
        return $this->quantity;
    }

    public function get_price() {
        return $this->price;
    }

    public function get_id() {
        return $this->id;
    }

    public function get_time_stamp() {
        return $this->time_stamp;
    }

    public function set_user_id($user_id): void {
        $this->user_id = $user_id;
    }

    public function set_stock_id($stock_id): void {
        $this->stock_id = $stock_id;
    }

    public function set_quantity($quantity): void {
        $this->quantity = $quantity;
    }

    public function set_price($price): void {
        $this->price = $price;
    }

    public function set_id($id): void {
        $this->id = $id;
    }

    public function set_time_stamp($time_stamp): void {
        $this->time_stamp = $time_stamp;
    }
} 

function list_transactions(){
    global $database; 
    
    $third_query = "SELECT user_id, stock_id, quantity, price, timestamp, id FROM transactions;"; 
    $third_statement = $database->prepare($third_query);
    $third_statement->execute(); 
    $transactions = $third_statement->fetchAll();
    $third_statement->closeCursor();
    
    $transactions_array = array(); 
    
    foreach($transactions as $stock){
        $transactions_array [] = new Transaction($stock['user_id'], $stock['stock_id'], $stock['quantity'], $stock['price'], $stock['timestamp'], $stock['id']);  
    }
    
    return $transactions_array; 
}

function insert_transactions($stock){
    
    global $database; 
    
    $quantity = filter_input(INPUT_POST, "quantity", FILTER_VALIDATE_FLOAT);
   
    $price_current_query = "SELECT current_price FROM stocks WHERE symbol = :symbol"; // get the price of stock inputted
    $fifth_statement = $database->prepare($price_current_query);
    $fifth_statement->bindValue(":symbol", $stock->get_stock_id());
    $fifth_statement->execute(); 
    $price_current = $fifth_statement->fetch();  
    $current_price_for_stock = $price_current['current_price'];
    $fifth_statement->closeCursor();
    
    echo '</br>Price of Stock Wanted By User: ' . $current_price_for_stock . '</br>'; // displays stock price at the top of the page 
    
    $user_balance_query = "SELECT cash_balance FROM users WHERE id = :user_id"; // gets cash balance to make sure the user can actually purchase the stock(s)
    $sixth_statement = $database->prepare($user_balance_query);
    $sixth_statement->bindValue(":user_id", $stock->get_user_id());
    $sixth_statement->execute(); 
    $balance_current = $sixth_statement->fetch(); 
    $balance_current_for_user = $balance_current['cash_balance'];
    $sixth_statement->closeCursor(); 
    
    if($balance_current_for_user >= $quantity * $current_price_for_stock){ // if the user has enough money to purchase the stock(s)
        
    $balance_updated = $balance_current_for_user - ($quantity * $current_price_for_stock); // new balance after subtraction of purchase 
    
    $user_id_query = "SELECT id FROM users WHERE cash_balance = :cash_balance"; // gets id for current user in order to delete ONLY a unique (SINGULAR) transaction
    $id_statement = $database->prepare($user_id_query);
    $id_statement->bindValue(":cash_balance", $balance_current_for_user);
    $id_statement->execute(); 
    $id_current = $id_statement->fetch(); 
    $id_current_for_user = $id_current['id'];
    $id_statement->closeCursor(); 
    
    $get_stock_query = "SELECT id FROM stocks WHERE symbol = :symbol"; // get id_stock so that symbol and stock_id correlate correctly 
    $stock_statement = $database->prepare($get_stock_query);
    $stock_statement->bindValue(":symbol", $stock->get_stock_id());
    $stock_statement->execute(); 
    $stock_integer = $stock_statement->fetch(); 
    $stock_integer_for_user = $stock_integer['id'];
    $stock_statement->closeCursor();
        
    $query = "INSERT INTO transactions (user_id, stock_id, quantity, price, timestamp, id) VALUES (:user_id, :stock_id, :quantity, :price, current_timestamp(), NULL)"; // finally, insert into transactions

    // value binding in PDO protects against sql injection
    $statement = $database->prepare($query);
    $statement->bindValue(":user_id", $stock->get_user_id());
    $statement->bindValue(":stock_id", $stock_integer_for_user);
    $statement->bindValue(":quantity", $stock->get_quantity());
    $statement->bindValue(":price", $current_price_for_stock);
    $statement->execute();
    $statement->closeCursor();
    
    $decrease_query = "UPDATE users SET cash_balance = :current_price WHERE id = :id"; 
    $increase_balance = $database->prepare($decrease_query);
    $increase_balance->bindValue(":id", $id_current_for_user);
    $increase_balance->bindValue(":current_price", $balance_updated);
    $increase_balance->execute();
    $increase_balance->closeCursor(); 

        }else if($balance_current_for_user < $quantity * $current_price_for_stock){
            echo 'The Specified User Cant Afford To Purchase This Stock Based On The Quantity and Price</br>';
        }
}

function update_transaction($stock){
    
    global $database; 
    
    $get_stock_query = "SELECT id FROM stocks WHERE symbol = :symbol"; // get id_stock so that symbol and stock_id correlate correctly 
    $seventh_statement = $database->prepare($get_stock_query);
    $seventh_statement->bindValue(":symbol", $stock->get_stock_id());
    $seventh_statement->execute(); 
    $stock_integer = $seventh_statement->fetch(); 
    $stock_integer_for_user = $stock_integer['id'];
    $seventh_statement->closeCursor();
    
    $query = "update transactions set stock_id = :symbol, quantity = :quantity " 
            . " where user_id = :user_id"; 
 
    // value binding in PDO protects against sql injection
    $statement = $database->prepare($query);
    $statement->bindValue(":user_id", $stock->get_user_id());
    $statement->bindValue(":symbol", $stock_integer_for_user);
    $statement->bindValue(":quantity", $stock->get_quantity());

    $statement->execute(); 

    $statement->closeCursor();
}

function delete_transaction($stock){
    
    global $database; 
    
    $price = filter_input(INPUT_POST, "current_price", FILTER_VALIDATE_FLOAT); 
    
    $user_balance_query = "SELECT cash_balance FROM users WHERE id = :user_id"; // gets cash balance to make sure the user can actually purchase the stock(s)
    $sixth_statement = $database->prepare($user_balance_query);
    $sixth_statement->bindValue(":user_id", $stock->get_user_id());
    $sixth_statement->execute(); 
    $balance_current = $sixth_statement->fetch(); 
    $balance_current_for_user = $balance_current['cash_balance'];
    $sixth_statement->closeCursor(); 
    
    $user_id_query = "SELECT id FROM users WHERE cash_balance = :cash_balance"; // gets id for current user in order to delete ONLY a unique (SINGULAR) transaction
    $seventh_statement = $database->prepare($user_id_query);
    $seventh_statement->bindValue(":cash_balance", $balance_current_for_user);
    $seventh_statement->execute(); 
    $id_current = $seventh_statement->fetch(); 
    $id_current_for_user = $id_current['id']; 
    $seventh_statement->closeCursor(); 
    
    $user_quantity_query = "SELECT quantity FROM transactions WHERE id = :id"; // gets quantity for a specific user 
    $eighth_statement = $database->prepare($user_quantity_query);
    $eighth_statement->bindValue(":id", $stock->get_id()); 
    $eighth_statement->execute(); 
    $quantity_current = $eighth_statement->fetch(); 
    $quantity_current_for_user = $quantity_current['quantity'];
    $eighth_statement->closeCursor(); 
        
    $total_increase = $balance_current_for_user + $quantity_current_for_user * $price; // finds the amount that's needed in order to add to current balance (+=) 
    
    $another_query = "UPDATE users SET cash_balance = cash_balance + :current_price WHERE id = :user_id"; 
    $increase_balance = $database->prepare($another_query);
    $increase_balance->bindValue(":user_id", $id_current_for_user);
    $increase_balance->bindValue(":current_price", $total_increase);
    $increase_balance->execute();
    $increase_balance->closeCursor(); 
    
    $query = "DELETE FROM transactions WHERE id = :id"; // deletes based on transaction ID in order to ensure unique and singular deletion
    // value binding in PDO protects against sql injection
    $statement = $database->prepare($query);
    $statement->bindValue(":id", $stock->get_id());
    $statement->execute();
    $statement->closeCursor(); 
}