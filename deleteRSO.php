<!DOCTYPE html>
<html>
    <head>
        <script type="text/javascript" src="js/login.js"></script>
        <meta charset="utf-8" />
        <link rel="icon" href="/favicon.ico" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="theme-color" content="#000000" />
        <title>Login</title>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Alatsi%3A400" />
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro%3A400" />
        <link rel="stylesheet" href="./css/joinRSO.css" />
        <title>Link CSS to HTML</title>
    </head>
    <body>
         <body onload="sendCookie()">
         

         <span class="error" id="RegisterResult"></span>
         <p>
        <?php 
        $userid = $_COOKIE['userid'];

        //echo $userid;

        //$userid = 1;
        include('LAMPAPI/testGetUserRSO.php');?> </p>

    <div class="login-7sb">

    <div class="rectangle-1-Ce9">
    </div>
    <div class="rectangle-2-7WD">
    </div>
    <p class="event-planner-wEM">Event Planner</p>
    <p class="login-Q7w">Your RSOs</p>
    <p class="login-123">Select RSO to leave</p>
    <!-- <div class="login-Udb">LOGIN </div> -->
    <!-- <p class="home-iXw">Home</p> -->
    <!-- <p class="login-nnh">Login</p>
    <p class="sign-up-g7P">Sign up</p> -->
    <input type="button" id="headerButton" class="header" onclick="window.location.href='sign-up.html';"
    value="Sign up">
    <!-- <p class="forgot-my-password-9mf">forgot my password</p>
    <p class="not-a-member-sign-up-RDP">Not a member? Sign up</p> -->
    <img class="vector-1-hRo" src="./assets/vector-1-auj.png" />
    <!-- <p class="contact-QLD">Contact </p> -->

    <div class="container">
    <img class="envelope-closed-1-Xp9" src="./assets/envelope-closed-1.png" />
    </div>

    <img class="facebook-1-xAh" src="./assets/facebook-1-Jhj.png" />
    <img class="twitter-1-3xq" src="./assets/twitter-1-pcd.png" />
    <img class="linkedin-1-AGm" src="./assets/linkedin-1-Foj.png" />
    <!-- <p class="event-Fp1">Event</p> -->
    </div>

    </body>
</html>