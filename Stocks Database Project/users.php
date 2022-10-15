<?php

try{
    require_once 'utility/ensure_logged_in.php'; 
    require_once 'models/database.php';
    require_once 'models/users.php';  

    $action = htmlspecialchars(filter_input(INPUT_POST, "action")); // all files

    $name = htmlspecialchars(filter_input(INPUT_POST, "name")); // user cont
    $email_address = htmlspecialchars(filter_input(INPUT_POST, "email_address", FILTER_VALIDATE_EMAIL)); 
    $cash_balance = filter_input(INPUT_POST, "cash_balance", FILTER_VALIDATE_FLOAT); 

    if($action == "insert" && $name != "" && $email_address != "" && $cash_balance != 0){
        $user = new User($email_address, $name, $cash_balance); 
        insert_user($user); 
        header("Location: users.php");
    }
    else if($action == "update" && $name != "" && $email_address != "" && $cash_balance != 0){
        $user = new User($email_address, $name, $cash_balance); 
        update_users($user); 
        header("Location: users.php"); 
    }
    else if($action == "delete" && $name != ""){
        $user = new User("", $name, 0); 
        delete_users($user);
        header("Location: users.php"); 
    }
    else if($action != ""){
        $error_message = "Missing symbol, name, or current price"; 
        include('views/error.php');
    }
    
    $users = list_users(); 
    include('views/users.php'); 
}
 catch (Exception $ex) {
        $error_message = $ex->getMessage(); 
            include('views/error.php'); 
} 
