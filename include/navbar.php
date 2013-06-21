<?php
$currentFile = $_SERVER["PHP_SELF"];
$parts = Explode('/', $currentFile);
$pagename = $parts[count($parts) - 1];
?>
  <div class='navbar navbar-inverse navbar-fixed-top'>
    <div class='navbar-inner nav-collapse' style="height: auto;">
      <div class='container'>
        <ul class="nav">
          <li <?php if ($pagename == 'index.php'){ echo'class="active"';}?>><a href="index.php">Home</a></li>
          <li <?php if ($pagename == 'global.php'){ echo'class="active"';}?>><a href="global.html">Global</a></li>
          <li <?php if ($pagename == 'top10.php'){ echo'class="active"';}?>><a href="top10.html">Top 10</a></li>
          <li <?php if ($pagename == 'ipcomp.php'){ echo'class="active"';}?>><a href="ipcomp.php">IPv4/6</a></li>
          <li <?php if ($pagename == 'peras.php'){ echo'class="active"';}?>><a href="peras.php">Per AS</a></li>
          <li <?php if ($pagename == 'perrir.php'){ echo'class="active"';}?>><a href="perrir.html">RIR Stats</a></li>
          <li <?php if ($pagename == 'validitytables.php'){ echo'class="active"';}?>><a href="validitytables.php">RPKI routes</a></li>
          <li <?php if ($pagename == 'maps.php'){ echo'class="active"';}?>><a href="maps.html">World map</a></li>
          <li <?php if ($pagename == 'trends.php'){ echo'class="active"';}?>><a href="trends.html">Trends</a></li>
        </ul>
      </div>
    </div>
  </div>
