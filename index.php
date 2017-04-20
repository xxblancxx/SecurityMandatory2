<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?php
        $servername = "localhost";
        $username = "securityclass";
        $password = "1234";
        $dbname = "securitydb";

        $nameErr1 = $passErr1 = $nameErr2 = $passErr2 = "";
        $name = $pass = $name2 = $pass2 = "";

        //If fields empty = error message, else set values for insert SQL
        if (isset($_POST['submit'])) {
            if (empty($_POST["name"])) {
                $nameErr1 = "Name is required";
            } else {
                $name = $_POST["name"];
            }
            if (empty($_POST["pass"])) {
                $passErr1 = "Password is required";
            } else {
                $pass = $_POST["pass"];
            }
        } else if (isset($_POST['submit2'])) {
            if (empty($_POST["name2"])) {
                $nameErr2 = "Name is required";
            } else {
                $name2 = $_POST["name2"];
            }
            if (empty($_POST["pass2"])) {
                $passErr2 = "Password is required";
            } else {
                $pass2 = $_POST["pass2"];
            }
        }
        ?>

        <h2>PHP injection example</h2>
        <span class="error">* required field.</span><br><br>
        <span class="error"><b>Unsafe version</b></span><br>
        <form method="post"  action="">  
            Name: <input type="text" name="name" value="<?php echo $name; ?>">
            <span class="error">* <?php echo $nameErr1; ?></span>
            <br>
            Password: <input type="text" name="pass" value="<?php echo $pass; ?>">
            <span class="error">* <?php echo $passErr1; ?></span>
            <br>
            <input type="submit" name="submit" value="Submit">  <br><br>
        </form>

        <span class="error"><b>Safe version</b></span>
        <form method="post"  action="">  
            Name: <input type="text" name="name2" value="<?php echo $name2; ?>">
            <span class="error">* <?php echo $nameErr2; ?></span>
            <br>
            Password: <input type="text" name="pass2" value="<?php echo $pass2; ?>">
            <span class="error">* <?php echo $passErr2; ?></span>
            <br>
            <input type="submit" name="submit2" value="Submit">  
        </form>

        <?php
        //Insert unsafe
        if (empty($_POST["name"]) && empty($_POST["pass"])) {
            
        } else {
//          Create connection
//          
            $conn = new mysqli($servername, $username, $password, $dbname);
            $sql = "SELECT * FROM User WHERE User.Name = '$name' AND User.Password = '$pass' LIMIT 1;";

            $result = mysqli_query($conn, $sql);

            if (mysqli_affected_rows($conn) == 1) {
                echo "<br>Logged in successfully";
                echo "<br>";
                echo "<h3>The server recieved the following statement:</h3>";
                echo $sql;
            } else {

                echo "<br>Incorrect Login";
                echo "<br>";
                echo "<h3>The server recieved the following statement:</h3>";
                echo $sql;
                echo mysqli_error($result);
            }
            $conn->close();
        }

        //Insert safe
// Create connection
        if (empty($_POST["name2"]) && empty($_POST["pass2"])) {
            
        } else {
            $conn = new mysqli($servername, $username, $password, $dbname);
            $sql = "SELECT SecureUser.Name, SecureUser.Digest FROM SecureUser WHERE SecureUser.Name = ?;";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param('s', $name2);
            $stmt->execute();
            $stmt->store_result();

            $stmt->bind_result($Name, $Digest);

            while ($stmt->fetch()) {
                if (password_verify($pass2, $Digest)) {
                    echo "<br>Logged in successfully";
                    echo "<br>";
                    echo "<h3>The server recieved the following statement:</h3>";
                    echo $sql;
                } else {
                    echo "<br>Incorrect Login";
                    echo "<br>";
                    echo password_hash($pass2, PASSWORD_DEFAULT);
                    echo "<h3>The server recieved the following statement:</h3>";
                    echo $sql;
                    echo mysqli_error($result);
                    exit();
                }
            }

            $conn->close();
        }
        ?>
    </body>
</html>