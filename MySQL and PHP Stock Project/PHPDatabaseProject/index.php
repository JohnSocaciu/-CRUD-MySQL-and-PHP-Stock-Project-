<?php
// database server type, location, database name
$data_source_name = 'mysql:host=localhost;dbname=stock'; 
// feels bad, but we don't have time to show a better way 
        
$username = 'stockuser'; 
$password = 'test'; 

try{
    
$database = new PDO($data_source_name, $username, $password);
echo "<p>Database Connection Successful</p>"; 

$action = htmlspecialchars(filter_input(INPUT_POST, "action"));

$symbol = htmlspecialchars(filter_input(INPUT_POST, "symbol")); 
$name = htmlspecialchars(filter_input(INPUT_POST, "name")); 
$current_price = filter_input(INPUT_POST, "current_price", FILTER_VALIDATE_FLOAT); 

$name_user = htmlspecialchars(filter_input(INPUT_POST, "name"));
$email_address = htmlspecialchars(filter_input(INPUT_POST, "email_address", FILTER_VALIDATE_EMAIL)); 
$cash_balance = filter_input(INPUT_POST, "cash_balance", FILTER_VALIDATE_FLOAT); 

$symbol_transaction = htmlspecialchars(filter_input(INPUT_POST, "symbol"));
$user_id = htmlspecialchars(filter_input(INPUT_POST, "user_id"));
$quantity = filter_input(INPUT_POST, "quantity", FILTER_VALIDATE_FLOAT);
$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

if($action == "insert" && $symbol != "" && $name != "" && $current_price != 0){
    // Danger Danger Danger - SQL injection risk
    // Don't ever just plug values into a query 
    // $query = "INSERT INTO stocks (symbol, name, current_price, id) VALUES ($symbol, $name, $current_price)"; 
    
    //instead, use substitutions
    $query = "INSERT INTO stocks (symbol, name, current_price) " .
             "VALUES (:symbol, :name, :current_price)";
    
    // value binding in PDO protects against sql injection
    $statement = $database->prepare($query);
    $statement->bindValue(":symbol", $symbol);
    $statement->bindValue(":name", $name);
    $statement->bindValue(":current_price", $current_price);
    
    $statement->execute(); 
    
    $statement->closeCursor();
    } else if($action == "update" && $symbol != "" && $name != "" && $current_price != 0){
        $query = "update stocks set name = :name, current_price = :current_price " 
            . " where symbol = :symbol"; 
    
    // value binding in PDO protects against sql injection
    $statement = $database->prepare($query);
    $statement->bindValue(":symbol", $symbol);
    $statement->bindValue(":name", $name);
    $statement->bindValue(":current_price", $current_price);
    
    $statement->execute(); 
    
    $statement->closeCursor();
        
    }else if($action == "delete" && $symbol != ""){
        $query = "delete from stocks "
            . " where symbol = :symbol"; 
    
    // value binding in PDO protects against sql injection
    $statement = $database->prepare($query);
    $statement->bindValue(":symbol", $symbol);
    
    $statement->execute(); 
    
    $statement->closeCursor(); 
    }
    else if($action == "insert" && $name_user != "" && $email_address != "" && $cash_balance != 0){
        $query = "INSERT INTO users (name, email_address, cash_balance) " .
                  "VALUES (:name, :email_address, :cash_balance)";

    // value binding in PDO protects against sql injection
    $statement = $database->prepare($query);
    $statement->bindValue(":name", $name_user);
    $statement->bindValue(":email_address", $email_address);
    $statement->bindValue(":cash_balance", $cash_balance);

    $statement->execute(); 

    $statement->closeCursor();
    }
    else if($action == "update" && $name_user != "" && $email_address != "" && $cash_balance != 0){
        $query = "UPDATE users SET cash_balance = :cash_balance, email_address = :email_address " 
            . " WHERE name = :name"; 
    
    // value binding in PDO protects against sql injection
    $statement = $database->prepare($query);
    $statement->bindValue(":name", $name_user);
    $statement->bindValue(":email_address", $email_address);
    $statement->bindValue(":cash_balance", $cash_balance);

    $statement->execute(); 

    $statement->closeCursor();
    }
    else if($action == "delete" && $name_user != ""){
        $query = "delete from users "
            . " where name = :name"; 
    
    // value binding in PDO protects against sql injection
    $statement = $database->prepare($query);
    $statement->bindValue(":name", $name_user);
    
    $statement->execute(); 
    
    $statement->closeCursor(); 
    }
    else if($action == "insert" && $user_id != "" && $symbol_transaction != "" && $quantity != 0){
  
    $price_current_query = "SELECT current_price FROM stocks WHERE symbol = :symbol"; // get the price of stock inputted
    $fifth_statement = $database->prepare($price_current_query);
    $fifth_statement->bindValue(":symbol", $symbol_transaction);
    $fifth_statement->execute(); 
    $price_current = $fifth_statement->fetch();  
    $current_price_for_stock = $price_current['current_price'];
    $fifth_statement->closeCursor();
    
    echo '</br>Price of Stock Wanted By User: ' . $current_price_for_stock; // displays stock price at the top of the page 
    
    $user_balance_query = "SELECT cash_balance FROM users WHERE id = :user_id"; // gets cash balance to make sure the user can actually purchase the stock(s)
    $sixth_statement = $database->prepare($user_balance_query);
    $sixth_statement->bindValue(":user_id", $user_id);
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
    $id_current_for_user = $id_current['user_id'] ?? '1';
    $id_statement->closeCursor(); 
    
    $get_stock_query = "SELECT id FROM stocks WHERE symbol = :symbol"; // get id_stock so that symbol and stock_id correlate correctly 
    $stock_statement = $database->prepare($get_stock_query);
    $stock_statement->bindValue(":symbol", $symbol_transaction);
    $stock_statement->execute(); 
    $stock_integer = $stock_statement->fetch(); 
    $stock_integer_for_user = $stock_integer['id'];
    $stock_statement->closeCursor();
        
    $query = "INSERT INTO transactions (user_id, stock_id, quantity, price, timestamp, id) VALUES (:user_id, :stock_id, :quantity, :price, current_timestamp(), NULL)"; // finally, insert into transactions

    // value binding in PDO protects against sql injection
    $statement = $database->prepare($query);
    $statement->bindValue(":user_id", $user_id);
    $statement->bindValue(":stock_id", $stock_integer_for_user);
    $statement->bindValue(":quantity", $quantity);
    $statement->bindValue(":price", $current_price_for_stock);
    
    $decrease_query = "UPDATE users SET cash_balance = :current_price WHERE id = :id"; 
    $increase_balance = $database->prepare($decrease_query);
    $increase_balance->bindValue(":id", $id_current_for_user);
    $increase_balance->bindValue(":current_price", $balance_updated);
    $increase_balance->execute();
    $increase_balance->closeCursor(); 

    $statement->execute(); 

    $statement->closeCursor();
        }else{
            echo '</br>The Specified User Cant Afford To Purchase This Stock</br>';
        }
    }
    else if($action == "update" && $user_id != "" && $symbol_transaction != "" && $quantity != 0){ // updates variables in transaction
        
    $get_stock_query = "SELECT id FROM stocks WHERE symbol = :symbol"; // get id_stock so that symbol and stock_id correlate correctly 
    $seventh_statement = $database->prepare($get_stock_query);
    $seventh_statement->bindValue(":symbol", $symbol_transaction);
    $seventh_statement->execute(); 
    $stock_integer = $seventh_statement->fetch(); 
    $stock_integer_for_user = $stock_integer['id'];
    $seventh_statement->closeCursor();
    
    $query = "update transactions set stock_id = :symbol, quantity = :quantity " 
            . " where user_id = :user_id"; 
 
    // value binding in PDO protects against sql injection
    $statement = $database->prepare($query);
    $statement->bindValue(":user_id", $user_id);
    $statement->bindValue(":symbol", $stock_integer_for_user);
    $statement->bindValue(":quantity", $quantity);

    $statement->execute(); 

    $statement->closeCursor();
    }
    else if($action == "delete" && $id != "" && $current_price != ""){ // deletes a transaction
        
    $user_balance_query = "SELECT cash_balance FROM users WHERE id = :id"; // gets cash balance to make sure the user can actually purchase the stock(s)
    $sixth_statement = $database->prepare($user_balance_query);
    $sixth_statement->bindValue(":id", $id);
    $sixth_statement->execute(); 
    $balance_current = $sixth_statement->fetch(); 
    $balance_current_for_user = $balance_current['cash_balance'] ?? '0';
    $sixth_statement->closeCursor(); 
    
    $user_id_query = "SELECT id FROM users WHERE cash_balance = :cash_balance"; // gets id for current user in order to delete ONLY a unique (SINGULAR) transaction
    $seventh_statement = $database->prepare($user_id_query);
    $seventh_statement->bindValue(":cash_balance", $balance_current_for_user);
    $seventh_statement->execute(); 
    $id_current = $seventh_statement->fetch(); 
    $id_current_for_user = $id_current['user_id'] ?? '1';
    $seventh_statement->closeCursor(); 
    
    $user_quantity_query = "SELECT quantity FROM transactions WHERE id = :id"; // gets quantity for a specific user 
    $eighth_statement = $database->prepare($user_quantity_query);
    $eighth_statement->bindValue(":id", $id); 
    $eighth_statement->execute(); 
    $quantity_current = $eighth_statement->fetch(); 
    $quantity_current_for_user = $quantity_current['quantity'];
    $eighth_statement->closeCursor(); 
        
    $total_increase = $balance_current_for_user + $quantity_current_for_user * $current_price; // finds the amount that's needed in order to add to current balance (+=) 
    
    $another_query = "UPDATE users SET cash_balance = cash_balance + :current_price WHERE id = :id"; 
    $increase_balance = $database->prepare($another_query);
    $increase_balance->bindValue(":id", $id_current_for_user);
    $increase_balance->bindValue(":current_price", $total_increase);
    $increase_balance->execute();
    $increase_balance->closeCursor(); 
    
    $query = "DELETE FROM transactions WHERE id = :id"; // deletes based on transaction ID in order to ensure unique and singular deletion
    // value binding in PDO protects against sql injection
    $statement = $database->prepare($query);
    $statement->bindValue(":id", $id);
    $statement->execute();
    $statement->closeCursor(); 
    }
    else if($action != ""){
      echo "<p> Missing symbol, name, or current price </p>"; 
    }
    
    $third_query = "SELECT user_id, stock_id, quantity, price, timestamp, id FROM transactions;"; 
    $third_statement = $database->prepare($third_query);
    $third_statement->execute(); 
    $transactions = $third_statement->fetchAll();
    $third_statement->closeCursor();
 
    $new_query = "SELECT name, email_address, cash_balance, id FROM users"; 
    $new_statement = $database->prepare($new_query);
    $new_statement->execute(); 
    $users = $new_statement->fetchAll();
    $new_statement->closeCursor();
        
    $query = "SELECT symbol, name, current_price, id FROM stocks";

    // prepare the query please
    $statement = $database->prepare($query);

    // run the query please
    $statement->execute(); 

    $stocks = $statement->fetchAll(); 
    
    $statement->closeCursor(); 
    }
    catch (Exception $e) {
        $error_message = $e->getMessage(); 
        echo "<p> Error message: error: $error_message </p>"; 
    } 
?>

<?php 

?> 

<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <table> 
            <tr>
                <th>Name</th>
                <th>Symbol</th> 
                <th>Current Price</th>
                <th>ID</th> 
            </tr> 
        <?php foreach($stocks as $stock): ?>
            <tr> 
                <td><?php echo $stock['name']; ?></td>
                <td><?php echo $stock['symbol']; ?></td> 
                <td><?php echo $stock['current_price']; ?></td> 
                <td><?php echo $stock['id']; ?></td> 
            </tr> 
            <?php endforeach; ?> 
        </table> 
            </br>
         <table> 
            <tr>
                <th>Name</th>
                <th>Email Address</th> 
                <th>Current Balance</th>
                <th>ID</th> 
            </tr> 
        <?php foreach($users as $stock): ?>
            <tr> 
                <td><?php echo $stock['name']; ?></td>
                <td><?php echo $stock['email_address']; ?></td> 
                <td><?php echo $stock['cash_balance']; ?></td> 
                <td><?php echo $stock['id']; ?></td>
            </tr> 
        <?php endforeach; ?> 
        </table> 
        </br>
        <table> 
            <tr>
                <th>User ID</th>
                <th>Stock ID</th> 
                <th>Quantity</th>
                <th>Price</th> 
                <th>Time Stamp</th>
                <th>ID</th> 
            </tr> 
        <?php foreach($transactions as $stock): ?>
            <tr> 
                <td><?php echo $stock['user_id']; ?></td>
                <td><?php echo $stock['stock_id']; ?></td> 
                <td><?php echo $stock['quantity']; ?></td> 
                <td><?php echo $stock['price']; ?></td>
                <td><?php echo $stock['timestamp']; ?></td> 
                <td><?php echo $stock['id']; ?></td> 
            </tr> 
        <?php endforeach; ?> 
        </table> 
        <h2> Add Stock </h2>  
            <form action="index.php" method="post">
                <label>Symbol:</label> 
                <input type="text" name="symbol"/><br>
                <label>Name:</label> 
                <input type="text" name="name"/><br>
                <label>Current Price:</label> 
                <input type="text" name="current_price"/><br>
                <input type='hidden' name='action' value ='insert'/><br>
                <label>&nbsp;</label> 
                <input type="submit" value = "Add Stock"/>
            </form>
        <h2> Update Stock </h2>
            <form action="index.php" method="post">
                <label>Symbol:</label> 
                <input type="text" name="symbol"/><br>
                <label>Name:</label> 
                <input type="text" name="name"/><br>
                <label>Current Price:</label> 
                <input type="text" name="current_price"/><br>
                <input type='hidden' name='action' value ='update'/><br>
                <label>&nbsp;</label> 
                <input type="submit" value = "Update Stock"/>
            </form>
         <h2> Delete Stock </h2>
            <form action="index.php" method="post">
                <label>Symbol:</label> 
                <input type="text" name="symbol"/><br>
                <input type='hidden' name='action' value ='delete'/><br>
                <label>&nbsp;</label> 
                <input type="submit" value = "Delete Stock"/>
            </form>
         <h2> Add User </h2>  
            <form action="index.php" method="post">
                <label>Name:</label> 
                <input type="text" name="name"/><br>
                <label>Email Address:</label>
                <input type="text" name="email_address"/><br>
                <label>Cash Balance:</label>
                <input type="text" name="cash_balance"/><br>
                <input type='hidden' name='action' value ='insert'/><br>
                <label>&nbsp;</label> 
                <input type="submit" value = "Add User"/>
            </form>
         <h2> Update User </h2>
            <form action="index.php" method="post">
                <label>Name:</label> 
                <input type="text" name="name"/><br>
                <label>Email Address:</label> 
                <input type="text" name="email_address"/><br>
                <label>Cash Balance:</label> 
                <input type="text" name="cash_balance"/><br>
                <input type='hidden' name='action' value ='update'/><br>
                <label>&nbsp;</label> 
                <input type="submit" value = "Update User"/>
            </form>
           </form>
         <h2> Delete User </h2>
            <form action="index.php" method="post">
                <label>Name:</label> 
                <input type="text" name="name"/><br>
                <input type='hidden' name='action' value ='delete'/><br>
                <label>&nbsp;</label> 
                <input type="submit" value = "Delete User"/>
            </form>
         <h2> Add Transaction </h2>  
            <form action="index.php" method="post">
                <label>User ID:</label> 
                <input type="text" name="user_id"/><br>
                <label>Symbol:</label> 
                <input type="text" name="symbol"/><br>
                <label>Quantity:</label> 
                <input type="text" name="quantity"/><br>
                <input type='hidden' name='action' value ='insert'/><br>
                <label>&nbsp;</label> 
                <input type="submit" value = "Add Transaction"/>
            </form>
         <h2> Update Transaction </h2>
            <form action="index.php" method="post">
                <label>User ID:</label> 
                <input type="text" name="user_id"/><br>
                <label>Symbol:</label> 
                <input type="text" name="symbol"/><br>
                <label>Quantity:</label> 
                <input type="text" name="quantity"/><br>
                <input type='hidden' name='action' value ='update'/><br>
                <label>&nbsp;</label> 
                <input type="submit" value = "Update Transaction"/>
            </form>
         <h2> Delete Transaction </h2>
            <form action="index.php" method="post">
                <label>Transaction ID:</label> 
                <input type="text" name="id"/><br>
                <label>Current Price:</label> 
                <input type="text" name="current_price"/><br>
                <input type='hidden' name='action' value ='delete'/><br>
                <label>&nbsp;</label> 
                <input type="submit" value = "Delete Transaction"/>
            </form>
    </body> 
</html>
