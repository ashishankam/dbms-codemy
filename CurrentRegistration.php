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

    //get student Name from DB
    $sqlName = "SELECT Name FROM Student WHERE StudentId = '$id'";
    $nameSet = mysqli_query($conn, $sqlName);
    $row = mysqli_fetch_assoc($nameSet);
    if($row){
        $name = $row['Name'];
    }


    if(isset($confirmDelete)){
        $sqlDelete = "DELETE FROM Registration WHERE StudentId = ? AND SemesterCode = ? AND CourseCode = ?";
        $deleteStmt = mysqli_prepare($conn, $sqlDelete);

        foreach ($checkbox as $name => $value) {
            mysqli_stmt_bind_param($deleteStmt, 'iss', $id, $value, $name);
            mysqli_stmt_execute($deleteStmt);
        }

        header("Location: CurrentRegistration.php");
    }

    if(isset($clear)){
        $checkbox = "";
    }

    include("mainInclude\header.php");
    print <<<HTML
        <div class="container">
            <h1>Current Registration</h1>
            <p>Hello <span style='font-weight: bold;'>$name</span> (not you? change user <a href="Login.php">here</a>), the followings are your current registration</p>
            <form action="CurrentRegistration.php" method="post">
                <table class="table">
                    <tr>
                        <th>Year</th>
                        <th>Term</th>
                        <th>Course Code</th>
                        <th>Course Title</th>
                        <th></th>
                        <th>Hours</th>
                        <th>Select</th>
                        <th>Class</th>
                    </tr>
    HTML;

    $semesterArr = [];
    $sqlSemesterCode = "SELECT r.SemesterCode
                        FROM Registration r
                        WHERE r.StudentId = '$id'
                        GROUP BY r.SemesterCode;";
    $semesterCodeSet = mysqli_query($conn, $sqlSemesterCode);
    while($row = mysqli_fetch_assoc($semesterCodeSet)){
        $semesterArr[] = $row['SemesterCode'];
    }

    foreach($semesterArr as $s){
        $totalHours = 0;
        $sqlRegistrations = "SELECT s.Year, s.Term, r.CourseCode, c.Title, c.WeeklyHours, r.SemesterCode,c.VideoLink
                             FROM Registration r INNER JOIN Course c ON r.CourseCode = c.CourseCode
                                                 INNER JOIN Semester s ON r.SemesterCode = s.SemesterCode
                             WHERE r.StudentId = '$id' AND r.SemesterCode = '$s';";
        $registrationsSet = mysqli_query($conn, $sqlRegistrations);
        while($row = mysqli_fetch_assoc($registrationsSet)){
            $courseCode = $row['CourseCode'];
            $videoLink = $row['VideoLink']; 

            print <<<table_body
            <tr>
                <td>{$row['Year']}</td>
                <td>{$row['Term']}</td>
                <td>{$row['CourseCode']}</td>
                <td>{$row['Title']}</td>
                <td></td>
                <td>{$row['WeeklyHours']}</td>
                <td><input type="checkbox" name="checkbox[{$row['CourseCode']}]" value="{$row['SemesterCode']}"></td>
                <td><a href='$videoLink' target='_blank'>Start Class</a> </td>
            </tr>
            table_body;
            $totalHours += $row['WeeklyHours'];
        }
        echo "<tr><td></td><td></td><td></td><td></td><td style='font-weight: bold;'>Total Weekly Hours</td><td style='font-weight: bold;'>$totalHours</td><td></td></tr>";
    }

    print <<<HTML
                </table>
                <input type="button" name="delete" id="delete" value="Delete Selected" class="btn btn-primary">
                <input type="submit" name="clear" value="Clear" class="btn btn-primary">
                <input type="submit" name="confirmDelete" id="confirmDelete" hidden>
            </form>
        </div>

        <script>
            document.getElementById("delete").addEventListener("click", function(){
                let text = "The selected registrations will be deleted!"
                if(confirm(text) === true){
                    document.getElementById("confirmDelete").click()
                }
            })
        </script>
    HTML;

    include("mainInclude/footer.php");

    // Close conn
    mysqli_close($conn);
?>
