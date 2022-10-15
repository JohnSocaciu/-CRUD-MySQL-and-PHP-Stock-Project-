<?php

try{
    require_once 'utility/ensure_logged_in.php'; 
    require_once 'models/transactions.php';
    require_once 'models/database.php';
    
    $stock_id = htmlspecialchars(filter_input(INPUT_POST, "symbol")); // trans cont
    $user_id = htmlspecialchars(filter_input(INPUT_POST, "user_id"));
    $quantity = filter_input(INPUT_POST, "quantity", FILTER_VALIDATE_FLOAT);
    $id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);
    $price = filter_input(INPUT_POST, "current_price", FILTER_VALIDATE_FLOAT); 
    $time_stamp = [INPUT_POST, "timestamp"];
   
    $action = htmlspecialchars(filter_input(INPUT_POST, "action")); // all files
    
    if($action == "insert" && $user_id != "" && $stock_id != "" && $quantity != 0){
        $transaction = new Transaction($user_id, $stock_id, $quantity); 
        insert_transactions($transaction);
    }
    else if($action == "update" && $user_id != "" && $stock_id != "" && $quantity != 0){ // updates variables in transaction
        $transaction = new Transaction($user_id, $stock_id, $quantity); 
        update_transaction($transaction); 
        header("Location: transactions.php"); 
    }
    else if($action == "delete" && $id != "" && $price != ""){ // deletes a transaction
        $transaction = new Transaction($user_id, "", 0, $price, 0, $id);
        delete_transaction($transaction); 
        header("Location: transactions.php"); 
    }
    else if($action != ""){
        $error_message = "Missing symbol, name, User ID, Transaction Id, or current price"; 
        include('views/error.php');
    }
    
    $transactions = list_transactions(); 
      
    include('views/transactions.php');
    
} catch (Exception $ex) {
        $error_message = $ex->getMessage(); 
            include('views/error.php'); 
} 
