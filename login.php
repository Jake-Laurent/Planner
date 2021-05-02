<?php
 session_start();

if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){ header("header: project.php"); exit;
}

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

$name = $password = ""; $name_err = $password_err = $login_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){

    if(empty(trim($_POST["name"]))){ $name_err = "Please enter username.";
    } else{
        $name = trim($_POST["name"]);
    }
    if(empty(trim($_POST["password"]))){ $password_err = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }
    if(empty($name_err) && empty($password_err)){ $sql = "SELECT User_id, name, password FROM users WHERE name = ?";
        if($stmt = mysqli_prepare($dbhost, $sql)){

            mysqli_stmt_bind_param($stmt, "s", $param_name); $param_name = $name; if(mysqli_stmt_execute($stmt)){ mysqli_stmt_store_result($stmt); 
                if(mysqli_stmt_num_rows($stmt) == 1){
                    mysqli_stmt_bind_result($stmt, $User_id, $name, $hashed_password); if(mysqli_stmt_fetch($stmt)){ if(password_verify($password, 
                        $hashed_password)){
                            session_start();

                            $_SESSION["loggedin"] = true; $_SESSION["User_id"] = $User_id; $_SESSION["name"] = $name; header("location: project.php");
                        } else{
                            $login_err = "Invalid username or password.";
                        }
                    }
                } else{
                    $login_err = "Invalid username or password.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }
    mysqli_close($dbhost);
}
?>

<!DOCTYPE html> <html lang="en"> <head> <meta charset="UTF-8"> <title>Login</title> </head> <body> <h2>Login</h2> <form action="project.php"> <div class="form-group"> 
                <label>Username</label> <input type="text" name="name" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" 
                value=""> <span class="invalid-feedback"><?php echo $name_err; ?></span>
            </div> <div class="form-group"> <label>Password</label> <input type="password" name="password" class="form-control <?php echo 
                (!empty($password_err)) ? 'is-invalid' : ''; ?>"> <span class="invalid-feedback"><?php echo $password_err; ?></span>
            </div> <div class="form-group"> <input type="submit" class="btn btn-primary" value="Login"> </div> <p>Don't have an account? <a 
            href="register.php">Sign up now</a>.</p>
        </form> </div> </body>
</html>
