<!-- Start Header -->
<?php
include('./dbConnection.php');
include('./mainInclude/header.php');
?>
<!-- End Header -->


    <!-- Start video Backround -->
    <div class="container-fluid remove-vid-marg">
        <div class="vid-parent">
            <video playsinline autoplay muted loop>
                <source src="video/banvid1.mp4">
            </video>
            <div class="vid-overlay">
            </div>
        </div>
        <div class="vid-content" style="left: 500px;right: 500px; top:500px">
            <h1 class="my-content">Welcome to <span style="font-family: 'Nabla', system-ui">CODEMY</span></h1>
            <small class="my-content">DBMS MINI PROJECT</small><br/>
              <a class="btn btn-danger mt-3" href="NewUser.php">Sign Up</a>
              <a class="btn btn-danger mt-3" href="Login.php">Login</a>
    </div> 
    <!-- End Video Background    -->

        <!-- start of text banner -->
    <div class="container-fluid bg-danger txt-banner">
        <div class="row bottom-banner" style=" margin-left: 10px;">
            <div class="col-sm" style=" margin-left: 20px;">
                <h5><span class="material-symbols-outlined" style=" padding-left: 6px; margin-top: 8px;margin-bottom: 8px;"> language</span> 100+ Online Courses</h5>
            </div>
            <div class="col-sm"style=" margin-left: 20px;">
                <h5><span class="material-symbols-outlined" style=" padding-left: 6px; margin-top: 8px;margin-bottom: 8px;"> school </span>   Expert Instructors</h5>
            </div>
            <div class="col-sm"style=" margin-left: 20px;">
                <h5><span class="material-symbols-outlined" style=" padding-left: 6px; margin-top: 8px;margin-bottom: 8px;"> login </span> Lifetime Access</h5>
            </div>
        </div>
    </div>
        <!-- end of text banner -->



<!-- Start Footer -->
<?php
include('./mainInclude/footer.php');
?>
<!-- End Footer -->