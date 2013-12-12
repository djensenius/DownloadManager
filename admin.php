<?php

/*
Copyright (c) 2013, David Jensenius
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met: 

1. Redistributions of source code must retain the above copyright notice, this
   list of conditions and the following disclaimer. 
2. Redistributions in binary form must reproduce the above copyright notice,
   this list of conditions and the following disclaimer in the documentation
   and/or other materials provided with the distribution. 

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

The views and conclusions contained in the software and documentation are those
of the authors and should not be interpreted as representing official policies, 
either expressed or implied, of the FreeBSD Project.
*/

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Download administration</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->
    <link href="bootstrap/css/bootstrap.css" rel="stylesheet">
    <style type="text/css">
      body {
        padding-top: 40px;
        padding-bottom: 40px;
        background-color: #f5f5f5;
      }

      .form-signin {
        max-width: 300px;
        padding: 19px 29px 29px;
        margin: 0 auto 20px;
        background-color: #fff;
        border: 1px solid #e5e5e5;
        -webkit-border-radius: 5px;
           -moz-border-radius: 5px;
                border-radius: 5px;
        -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05);
           -moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);
                box-shadow: 0 1px 2px rgba(0,0,0,.05);
      }
      .form-signin .form-signin-heading,
      .form-signin .checkbox {
        margin-bottom: 10px;
      }
      .form-signin input[type="text"],
      .form-signin input[type="password"] {
        font-size: 16px;
        height: auto;
        margin-bottom: 15px;
        padding: 7px 9px;
      }

    </style>
    <link href=" bootstrap/css/bootstrap-responsive.css" rel="stylesheet">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="../assets/js/html5shiv.js"></script>
    <![endif]-->

  </head>

  <body>

    <div class="container">


<?
session_start();

if (isset($_POST['username']) && isset($_POST['password'])) {
	$_SESSION['user'] = $_POST['username'];
	$_SESSION['password'] = $_POST['password'];
}

if ($_SESSION['user'] == $adminLogin && $_SESSION['password'] == $adminPassword) {
	if (!($link = mysql_connect($databaseServer, $databaseLogin, $databasePassword))) {
		$errno = mysql_errno();
		$errtext = mysql_error();
		trigger_error("internal error $errno:$errtext", E_USER_ERROR);
        return 0;
    }
    
    mysql_select_db($databaseName);
	
	if ($_POST['add']) {
		$email = $_POST['email'];
		$email = mysql_real_escape_string($email);
		$code = md5(date('U'));
		$insert = "INSERT INTO download(email,code,created) VALUES('$email','$code',NOW())";
		if (!($result = mysql_query($insert))) {
			$errno = mysql_errno();
			$errtext = mysql_error();
			print "internal error $errno:$errtext: $statement ";
			return 0;
	    }
	}
	
	?>
    <form caction="admin.php" method="POST">
      <input type="text" name="email" placeholder="Email">
	  <input type="hidden" name="add" value="true">
      <button class="btn btn-medium btn-primary" type="submit">Add email</button>
    </form>
	<table class="table">
		<tr><th>email</th><th>created</th><th>code</th><th>downloaded</th><th></tr>
		<?
	$select = "SELECT id,email,code,created,used,downloaded FROM download ORDER BY email";
	if (!($result = mysql_query($select))) {
		$errno = mysql_errno();
		$errtext = mysql_error();
		print "internal error $errno:$errtext: $statement ";
		return 0;
    }
	
	while ($row = mysql_fetch_row($result)) {
		print "<tr><td>$row[1]</td><td>$row[3]</td><td><a href=\"$downloadURL/?r=$row[2]\">$row[2]</a></td><td>$row[5]</td></tr>";
	}
	print "</table>";
	
	mysql_close($link);
	
} else {
	?>
    <form class="form-signin" action="admin.php" method="POST">
      <h2 class="form-signin-heading">Please sign in</h2>
      <input type="text" name="username" class="input-block-level" placeholder="Username">
      <input type="password" name="password" class="input-block-level" placeholder="Password">
      <button class="btn btn-large btn-primary" type="submit">Sign in</button>
    </form>
	
		
	<?
}
?>


    </div> <!-- /container -->

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
    <script src="bootstrap/js/bootstrap.js"></script>
  </body>
</html>
