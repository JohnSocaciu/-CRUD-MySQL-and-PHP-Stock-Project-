<?php

function login($email_address, $password){
    
    global $database; 
    
    $new_query = "SELECT email_address, password_hash FROM users WHERE email_address = :email_address"; 
    
    $new_statement = $database->prepare($new_query);
    $new_statement->bindValue(":email_address", $email_address);
    $new_statement->execute(); 
    $user = $new_statement->fetch();
    
    $new_statement->closeCursor();
    if($user == NULL){
        return false; 
    }
    
    $password_hash = $user['password_hash'];
    
    return password_verify($password, $password_hash); 
}