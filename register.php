<?php

  $myfile = fopen("../pg_connection_info.txt", "r") or die("Unable to open \"../pg_connection_info.txt\" file!");
    $my_host = fgets($myfile);
    $my_dbname = fgets($myfile);
    $my_user = fgets($myfile);
    $my_password = fgets($myfile);
    fclose($myfile);

    $dbhost = pg_connect("host=$my_host dbname=$my_dbname user=$my_user password=$my_password");
    if(!$dbhost) {
        die("Error: " .pg_last_error());
      }

$name = $password = $confirm_password = $email = "";
$name_err = $password_err = $confirm_password_err = $email_err = "";
 
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
   
    if(empty(trim($_POST["name"]))){
        $name_err = "Please enter a username.";
    } else{
        
        $sql = "SELECT User_id FROM users WHERE name = ?";
        
        if($stmt = mysqli_prepare($dbhost, $sql)){
            
            mysqli_stmt_bind_param($stmt, "s", $param_name);
            
            
            $param_name = trim($_POST["name"]);
            
            
            if(mysqli_stmt_execute($stmt)){
                
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $name_err = "This username is already taken.";
                } else{
                    $name = trim($_POST["name"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            
            mysqli_stmt_close($stmt);
        }
    }
	
	
	if(empty(trim($_POST["email"]))){
        $email_err = "Please enter a email.";
    } else{
        
        $sql = "SELECT User_id FROM users WHERE email = ?";
        
        if($stmt = mysqli_prepare($dbhost, $sql)){
            
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            
            $param_email = trim($_POST["email"]);
            
            
            if(mysqli_stmt_execute($stmt)){
                
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $email_err = "This email is already in use.";
                } else{
                    $email = trim($_POST["email"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            
            mysqli_stmt_close($stmt);
        }
    }
    
    
    
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Password must have atleast 6 characters.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm password.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }
    
    
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($email_err)){
        
        
        $sql = "INSERT INTO users (user_id, name, password, email) VALUES (1, '?', '?', '?')";
         
        if($stmt = mysqli_prepare($dbhost, $sql)){
            
            mysqli_stmt_bind_param($stmt, "ss", $param_name, $param_password);
            
            
            $param_name = $name;
            $param_password = password_hash($password, PASSWORD_DEFAULT); 
            
            
            if(mysqli_stmt_execute($stmt)){
                
                header("location: login.php");
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            
            mysqli_stmt_close($stmt);
        }
    }
    
    
    mysqli_close($dbhost);
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
</head>
<body>
    <div class="wrapper">
        <h2>Sign Up</h2>
        <p>Please fill this form to create an account.</p>
        <form action= "login.php" method="GET"?>
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="name" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" value="">
                <span class="invalid-feedback"><?php echo $name_err; ?></span>
            </div> 
			<div class="form-group">
                <label>Email</label>
                <input type="text" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="">
                <span class="invalid-feedback"><?php echo $email_err; ?></span>
            </div> 
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="">
                <span class="invalid-feedback"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirm_password; ?>">
                <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <?php 
                $name1 = $_GET["name"];
                $password1 = $_GET["password"];
                $email1 = $_GET["email"];
                $sql = "INSERT INTO users (user_id, name, password, email) VALUES (1,$name1,$password1,$email1)";
                if(pg_query($dbhost,$sql)){
                    echo "it worked";
                }
                ?>
                <input type="reset" class="btn btn-secondary ml-2" value="Reset">
            </div>
            <p>Already have an account? <a href="login.php">Login here</a>.</p>
        </form>
    </div>    
</body>
</html>
