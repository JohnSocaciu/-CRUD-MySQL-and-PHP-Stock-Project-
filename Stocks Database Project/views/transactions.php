<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
        <title>Transactions List</title>
    </head>
     <?php include('topNavigation.php'); ?> 
    <body>
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
                <td><?php echo $stock->get_user_id(); ?></td>
                <td><?php echo $stock->get_stock_id(); ?></td> 
                <td><?php echo $stock->get_quantity(); ?></td>    
                <td><?php echo $stock->get_price(); ?></td>
                <td><?php echo $stock->get_time_stamp(); ?></td> 
                <td><?php echo $stock->get_id(); ?></td> 
            </tr> 
        <?php endforeach; ?> 
        </table> 
          <h2> Add Transaction </h2>  
            <form action="transactions.php" method="post">
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
            <form action="transactions.php" method="post">
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
            <form action="transactions.php" method="post">
                <label>User Id:</label> 
                <input type="text" name="user_id"/><br>
                <label>Transaction Id:</label> 
                <input type="text" name="id"/><br>
                <label>Current Price:</label> 
                <input type="text" name="current_price"/><br>
                <input type='hidden' name='action' value ='delete'/><br>
                <label>&nbsp;</label> 
                <input type="submit" value = "Delete Transaction"/>
            </form>
         <br>
    </body>
    <?php include('footer.php'); ?> 
</html>
