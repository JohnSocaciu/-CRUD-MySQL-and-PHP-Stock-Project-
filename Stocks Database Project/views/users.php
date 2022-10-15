<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
        <title>Users List</title>
    </head>
        <?php include('topNavigation.php'); ?> 
    <body>
  <table> 
            <tr>
                <th>Name</th>
                <th>Email Address</th> 
                <th>Current Balance</th>
                <th>ID</th> 
            </tr> 
        <?php foreach($users as $stock): ?>
            <tr> 
                <td><?php echo $stock->get_name(); ?></td>
                <td><?php echo $stock->get_email_address(); ?></td> 
                <td><?php echo $stock->get_cash_balance(); ?></td> 
                <td><?php echo $stock->get_id(); ?></td>
            </tr> 
        <?php endforeach; ?> 
        </table> 
         <h2> Add User </h2>  
            <form action="users.php" method="post">
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
            <form action="users.php" method="post">
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
            <form action="users.php" method="post">
                <label>Name:</label> 
                <input type="text" name="name"/><br>
                <input type='hidden' name='action' value ='delete'/><br>
                <label>&nbsp;</label> 
                <input type="submit" value = "Delete User"/>
            </form>
         <br>
    </body>
        <?php include('footer.php'); ?> 
</html>
