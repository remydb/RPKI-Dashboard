<!DOCTYPE html>
<html lang="en">
<head>
<META HTTP-EQUIV="Pragma" CONTENT="no-cache"> 
<title>RPKI Dashboard</title>
<link href="bootstrap/css/bootstrap.css" rel="stylesheet">
<link href="bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
<link href="bootstrap/css/custom.css" rel="stylesheet">
</head>
<body>
<div id='wrap'>
  <!-- Navbar stuff 
  =================================================== -->
  <?php
  include 'include/navbar.php';
  ?>

  <!-- Header stuff
  =================================================== -->
  <?php
  include 'include/header.php';
  ?>

  <!-- Body stuff
  =================================================== -->
  <div class='container'>
    <div id='content' class='row-fluid'>

 <!-- Sidebar
 =============================================== 
      <div class="span3 sidebar" id="navparent">
        <ul class="nav nav-list sidenav" data-spy="affix" data-offset-top="200">
          <li><a href="#AS"><i class="icon-chevron-right"></i> Per AS</a></li>
          <li><a href="#country"><i class="icon-chevron-right"></i> Per country</a></li>
        </ul>
      </div>-->

      <div class='span9 main'>
     <h1>Welcome!</h1><div class="well"><p>This web page has been made to monitor the adoption rate of Route Origin 
  Validation for BGP. A database is created daily using the most recent RIS database dumps provided by RIPE NCC. These 
  databases are used to extract all the statistics shown on this website.<p> 
	</div>
    </div>
  </div>
</div>
  <div id='push'></div>
</div>

    <footer class="footer">
      <div class="container">
        <?php include 'include/footer.php';?>
      </div>
    </footer>
<script src="bootstrap/js/bootstrap.js"></script>
</body>
</html>
