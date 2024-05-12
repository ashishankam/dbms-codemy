<?php
    error_reporting(E_ALL ^ E_NOTICE); // Specify all errors and warnings are displayed
    session_start();
    extract($_POST);
    $idErr = "";
    $passwordErr = "";
    $logInErr = "";

    // Connect to MySQL DB
    include("dbConnection.php");
    $conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);

    // Check conn
    if($conn === false){
        die("ERROR: Could not connect. " . mysqli_connect_error());
    }

    if(isset($submit)){
        $idErr = ValidateId($id);
        $passwordErr = ValidatePassword($Password);

        if(!$idErr && !$passwordErr){
            // Sanitize user input to prevent SQL injection
            $id = mysqli_real_escape_string($conn, $id);
            $Password = mysqli_real_escape_string($conn, $Password);

            //Method: prevent SQL-injection attack - Prepared statement
            $sqlLogin = "SELECT StudentId FROM Student WHERE StudentId = ? AND Password = ?";
            $preparedStatement = mysqli_prepare($conn, $sqlLogin);
            mysqli_stmt_bind_param($preparedStatement, "ss", $id, $Password);
            mysqli_stmt_execute($preparedStatement);
            mysqli_stmt_store_result($preparedStatement);

            if(mysqli_stmt_num_rows($preparedStatement) == 0){
                $logInErr = "Incorrect student ID and/or Password!";
                // Log the error for debugging
                error_log("Login failed for Student ID: $id", 3, "error.log");
            }
            else{
                // Store data in session
                $_SESSION["id"] = $id;
                $_SESSION["Password"] = $Password;
                $_SESSION["login"] = "true";
                header("Location: CourseSelection.php");
                exit(); // Ensure script stops execution after redirect
            }
        }
    }
    else{
        // If the data has been stored in the session, display the data on the page when the user enters this page
        $id = $_SESSION["id"] ?? "";
        $Password = $_SESSION["Password"] ?? "";
    }

    if(isset($clear)) {
        $id = '';
        $Password = '';
    }

    include("mainInclude\header.php");
    print <<<HTML
    <div class="container">
        <h1>Log In</h1>
        <p>You need to <a href="NewUser.php">sign up</a> if you are a new user</p>
        <form action="Login.php" method="post">
            <span class="errorMsg">$logInErr</span>
            <div class="row form-group form-inline">
                <label for="id" class="col-md-2">Student ID: </label>
                <input type="text" id="id" name="id" class="form-control col-md-3" value="$id">
                <span class="errorMsg">$idErr</span>
            </div>
            <div class="row form-group form-inline">
                <label for="Password" class="col-md-2">Password: </label>
                <input type="password" id="Password" name="Password" class="form-control col-md-3" value="$Password">
                <span class="errorMsg">$passwordErr</span>
            </div>
            <button type="submit" name="submit" class="btn btn-primary">Submit</button>
            <button type="submit" name="clear" class="btn btn-primary">Clear</button>
        </form>
        </div>
    </div>
    HTML;

    include("mainInclude/footer.php");


    function ValidateId($id): string
    {
        if(!trim($id))
        {
            return "Student ID can not be blank";
        }
        else
        {
            return "";
        }
    }

    function ValidatePassword($Password): string
    {
        if(!trim($Password))
        {
            return "Password can not be blank";
        }
        else
        {
            return "";
        }
    }
?>
