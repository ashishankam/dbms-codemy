<?php
   error_reporting(E_ALL); // Enable full error reporting
   session_start();
   extract($_POST);
    $idErr = "";
    $nameErr = "";
    $phoneErr = "";
    $passwordErr = "";
    $passwordAgainErr = "";

    //connect to MySQL DB
    include("dbConnection.php");


    if(isset($submit)){ 
        $idErr = ValidateId($id);
    $nameErr = ValidateName($name);
    // $phoneErr = ValidatePhone($phone);
    $passwordErr = ValidatePassword($Password);
    $passwordAgainErr = ValidatePasswordAgain($passwordAgain, $Password);

    // If there are no validation errors, proceed with data insertion
   //if the page is requested due to the form submission, NOT the first time request


        if(!$idErr && !$nameErr && !$phoneErr && !$passwordErr && !$passwordAgainErr)
        {
            //store data in session
            $id=$_POST["id"];
            $name =$_POST["name"] ;
            $phone=$_POST["phone"];
            $Password=$_POST["Password"];
            $passwordAgain=$_POST["passwordAgain"];
            

            
            //hash password
            $hashedPassword = hash("sha256", $Password);

            $sql="INSERT INTO `cdms`.`student` (`StudentId`, `Name`, `Phone`, `Password`) VALUES ('$id', '$name', '$phone', '$Password');";
            
            if($conn->query($sql) == true){
                echo "Successfully inserted";}
        }
    }
    else{
        //if the data has been stored in the session, display the data on the page when the user enters this page
        $id = $_POST["id"] ?? "";
        $name = $_POST["name"] ?? "";
        $phone = $_POST["phone"] ?? "";
        $Password = $_POST["Password"] ?? "";
        $passwordAgain = $_POST["passwordAgain"] ?? "";
    }


    if(isset($clear)) {
        $id = '';
        $name = '';
        $phone = '';
        $Password = '';
        $passwordAgain = '';
    }


    include("mainInclude\header.php");
    print <<<HTML
        <div class="container">
            <h1>Sign Up</h1>
            <p>All fields are required</p>
            <form action="NewUser.php" method="post">
                <div class="row form-group form-inline">
                    <label for="id" class="col-md-2">Student ID: </label>
                    <input type="text" id="id" name="id" class="form-control col-md-3" value="$id">
                    <span class="errorMsg">$idErr</span>
                </div>
                <div class="row form-group form-inline">
                    <label for="name" class="col-md-2">Name: </label>
                    <input type="text" id="name" name="name" class="form-control col-md-3" value="$name">
                    <span class="errorMsg">$nameErr</span>
                </div>
                <div class="row form-group form-inline">
                    <label for="phone" class="col-md-2">Phone Number: <br>(nnn-nnn-nnnn)</label>
                    <input type="text" id="phone" name="phone" class="form-control col-md-3" value="$phone">
                    <span class="errorMsg">$phoneErr</span>
                </div>
                <div class="row form-group form-inline">
                    <label for="Password" class="col-md-2">Password: </label>
                    <input type="password" id="Password" name="Password" class="form-control col-md-3" value="$Password">
                    <span class="errorMsg">$passwordErr</span>
                </div>
                <div class="row form-group form-inline">
                    <label for="passwordAgain" class="col-md-2">Password Again: </label>
                    <input type="password" id="passwordAgain" name="passwordAgain" class="form-control col-md-3" value="$passwordAgain">
                    <span class="errorMsg">$passwordAgainErr</span>
                </div>
                <br/>
                <button type="submit" name="submit" class="btn btn-primary bg-danger">Submit</button>
                <button type="submit" name="clear" class="btn btn-primary bg-danger">Clear</button>
                <br/>
            </form>
        </div> <br/>
    HTML;
    include("./mainInclude/footer.php");

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

    function ValidateName($name): string
    {
        if(!trim($name))
        {
            return "Name can not be blank";
        }
        else
        {
            return "";
        }
    }

    function ValidatePhone($phone): string
    {
        $regex = "/^([2-9]\d{2})-([2-9]{3})-(\d{4})$/";
        if(!trim($phone))
        {
            return "Phone number can not be blank";
        }
        elseif(!preg_match($regex, $phone))
        {
            return "Incorrect phone number";
        }
        else
        {
            return "";
        }
    }

    function ValidatePassword($Password): string
    {
        $regex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{6,}$/";
        if(!trim($Password))
        {
            return "Password can not be blank";
        }
        elseif(!preg_match($regex, $Password))
        {
            return "Password must be at least 6 characters long, contains at least one upper case, one lowercase and one digit";
        }
        else
        {
            return "";
        }
    }

    function ValidatePasswordAgain($passwordAgain, $Password): string
    {
        if($passwordAgain != $Password)
        {
            return "Password does not match";
        }
        else
        {
            return "";
        }
    }
?>
