<?php
    error_reporting(E_ALL ^ E_NOTICE); //specify All errors and warnings are displayed
    session_start();
    extract($_POST);
    if(!isset($_SESSION["login"])){
        header("Location: Login.php");
        exit();
    }

    //connect to MySQL DB
    include("dbConnection.php");
    $conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);

    // Check conn
    if($conn === false){
        die("ERROR: Could not connect. " . mysqli_connect_error());
    }
   


    //Before you use any session, you always have to check if that session has value or not!
    $id = $_SESSION["id"] ?? "";

    $sqlCheckStudent = "SELECT * FROM Student WHERE StudentId = '$id'";
    $result = mysqli_query($conn, $sqlCheckStudent);

    if (mysqli_num_rows($result) === 0) {
        // Handle error here, e.g., display an error message
        echo "Invalid Student ID. Please verify and try again.";
        exit();
    }

    //get student Name from DB
    if($id){
        $sqlName = "SELECT Name FROM Student WHERE StudentId = '$id'";
        $nameSet = mysqli_query($conn, $sqlName);
        $row = mysqli_fetch_assoc($nameSet);
        if($row){
            $name = $row['Name'];
        }
    }

    //get a list of semester
    $sqlSemester = "SELECT * FROM Semester";
    $semesterSet = mysqli_query($conn, $sqlSemester);
    while($row = mysqli_fetch_assoc($semesterSet)){
        $semesterList[] = $row['SemesterCode']; //to get semesterList[0] so that display 22F's course list when the page is first loaded
    }


    if(isset($semesterBtn)){
        $_SESSION["semester"] = $semester;
    } else{
        $semester = $_SESSION["semester"] ?? $semesterList[0]; //display 22F's course list when the page is first loaded
    }


    //get the number of weekly hours the user has registered for the semester
    $sqlHours = "SELECT SUM(c.WeeklyHours) AS hours
                 FROM Registration r INNER JOIN Course c on r.CourseCode = c.CourseCode
                 WHERE r.StudentId = '$id' AND r.SemesterCode = '$semester';";
    $hoursSet = mysqli_query($conn, $sqlHours);
    $row = mysqli_fetch_assoc($hoursSet);
    if($row){
        $hours = $row['hours'] ?? 0;
        $remainingHours = 16 - $hours;
    }


    $hoursChecked = 0;
    if(isset($submit)){
        if(!isset($checkbox)){
            $errorMsg = "You need select at least one course!";
        }else{
            foreach($checkbox as $name => $value){
                $hoursChecked += $value;
            }
            if($hoursChecked > $remainingHours){
                $errorMsg = "Your selection exceed the max weekly hours";
            }
            else{
                $errorMsg = "";
                $sqlRegister = "INSERT INTO `Registration` (`StudentId`, `CourseCode`, `SemesterCode`) VALUES (?,?,?);";
                $preparedStmt = mysqli_prepare($conn, $sqlRegister);
                foreach($checkbox as $name => $value){
                    mysqli_stmt_bind_param($preparedStmt, "iss", $id, $name, $semester); // Ensure $id is the correct student ID
                    mysqli_stmt_execute($preparedStmt);
                }
                header("Location: CourseSelection.php");
                
            }
        }
    }


    if(isset($clear)){
        $errorMsg = "";
        $checkbox = "";
    }


    include("mainInclude\header.php");
    print <<<HTML
    <div class="container">
        <h1>Course Selection</h1>
        <p>Welcome <span style='font-weight: bold;'>$name</span> (not you? change user <a href="Login.php">here</a>)</p>
        <p>You have registered <span style='font-weight: bold;'>$hours</span> hours for the selected semester.</p>
        <p>You can register <span style='font-weight: bold;'>$remainingHours</span> more hours of course(s) for the semester.</p>
        <p>Please note that the courses you have registered will not be displayed in the list</p>  
        <form action="CourseSelection.php" method="post"> 
        <div class="row col-md-4">
            <select name="semester" id="semester" class="form-control">
    HTML;

    //get a dropdown list of semesters from DB
    $sqlSemester = "SELECT * FROM Semester";
    $semesterSet = mysqli_query($conn, $sqlSemester);
    while($row = mysqli_fetch_assoc($semesterSet)){
        $selected = $semester == $row['SemesterCode'] ? "selected" : "";
        echo "<option value='{$row['SemesterCode']}' $selected>{$row['Year']} {$row['Term']}</option>";
    }


    print <<<HTML
            </select>
            <!---------------hidden button triggers click------------->
            <input type="submit" id="semesterBtn" name="semesterBtn" value="semesterBtn" hidden>  
            <span class="errorMsg">$errorMsg</span>       
            <script>
                document.getElementById("semester").addEventListener("change", function(){
                    document.getElementById("semesterBtn").click();
                })
            </script>            
        </div>
        <br>
        <table class='table' style='margin-top: 30px;'>
            <tr>
                <th>Code</th>
                <th>Course Title</th>
                <th>Hours</th>
                <th>Select</th>
            </tr>
    HTML;


    //get a course list excluding user's registered courses
    $sqlCourse = "SELECT co.CourseCode, c.Title, c.WeeklyHours
                  FROM CourseOffer co INNER JOIN Course c ON co.CourseCode = c.CourseCode
                  LEFT OUTER JOIN (SELECT * FROM Registration WHERE StudentId = '$id')  r on co.CourseCode = r.CourseCode
                  WHERE co.SemesterCode = '$semester'  AND r.StudentId IS null;";
    $courseSet = mysqli_query($conn, $sqlCourse);
    while($row = mysqli_fetch_assoc($courseSet)){
        print <<<table_body
            <tr>
                <td>{$row['CourseCode']}</td>
                <td>{$row['Title']}</td>
                <td>{$row['WeeklyHours']}</td>
                <td><input type="checkbox" name="checkbox[{$row['CourseCode']}]" value="{$row['WeeklyHours']}"></td>
                <!-- <input type="hidden" name="weeklyHours[{$row['CourseCode']}]" value="{$row['WeeklyHours']}"> -->
            </tr>
        table_body;
    }

    print <<<HTML
            </table>
            <input type="submit" name="submit" value="Submit" class="btn btn-primary">
            <input type="submit" name="clear" value="Clear" class="btn btn-primary">
        </form>
    </div>
    HTML;
    include("mainInclude/footer.php");

    // Close conn
    mysqli_close($conn);
?>
