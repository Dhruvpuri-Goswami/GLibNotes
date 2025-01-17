<?php
    session_start();
    if(isset($_SESSION['username']))
    {
        $email=$_SESSION['username'];
        include 'connection.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Glib Notes</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link href='https://fonts.googleapis.com/css?family=RobotoDraft' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
    html,
    body,
    h1,
    h2,
    h3,
    h4,
    h5 {
        font-family: "RobotoDraft", "Roboto", sans-serif
    }

    .w3-bar-block .w3-bar-item {
        padding: 16px
    }

    .responsive-iframe {
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
        width: 100%;
        height: 100%;
        border: none;
    }

    .container {
        position: relative;
        width: 100%;
        overflow: hidden;
        padding-left: 2rem;
        padding-right: 2rem;
        padding-top: 56.25%;
        /* 16:9 Aspect Ratio */
    }
    </style>
</head>

<body>

    <!-- Side Navigation -->
    <nav class="w3-sidebar w3-bar-block w3-collapse w3-white w3-animate-left w3-card" style="z-index:3;width:320px;"
        id="mySidebar">
        <a href="javascript:void(0)" class="w3-bar-item w3-border-bottom w3-large w3-center"><img src="logo.png"
                style="width:80%;"></a>
        <a href="javascript:void(0)" onclick="w3_close()" title="Close Sidemenu"
            class="w3-bar-item w3-button w3-hide-large w3-large">Close <i class="fa fa-remove"></i></a>
        <a href="javascript:void(0)" class="w3-bar-item w3-button w3-teal w3-button w3-hover-black w3-left-align"
            onclick="document.getElementById('id01').style.display='block'"><i
                class="w3-padding fa fa-pencil"></i>Upload Notes </a>
        <form method="POST" action="" class="w3-containe w3-center w3-bar-item">
            <input type="text" class="w3-margin-top w3-padding-16" style="height: 35px;padding-left: 10px;" width="30%"
                placeholder="Find Your Notes..." name="search">
            <input type="submit" class="w3-button w3-teal w3-round" style="height: 37px;margin-bottom: 5px;width:50px;"
                value="GO" name="go">
        </form>
        <?php
            if(isset($_REQUEST['go']))
            {
                $search = strtoupper($_REQUEST['search']);
                $sql2 = "SELECT * FROM tbl_uploads WHERE keyword='$search'";
                $result2 = mysqli_query($conn, $sql2);
                $keywords = mysqli_fetch_all($result2, MYSQLI_ASSOC);
                foreach($keywords as $keyword)
                {
                    $file_name = $keyword['file_name'];
                    $upload_id = $keyword['u_id'];
                    $user_id = $keyword['user_id'];
                    $sql3 = "SELECT * FROM tbl_user WHERE user_id='$user_id'";
                    $result3 = mysqli_query($conn, $sql3);
                    $userinfo = mysqli_fetch_assoc($result3);
                    $user_name = $userinfo['user_name'];
            ?>
        <form action="userhome.php" method="post">
            <input type="hidden" name="id" value="<?php echo $upload_id; ?>">
            <button type="submit" name="btnpdfopen">
                <ul class="w3-ul">
                    <li><?php echo $file_name. " - ". $user_name; ?></li>
                </ul>
            </button>
        </form>
        <?php
                }
            }
        ?>
        <a href="logout.php" class="w3-bar-item w3-button w3-display-bottomleft w3-teal"><i
                class="fa fa-remove w3-margin-right"></i>Log Out</a>
    </nav>

    <!-- Modal that pops up when you click on "New Message" -->
    <div id="id01" class="w3-modal w3-display-middle">
        <div class="w3-modal-content w3-animate-zoom">
            <div class="w3-container w3-padding w3-teal">
                <span onclick="document.getElementById('id01').style.display='none'"
                    class="w3-button w3-right w3-large w3-round" style="margin-top: 0.8rem;"><i
                        class="fa fa-remove"></i></span>
                <h2>Upload Notes</h2>
            </div>
            <div class="w3-panel">
                <form action="" method="POST" enctype="multipart/form-data">
                    <label>Notes Name</label>
                    <input class="w3-input w3-border w3-margin-bottom" type="text" name="name" required>
                    <label>Keyword</label>
                    <input class="w3-input w3-border w3-margin-bottom" type="text" name="keyword" required>
                    <label>Upload Notes</label>
                    <input class="w3-input w3-border w3-margin-bottom" type="file" name="fileToUpload" required>
                    <input class="w3-input w3-border w3-margin-bottom" style="height:100px" placeholder="Description"
                        name="desc" required>
                    <div class="w3-section">
                        <a class="w3-button w3-red"
                            onclick="document.getElementById('id01').style.display='none'">Cancel
                             <i class="fa fa-remove"></i></a>
                        <button class="w3-button w3-teal w3-right" type="submit" name="submit">Upload  <i
                                class="fa fa-paper-plane"></i></button>
                    </div>
                </form>
            </div>
            <?php
                if(isset($_REQUEST['submit']))
                {
                $filename = $_FILES["fileToUpload"]["name"];
                $tempname = $_FILES["fileToUpload"]["tmp_name"];
                $folder = "uploads_images/";
                $notes_name = $_REQUEST['name'];
                $notes_desc = $_REQUEST['desc'];
                $notes_keyword = strtoupper($_REQUEST['keyword']);
                date_default_timezone_set("Asia/Kolkata");
                $date = date("Y-m-d");
                $sql = "SELECT * FROM tbl_user WHERE user_name='$email'";
                $result = mysqli_query($conn,$sql);
                $rows = mysqli_fetch_assoc($result);
                $user_id = $rows['user_id'];

                $sql1 = "INSERT INTO tbl_uploads (user_id, uploaded_file, keyword, file_name, file_description,
                uploaded_date)
                VALUES ('$user_id', '$filename', '$notes_keyword', '$notes_name', '$notes_desc', '$date')";
                if (mysqli_query($conn, $sql1))
                {
                if (move_uploaded_file($tempname, $folder.$filename))
                {
                sleep(2);
                echo '<script>
                alert("Notes Uploaded Successfully Successfully...");
                </script>';
                }
                else {
                sleep(2);
                echo '<script>
                alert("Something Went Wrong...")
                </script>';
                }
                }
                else {
                sleep(2);
                echo '<script>
                alert("Something Went Wrong...")
                </script>';
                }
                }
                ?>
        </div>
    </div>

    <!-- Overlay effect when opening the side navigation on small screens -->
    <div class="w3-overlay w3-hide-large w3-animate-opacity" onclick="w3_close()" style="cursor:pointer"
        title="Close Sidemenu" id="myOverlay"></div>



    <!-- Page content -->
    <div class="w3-main" style="margin-left:320px;">
        <div class="w3-bar w3-teal">
            <a href="logout.php"
                class="w3-bar-item w3-padding-24 w3-right w3-margin-right w3-button w3-mobile">Logout</a>
            <a href="#" class="w3-padding-24 w3-right w3-bar-item w3-button w3-mobile">My Profile</a>
            <a href="your_notes.php" class="w3-bar-item w3-padding-24 w3-right w3-button w3-mobile">Your Notes</a>
            <a href="userhome.php" class="w3-bar-item w3-padding-24 w3-right w3-button w3-mobile">Home</a>
        </div>
        <i class="fa fa-bars w3-button w3-white w3-hide-large w3-xlarge w3-margin-left w3-margin-top"
            onclick="w3_open()"></i>
        <a href="javascript:void(0)" class="w3-hide-large w3-red w3-button w3-right w3-margin-top w3-margin-right"
            onclick="document.getElementById('id01').style.display='block'"><i class="fa fa-pencil"></i></a>

        <div class="w3-card w3-margin w3-round-large w3-margin w3-padding">
            <div class="w3-card w3-margin w3-round-large">
                <div class="w3-padding">
                    <h2><b>My Profile</b></h2>
                </div>
            </div>
            <!-- Page Container -->
            <div class="w3-content w3-margin-top" style="max-width:1400px;">
                <!-- The Grid -->
                <div class="w3-row-padding">
                    <?php
                        $sql4="SELECT * FROM tbl_user WHERE user_name='$email'";
                        $result4=mysqli_query($conn,$sql4);
                        $userdata=mysqli_fetch_assoc($result4);
                        $name=$userdata['user_name'];
                        $id=$userdata['user_id'];
                        $img=$userdata['img'];
                        $mail=$userdata['email'];
                        $mobile_no=$userdata['mobile_no'];
                        $city=$userdata['city'];
                        $state=$userdata['state'];
                    ?>
                    <!-- Left Column -->
                    <div class="w3-third">

                        <div class="w3-white w3-text-grey w3-card-4">
                            <div class="w3-display-container">
                                <img src="<?php echo "uploads_images/".$img; ?>" style="width:100%" alt="Avatar">
                                <div class="w3-container w3-text-black">
                                    <h2><?php echo $name; ?></h2>
                                </div>
                            </div>
                            <div class="w3-container">
                                <p><i class="fa fa-briefcase fa-fw w3-margin-right w3-large w3-text-teal"></i>
                                    <?php
                                if(isset($city))
                                {
                                    echo $city;
                                }
                                else
                                {
                                    echo "Not Yet Set";
                                }
                                ?>
                                </p>
                                <p><i class="fa fa-home fa-fw w3-margin-right w3-large w3-text-teal"></i>
                                    <?php
                                if(isset($state))
                                {
                                    echo $state;
                                }
                                else
                                {
                                    echo "Not Yet Set";
                                }
                                ?>
                                </p>
                                <p><i class="fa fa-envelope fa-fw w3-margin-right w3-large w3-text-teal"></i>
                                    <?php
                                if(isset($mail))
                                {
                                    echo $mail;
                                }
                                else
                                {
                                    echo "Not Yet Set";
                                }
                                ?>
                                </p>
                                <p><i class="fa fa-phone fa-fw w3-margin-right w3-large w3-text-teal"></i>
                                    <?php
                                if(isset($mobile_no))
                                {
                                    echo $mobile_no;
                                }
                                else
                                {
                                    echo "Not Yet Set";
                                }
                                ?>
                                </p>
                                <button type="submit" class="w3-button w3-round-large w3-padding w3-teal"
                                    style="text-decoration: none;"><a href="editprofile.php"
                                        style="text-decoration:none;">Edit Profile</a></button>
                                <hr>
                            </div>
                        </div><br>

                        <!-- End Left Column -->
                    </div>


                    <!-- Right Column -->
                    <div class="w3-twothird">
                        <?php
                        $sql5="SELECT * FROM tbl_uploads WHERE user_id='$id' LIMIT 2";
                        $result5=mysqli_query($conn,$sql5);
                        $details=mysqli_fetch_all($result5,MYSQLI_ASSOC);
                        ?>
                        <div class="w3-container w3-card w3-white w3-padding">
                            <h2 class="w3-text-grey w3-padding-16"><i
                                    class="fa fa-suitcase fa-fw w3-margin-right w3-xxlarge w3-text-teal"></i>My Notes
                                <hr>
                            </h2>

                            <?php
                            foreach($details as $detail)
                            {
                            ?>
                            <div class="w3-container">
                                <h3 class="w3-opacity"><b><?php echo $detail['file_name']; ?></b></h3>
                                <h5 class="w3-text-teal"><i
                                        class="fa fa-calendar fa-fw w3-margin-right"></i><?php echo $detail['uploaded_date']; ?>
                                </h5>
                                <h4><?php echo $detail['file_description']; ?></h4>
                                <hr>
                            </div>
                            <?php
                            }
                            ?>
                            <button type="submit" class="w3-button w3-teal w3-round-large w3-padding w3-right">
                                <a href="your_notes.php" style="text-decoration: none;">Show All Notes</a></button>
                        </div>


                        <!-- End Right Column -->
                    </div>

                    <!-- End Grid -->
                </div>

                <!-- End Page Container -->
            </div>



        </div>
    </div>

    <script>
    var openInbox = document.getElementById("myBtn");
    openInbox.click();

    function w3_open() {
        document.getElementById("mySidebar").style.display = "block";
        document.getElementById("myOverlay").style.display = "block";
    }

    function w3_close() {
        document.getElementById("mySidebar").style.display = "none";
        document.getElementById("myOverlay").style.display = "none";
    }

    function myFunc(id) {
        var x = document.getElementById(id);
        if (x.className.indexOf("w3-show") == -1) {
            x.className += " w3-show";
            x.previousElementSibling.className += " w3-red";
        } else {
            x.className = x.className.replace(" w3-show", "");
            x.previousElementSibling.className =
                x.previousElementSibling.className.replace(" w3-red", "");
        }
    }

    openMail("Borge")

    function openMail(personName) {
        var i;
        var x = document.getElementsByClassName("person");
        for (i = 0; i < x.length; i++) {
            x[i].style.display = "none";
        }
        x = document.getElementsByClassName("test");
        for (i = 0; i < x.length; i++) {
            x[i].className = x[i].className.replace(" w3-light-grey", "");
        }
        document.getElementById(personName).style.display = "block";
        event.currentTarget.className += " w3-light-grey";
    }
    </script>

    <script>
    var openTab = document.getElementById("firstTab");
    openTab.click();
    </script>

</body>

</html>
<?php
    }
    else
    {
        header("location: login.php");
    }
?>