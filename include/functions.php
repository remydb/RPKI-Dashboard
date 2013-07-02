<?php
$colours = array("3366cc","dc3912","ff9900","109618","990099","0099c6","dd4477","66aa00","b82e2e","6e97e9");
$threehours = 10800;
$date = date("d-m-y", time()-$threehours)."_routes";
$vrptable = date("d-m-y", time()-$threehours)."_vrp";

function mySQLi()
{
    $mysqli = new mysqli("localhost", "username", "password", "bgp");
    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    }
    return $mysqli; 
}

function getRIRlist()
{
    $mysqli = mySQLi();

    $query = "SELECT DISTINCT(RIR) as RIR FROM `".$GLOBALS['date']."` ORDER BY RIR";
    if (!$result = $mysqli->query($query))
    {
        echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }

    while ($row = $result->fetch_row())
    {
        //echo "<a href='anpie.php?asn=" . $row[0] . "'>AS" . $row[0] . "</a><br>";
        echo "<option value='" . $row[0] . "'>" . $row[0] . "</option>";
    }
    $mysqli->close();
}

function getASlist()
{
    $mysqli = mySQLi();

    $query = "SELECT DISTINCT(ASN) as ASN FROM `".$GLOBALS['date']."` ORDER BY ASN";
    if (!$result = $mysqli->query($query))
    {
        echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }

    while ($row = $result->fetch_row())
    {
        //echo "<a href='anpie.php?asn=" . $row[0] . "'>AS" . $row[0] . "</a><br>";
        echo "<option value='" . $row[0] . "'>" . $row[0] . "</option>";
    }
    $mysqli->close();
}

function getASlistjson($part, $limit)
{
    $mysqli = mySQLi();
    $part .= '%';

    if (!($stmt = $mysqli->prepare("SELECT DISTINCT(ASN) as ASN FROM `".$GLOBALS['date']."` WHERE `ASN` LIKE ? ORDER BY ASN LIMIT ?"))) {
        echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }
    $stmt->bind_param("si", $part, $limit);
    if (!$stmt->execute()) {
         echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
    }
    $stmt->bind_result($item);
    $a = "";
    while ($stmt->fetch())
    {
        $a .= "\"".$item."\", ";
    }
    $stmt->close();
    $mysqli->close();


    return rtrim($a, ", ");
}

function query_totals($status, $ipver = '%')
{
    $mysqli = mySQLi();
    
    if (!($stmt = $mysqli->prepare("SELECT count(*) FROM `".$GLOBALS['date']."` where Validity like ? and IPver like ?"))) {
        echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }
    $stmt->bind_param("ss", $status, $ipver);
    if (!$stmt->execute()) {
         echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
    }
    $stmt->bind_result($total);
    if (!($stmt->fetch())) {
        echo "Getting result failed: (" . $stmt->errno . ") " . $stmt->error;
    }
    $mysqli->close();
    return $total;
}

function query_totals_peras($status, $asn)
{
        $mysqli = mySQLi();

    if (!($stmt = $mysqli->prepare("SELECT count(*) FROM `".$GLOBALS['date']."` where Validity like ? and ASN = ?"))) {
        echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }
    $stmt->bind_param("ss", $status, $asn);
    if (!$stmt->execute()) {
         echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
    }
    $stmt->bind_result($total);
    if (!($stmt->fetch())) {
        echo "Getting result failed: (" . $stmt->errno . ") " . $stmt->error;
    }
    $mysqli->close();
    return $total;
}

function query_totals_per_rir($status, $rir)
{
        $mysqli = mySQLi();

        if (!($stmt = $mysqli->prepare("SELECT count(*) FROM `".$GLOBALS['date']."` where Validity like ? AND RIR like ?"))) {
            echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
        }
        $stmt->bind_param("ss", $status, $rir);
        if (!$stmt->execute()) {
             echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        }
        $stmt->bind_result($total);
        if (!($stmt->fetch())) {
            echo "Getting result failed: (" . $stmt->errno . ") " . $stmt->error;
        }
        $mysqli->close();
        return $total;
}

function query_total_prefixes($status)
{
        $mysqli = mySQLi();

        if (!($stmt = $mysqli->prepare("SELECT count(*) FROM `".$GLOBALS['date']."` where Validity like ?"))) {
            echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
        }
        $stmt->bind_param("s", $status);
        if (!$stmt->execute()) {
             echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        }
        $stmt->bind_result($total);
        if (!($stmt->fetch())) {
            echo "Getting result failed: (" . $stmt->errno . ") " . $stmt->error;
        }
        $mysqli->close();
        return $total;
}

function newpichart($a, $b, $c, $lega, $legb, $legc, $title, $chart, $ipver = '%'){
        $vara = query_totals($a, $ipver);
        $varb = query_totals($b, $ipver);
        $varc = query_totals($c, $ipver);
        $total = $vara + $varb + $varc;
        $pera = round($vara / $total * 100, 2);
        $perb = round($varb / $total * 100, 2);
        $perc = round($varc / $total * 100, 2);
        
    print "google.setOnLoadCallback(draw$chart);
        function draw$chart() {
            var data = google.visualization.arrayToDataTable([
            ['Legend - Percent', 'Value'],
            ['$lega - $pera%', $vara],
            ['$legb - $perb%', $varb],
            ['$legc - $perc%', $varc]
            ]);

            // Create and draw the visualization.
            var options = {'title':'$title','width':600,'height':600,'is3D':1};

            var chart = new google.visualization.PieChart(document.getElementById('$chart'));
            chart.draw(data, options);

            function selectHandler(e) {
                var selection = chart.getSelection();

                for (var i = 0; i < selection.length; i++) {
                    var item = selection[i];
                    if (item.row != null && item.column != null) {
                      var str = data.getFormattedValue(item.row, item.column);
                    } else if (item.row != null) {
                      var str = data.getFormattedValue(item.row, 0);
                    } else if (item.column != null) {
                      var str = data.getFormattedValue(0, item.column);
                    }
                }
                if (str.substr(0,1) == 'U'){
                    return; //Do nothing
                }
                else {
                    $('#waitpopup').modal('show');
                    location.href='validitytables_' + str.substr(0,1) + '.html';
                }                  
            }

            if (location.pathname.substring(1) == 'global.html' || location.pathname.substring(1) == 'global.php'){
                google.visualization.events.addListener(chart, 'select', selectHandler);
            }
        }";
}

function newpichart2($a, $b, $c, $d, $e, $lega, $legb, $legc, $legd, $lege, $title, $chart, $ipver = '%'){
        $vara = query_totals($a, $ipver);
        $varb = query_totals($b, $ipver);
        $varc = query_totals($c, $ipver);
        $vard = query_totals($d, $ipver); 
	$vare = query_totals($e, $ipver);    
	$total = $vara + $varb + $varc + $vard + $vare;
        $pera = round($vara / $total * 100, 2);
        $perb = round($varb / $total * 100, 2);
        $perc = round($varc / $total * 100, 2);
    	$perd = round($vard / $total * 100, 2);
        $pere = round($vare / $total * 100, 2);
	print "google.setOnLoadCallback(draw$chart);
        function draw$chart() {
        var data = google.visualization.arrayToDataTable([
                ['Topping', 'Slices'],
                ['$lega - $vara ($pera%)', $vara],
                ['$legb - $varb ($perb%)', $varb],
                ['$legc - $varc ($perc%)', $varc],
        	['$legd - $vard ($perd%)', $vard],
		['$lege - $vare ($pere%)', $vare]
        ]);

        // Create and draw the visualization.
        var options = {'title':'$title','width':600,'height':600,'is3D':1};
        var chart = new google.visualization.PieChart(document.getElementById('$chart'));
        chart.draw(data, options);

        google.visualization.events.addListener(chart, 'select', selectHandler);

            function selectHandler(e) {
                var selection = chart.getSelection();

                for (var i = 0; i < selection.length; i++) {
                    var item = selection[i];
                    if (item.row != null && item.column != null) {
                      var str = data.getFormattedValue(item.row, item.column);
                    } else if (item.row != null) {
                      var str = data.getFormattedValue(item.row, 0);
                    } else if (item.column != null) {
                      var str = data.getFormattedValue(0, item.column);
                    }
                }
                if (str.indexOf('Invalid AS') !== -1){
                    var validity = 'IA';
                }
                else if (str.indexOf('Fixed') !== -1){
                    var validity = 'IP';
                }
                else if (str.indexOf('Range') !== -1){
                    var validity = 'IQ';
                }
                else if (str.indexOf('AS & Prefix') !== -1){
                    var validity = 'IB';
                }
                else if (str.indexOf('Valid') !== -1){
                    var validity = 'V';
                }
                $('#waitpopup').modal('show');
                window.location.href='validitytables_' + validity + '.html';               
            }

            if (location.pathname.substring(1) == 'global.html' || location.pathname.substring(1) == 'global.php'){
                google.visualization.events.addListener(chart, 'select', selectHandler);
            }
      }";
}

function newpichartperip($validity, $title, $chart){
        $vara = query_totals($validity, '4');
        $varb = query_totals($validity, '6');
        $total = $vara + $varb;
        $pera = round($vara / $total * 100, 2);
        $perb = round($varb / $total * 100, 2);
        
    print "google.setOnLoadCallback(draw$chart);
        function draw$chart() {
        var data = google.visualization.arrayToDataTable([
            ['Topping', 'Slices'],
        ['IPv4 - $pera%', $vara],
        ['IPv6 - $perb%', $varb]
        ]);

        // Create and draw the visualization.
        var options = {'title':'$title','width':800,'height':600,'is3D':1};
        new google.visualization.PieChart(document.getElementById('$chart')).
            draw(data, options);
      }";
}

function newpichartperas($asn, $a, $b, $c, $lega, $legb, $legc, $title, $chart, $d = NULL, $legd = NULL, $e = NULL, $lege = NULL){
        $vara = query_totals_peras($a, $asn);
        $varb = query_totals_peras($b, $asn);
        $varc = query_totals_peras($c, $asn);
        if ($d != NULL){
            $vard = query_totals_peras($d, $asn);
            $vare = query_totals_peras($e, $asn);
            $total = $vara + $varb + $varc + $vard + $vare;
        }
        else{
            $total = $vara + $varb + $varc;
        }
        $pera = round($vara / $total * 100, 2);
        $perb = round($varb / $total * 100, 2);
        $perc = round($varc / $total * 100, 2);
        if ($d != NULL){
            $perd = round($vard / $total * 100, 2);
            $pere = round($vare / $total * 100, 2);
        }
        
    print "google.setOnLoadCallback(draw$chart);
        function draw$chart() {
        var data = google.visualization.arrayToDataTable([
            ['Topping', 'Slices'],\n['$lega - $pera%', $vara],\n['$legb - $perb%', $varb],\n['$legc - $perc%', $varc],";
        if ($d != NULL){
            echo "\n['$legd - $perd%', $vard],\n['$lege - $pere%', $vare]";
        }
        echo "]);

        // Create and draw the visualization.
        var options = {'title':'$title','width':800,'height':500,'is3D':1};
        new google.visualization.PieChart(document.getElementById('$chart')).
            draw(data, options);
      }";
}

function newpichartperrir($rir, $a, $b, $c, $lega, $legb, $legc, $title, $chart, $d = NULL, $legd = NULL, $e = NULL, $lege = NULL){
        $vara = query_totals_per_rir($a, $rir);
        $varb = query_totals_per_rir($b, $rir);
        $varc = query_totals_per_rir($c, $rir);
        
        if ($d != NULL){
            $vard = query_totals_per_rir($d, $rir);
            $vare = query_totals_per_rir($e, $rir);
            $total = $vara + $varb + $varc + $vard + $vare;
        }
        else{
            $total = $vara + $varb + $varc;
        }
        $pera = round($vara / $total * 100, 2);
        $perb = round($varb / $total * 100, 2);
        $perc = round($varc / $total * 100, 2);
        if ($d != NULL){
            $perd = round($vard / $total * 100, 2);
            $pere = round($vare / $total * 100, 2);
        }
        
    print "google.setOnLoadCallback(draw$chart);
        function draw$chart() {
        var data = google.visualization.arrayToDataTable([
            ['Topping', 'Slices'],\n['$lega - $pera%', $vara],\n['$legb - $perb%', $varb],\n['$legc - $perc%', $varc],";
        if ($d != NULL){
            echo "\n['$legd - $perd%', $vard],\n['$lege - $pere%', $vare]";
        }
        echo "]);

        // Create and draw the visualization.
        var options = {'title':'$title','width':800,'height':500,'is3D':1};
        new google.visualization.PieChart(document.getElementById('$chart')).
            draw(data, options);
      }";
}

function newbarchart($type, $validity, $title, $group, $chart, $amount){
    $mysqli = mySQLi();

        $query = "SELECT `$type`, 
            COUNT(`$type`) AS `occurrence` 
            FROM `".$GLOBALS['date']."` 
            WHERE Validity LIKE '$validity'
            AND $type != ''
            GROUP BY `$type` ORDER BY `occurrence` 
            DESC LIMIT $amount;";

    if (!$result = $mysqli->query($query))
        {
            echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
        }
        $stack = array();
        $stack2 = array();
        while ($row = $result->fetch_row())
        {
          if ($type == 'ASN'){
            array_push($stack, "AS" . $row[0] . " (" . $row[1] . ")");// = $row[1];
          }
          else{
            array_push($stack, strtoupper($row[0]) . " (" . $row[1] . ")");// = $row[1];
          }
          array_push($stack2, $row[1]);
        }
        $mysqli->close();
        
        //foreach ($stack as $v){
    //print "'$v', ";
    //} 
    print "google.setOnLoadCallback(draw$chart);
        function draw$chart() {
        var data = google.visualization.arrayToDataTable([
        ['Year',";
    foreach ($stack as $v){
        print "'$v',";
    }  
    print "],";
    
    print " 
    ['', ";
    foreach ($stack2 as $v){
                print "$v,";
        }
    print "]
        ]);

        // Create and draw the visualization.
        var options = {
        hAxis: {title: \"$title\"},
        //width: 800,
        height: 400,
        chartArea:{left:0,top:0,width:\"80%\",height:\"80%\"} 
        };
        
        var chart = new google.visualization.BarChart(document.getElementById('$chart'));
        chart.draw(data, options);

        google.visualization.events.addListener(chart, 'select', selectHandler);

        function selectHandler(e) {
            var selection = chart.getSelection()[0];
            var label = data.getColumnLabel(selection.column);
            if (label.substr(0,2) == 'AS'){
                $('#waitpopup').modal('show');
                window.location.href='peras.php?asn=' + label.substr(2).split(' ')[0];
            }
        }
      }";
}

function newrirbarchart($RIR, $chart){
    $mysqli = mySQLi();

    $query = "SELECT `RIR`, 
            COUNT(`RIR`) AS `occurrence` 
            FROM `".$GLOBALS['date']."` 
            WHERE Validity LIKE 'V'
            AND RIR = '$RIR'
            GROUP BY `RIR` ORDER BY `occurrence`;";

    if (!$result = $mysqli->query($query))
        {
            echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
        }
        $stack = array();
        $stack2 = array();
        while ($row = $result->fetch_row())
        {
            array_push($stack, "Valid ($row[1])");// = $row[1];
            array_push($stack2, $row[1]);
        }

    $query = "SELECT `RIR`, 
            COUNT(`RIR`) AS `occurrence` 
            FROM `".$GLOBALS['date']."` 
            WHERE Validity LIKE 'I%'
            AND RIR = '$RIR'
            GROUP BY `RIR` ORDER BY `occurrence`;";

    if (!$result = $mysqli->query($query))
        {
            echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
        }
        while ($row = $result->fetch_row())
        {
            array_push($stack, "Invalid ($row[1])");// = $row[1];
            array_push($stack2, $row[1]);
        }

        $mysqli->close();
        $lowercase = strtolower($RIR);
        //foreach ($stack as $v){
    //print "'$v', ";
    //} 
    print "google.setOnLoadCallback(draw$chart);
        function draw$chart() {
        var data = google.visualization.arrayToDataTable([
        ['Year',";
    foreach ($stack as $v){
        print "'$v',";
    }
    print "],";
    
    print " 
    ['', ";
    foreach ($stack2 as $v){
                print "$v,";
        }
    print "]
        ]);

        // Create and draw the visualization.
        var options = {
        hAxis: {title: \"$RIR\"},
        //width: 400,
        height: 200,
        isStacked: true,
        chartArea:{left:0,top:0,width:\"80%\",height:\"80%\"} 
    };
        
        var chart = new google.visualization.BarChart(document.getElementById('$chart'));
        chart.draw(data, options);
    ";

    print "google.visualization.events.addListener(chart, 'select', selectHandler);

        function selectHandler(e) {
            location.href='$lowercase.html';
        }
      };\n";
}

function newlinechart($validity, $validity2, $title, $chart, $as, $rir = '%', $lega, $legb){
        
        //Dirty way to create an array with dates of last 7 days
        //Should consider using dateperiod/dateinterval instead
        $daterange = array();
        // $daterange[0] = date("d-m-y", time()-604800)."_routes"; //7 days ago  
        // $daterange[1] = date("d-m-y", time()-518400)."_routes"; //6 days ago
        // $daterange[2] = date("d-m-y", time()-432000)."_routes"; //5 days ago
        // $daterange[3] = date("d-m-y", time()-345600)."_routes"; //4 days ago
        // $daterange[4] = date("d-m-y", time()-259200)."_routes"; //3 days ago
        // $daterange[5] = date("d-m-y", time()-172800)."_routes"; //2 days ago      
        // $daterange[6] = date("d-m-y", time()-86400)."_routes";  //yesterday
        // $daterange[7] = date("d-m-y")."_routes";  //today

    $daterange[0] = date("d-m-y", time()-1123200)."_routes"; //7 days ago  
    $daterange[1] = date("d-m-y", time()-1036800)."_routes"; //6 days ago
    $daterange[2] = date("d-m-y", time()-950400)."_routes"; //5 days ago
    $daterange[3] = date("d-m-y", time()-864000)."_routes"; //4 days ago
    $daterange[4] = date("d-m-y", time()-777600)."_routes"; //3 days ago
    $daterange[5] = date("d-m-y", time()-691200)."_routes"; //2 days ago
    $daterange[6] = date("d-m-y", time()-604800)."_routes"; //7 days ago  
    $daterange[7] = date("d-m-y", time()-518400)."_routes"; //6 days ago
    $daterange[8] = date("d-m-y", time()-432000)."_routes"; //5 days ago
    $daterange[9] = date("d-m-y", time()-345600)."_routes"; //4 days ago
    $daterange[10] = date("d-m-y", time()-259200)."_routes"; //3 days ago
    $daterange[11] = date("d-m-y", time()-172800)."_routes"; //2 days ago      
    $daterange[12] = date("d-m-y", time()-86400)."_routes";  //yesterday
    $daterange[13] = date("d-m-y")."_routes";  //today

        $mysqli = mySQLi();

        $stack = array();
        foreach ($daterange as $xdate){
            $query = "SELECT COUNT(*) 
                FROM `$xdate` 
                WHERE Validity LIKE '$validity' AND ASN LIKE '$as' AND RIR like '$rir'";
                //echo $query.'\n';
            if ($result = $mysqli->query($query))
            {
                //echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
                $row = $result->fetch_row();
                $stack[$xdate] = $row[0];
            }
            
        }
        
    $stack2 = array();
        foreach ($daterange as $xdate){
                $query = "SELECT COUNT(*)
                    FROM `$xdate`
                    WHERE Validity LIKE '$validity2' AND ASN LIKE '$as' AND RIR like '$rir'";
                    //echo $query.'\n';
                if ($result = $mysqli->query($query))
                {
                    //echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
                                $row = $result->fetch_row();
                        $stack2[$xdate] = $row[0];
                }

        }

    $mysqli->close();
        

    print "google.setOnLoadCallback(draw$chart);
        function draw$chart() {
        var data = google.visualization.arrayToDataTable([
          ['Date','$lega', '$legb'],";

         $keys = array_keys($stack);
         foreach ($keys as $key){
            echo "['".explode("_", $key)[0]."',".$stack[$key].", ".$stack2[$key]."],";
         }

        echo "]);

        // Create and draw the visualization.
        var options = {
        title: '$title',
        width: 800,
        height: 500,
        };
        
        var chart = new google.visualization.LineChart(document.getElementById('$chart'));
        chart.draw(data, options);
      }";
}

function newinvalidlinechart($title, $chart, $as = '%', $rir = '%'){
        
    //Dirty way to create an array with dates of last 7 days
    //Should consider using dateperiod/dateinterval instead
    //Or even query database for all previous tables
    //And add each found one to the array
    $daterange = array();
    // $daterange[0] = date("d-m-y", time()-604800)."_routes"; //7 days ago  
    // $daterange[1] = date("d-m-y", time()-518400)."_routes"; //6 days ago
    // $daterange[2] = date("d-m-y", time()-432000)."_routes"; //5 days ago
    // $daterange[3] = date("d-m-y", time()-345600)."_routes"; //4 days ago
    // $daterange[4] = date("d-m-y", time()-259200)."_routes"; //3 days ago
    // $daterange[5] = date("d-m-y", time()-172800)."_routes"; //2 days ago      
    // $daterange[6] = date("d-m-y", time()-86400)."_routes";  //yesterday
    // $daterange[7] = date("d-m-y")."_routes";  //today

    $daterange[0] = date("d-m-y", time()-1123200)."_routes"; //7 days ago  
    $daterange[1] = date("d-m-y", time()-1036800)."_routes"; //6 days ago
    $daterange[2] = date("d-m-y", time()-950400)."_routes"; //5 days ago
    $daterange[3] = date("d-m-y", time()-864000)."_routes"; //4 days ago
    $daterange[4] = date("d-m-y", time()-777600)."_routes"; //3 days ago
    $daterange[5] = date("d-m-y", time()-691200)."_routes"; //2 days ago
    $daterange[6] = date("d-m-y", time()-604800)."_routes"; //7 days ago  
    $daterange[7] = date("d-m-y", time()-518400)."_routes"; //6 days ago
    $daterange[8] = date("d-m-y", time()-432000)."_routes"; //5 days ago
    $daterange[9] = date("d-m-y", time()-345600)."_routes"; //4 days ago
    $daterange[10] = date("d-m-y", time()-259200)."_routes"; //3 days ago
    $daterange[11] = date("d-m-y", time()-172800)."_routes"; //2 days ago      
    $daterange[12] = date("d-m-y", time()-86400)."_routes";  //yesterday
    $daterange[13] = date("d-m-y")."_routes";  //today

    $mysqli = mySQLi();

    $stack = array();
    foreach ($daterange as $xdate){
        $query = "SELECT COUNT(*) 
            FROM `$xdate` 
            WHERE Validity LIKE 'IA' AND ASN LIKE '$as' AND RIR like '$rir'";
            //echo $query.'\n';
        if ($result = $mysqli->query($query))
        {
            //echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
            $row = $result->fetch_row();
            $stack[$xdate] = $row[0];
        }
        
    }
        
    $stack2 = array();
        foreach ($daterange as $xdate){
                $query = "SELECT COUNT(*)
                    FROM `$xdate`
                    WHERE Validity LIKE 'IP' AND ASN LIKE '$as' AND RIR like '$rir'";
                    //echo $query.'\n';
                if ($result = $mysqli->query($query))
                {
                    //echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
                                $row = $result->fetch_row();
                        $stack2[$xdate] = $row[0];
                }

        }

    $stack3 = array();
        foreach ($daterange as $xdate){
                $query = "SELECT COUNT(*)
                    FROM `$xdate`
                    WHERE Validity LIKE 'IQ' AND ASN LIKE '$as' AND RIR like '$rir'";
                    //echo $query.'\n';
                if ($result = $mysqli->query($query))
                {
                    //echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
                                $row = $result->fetch_row();
                        $stack3[$xdate] = $row[0];
                }

        }

    $stack4 = array();
        foreach ($daterange as $xdate){
                $query = "SELECT COUNT(*)
                    FROM `$xdate`
                    WHERE Validity LIKE 'IB' AND ASN LIKE '$as' AND RIR like '$rir'";
                    //echo $query.'\n';
                if ($result = $mysqli->query($query))
                {
                    //echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
                                $row = $result->fetch_row();
                        $stack4[$xdate] = $row[0];
                }

        }

    $mysqli->close();
        

    print "google.setOnLoadCallback(draw$chart);
        function draw$chart() {
        var data = google.visualization.arrayToDataTable([
          ['Date','Invalid ASN', 'Invalid Prefix (Fixed length mismatch)', 'Invalid Prefix (Range length exceeded)', 'Invalid ASN & Prefix'],";

         $keys = array_keys($stack);
         foreach ($keys as $key){
            echo "['".explode("_", $key)[0]."',".$stack[$key].", ".$stack2[$key].", ".$stack3[$key].", ".$stack4[$key]."],";
         }

        echo "]);

        // Create and draw the visualization.
        var options = {
        title: '$title',
        width: 800,
        height: 500,
        };
        
        var chart = new google.visualization.LineChart(document.getElementById('$chart'));
        chart.draw(data, options);
      }";
}

function newtable($as){
        $mysqli = mySQLi();

        $query = "SELECT *
            FROM `".$GLOBALS['date']."`
            WHERE ASN LIKE '$as'
            GROUP BY `Prefix`";

        if ($result = $mysqli->query($query)) {

        /* fetch associative array */
        print "<table class=\"table table-striped table-bordered table-hover\" id=\"pagetable\">";
        print "<col width=\"55%\"><col width=\"15%\"><col width=\"15%\"><col width=\"15\">";
    	print "<thead><tr><th><b>Prefix</b></th><th><b>IP Version</b></th><th><b>Validity</b></th><th><b>Info</b></th></tr></thead><tbody>";
        while ($row = $result->fetch_assoc()) {
        //if (substr('_abcdef', 0, 1) === '_')
            
            if (strpos($row["Validity"],"I") !== false){
                    //$roa = getrelroa($row["Prefix"]);
                    $class = "error";
                    $validity = "<span class=\"label label-important\">Invalid</span>";
                    $button = "<div class=\"btn-group\"><button class=\"btn\" id=\"".$row['Prefix']."\" rel=\"popover\" data-content=\"\">Show details</button></div>";
                    }
            elseif ($row["Validity"] == "V"){
                    //$roa = getrelroa($row["Prefix"]);
                    $class = "success";
                    $validity = "<span class=\"label label-success\">Valid</span>";
                    $button = "<div class=\"btn-group\"><button class=\"btn\" id=\"".$row['Prefix']."\" rel=\"popover\" data-content=\"\">Show details</button></div>";
                    }
            else {$class = "warning";
                  $validity = "<span class=\"label label-warning\">Unknown</span>";
                  $button = " ";
                    }
            print "<tr class=\"$class\">
                    <td>".$row["Prefix"]."</td>
                    <td>".$row["IPver"]."</td>
                    <td>$validity</td>
                    <td><center>$button</center></td>
                   </tr>";
        }
        print "</tbody></table>";
        /* free result set */
        $result->free();
        }
}

function newtableperrir($rir){
        $mysqli = mySQLi();

        $query = "SELECT *
            FROM `".$GLOBALS['date']."`
            WHERE RIR = '$rir'
            AND VALIDITY LIKE 'I%'
            GROUP BY `Prefix`";

        if ($result = $mysqli->query($query)) {

        /* fetch associative array */
        print "<table class=\"table table-striped table-bordered table-hover\" id=\"pagetable\">";
        print "<col width=\"40%\"><col width=\"15%\"><col width=\"15%\"><col width=\"15%\"><col width=\"15\">";
        print "<thead><tr><th><b>Prefix</b></th><th><b>ASN</b></th><th><b>IP Version</b></th><th><b>Validity</b></th><th><b>Info</b></th></tr></thead><tbody>";
        while ($row = $result->fetch_assoc()) {
           
            $class = "error";
            $validity = "<span class=\"label label-important\">Invalid</span>";
            $button = "<div class=\"btn-group\"><button class=\"btn\" id=\"".$row['Prefix']."\" rel=\"popover\" data-content=\"\">Show details</button></div>";
            print "<tr class=\"$class\">
                    <td>".$row["Prefix"]."</td>
                    <td>".$row["ASN"]."</td>
                    <td>".$row["IPver"]."</td>
                    <td>$validity</td>
                    <td><center>$button</center></td>
                   </tr>";
        }
        print "</tbody></table>";
        /* free result set */
        $result->free();
        }
}

function validitytable($validity){
    if ($validity == 'U'){
        echo "<div class='alert alert-error'>Cannot show table with all unknown prefixes. Too many results.<br/><br/></div>
        <pre>                                  _.---\"'\"\"\"\"\"'`--.._
                             _,.-'                   `-._
                         _,.\"                            -.
                     .-\"\"   ___...---------.._             `.
                     `---'\"\"                  `-.            `.
                                                 `.            \
                                                   `.           \
                                                     \           \
                                                      .           \
                                                      |            .
                                                      |            |
                                _________             |            |
                          _,.-'\"         `\"'-.._      :            |
                      _,-'                      `-._.'             |
                   _.'                              `.             '
        _.-.    _,+......__                           `.          .
      .'    `-\"'           `\"-.,-\"\"--._                 \        /
     /    ,'                  |    __  \                 \      /
    `   ..                       +\"  )  \                 \    /
     `.'  \          ,-\"`-..    |       |                  \  /
      / \" |        .'       \   '.    _.'                   .'
     |,..\"--\"\"\"--..|    \"    |    `\"\"`.                     |
   ,\"               `-._     |        |                     |
 .'                     `-._+         |                     |
/                           `.                        /     |
|    `     '                  |                      /      |
`-.....--.__                  |              |      /       |
   `./ \"| / `-.........--.-   '              |    ,'        '
     /| ||        `.'  ,'   .'               |_,-+         /
    / ' '.`.        _,'   ,'     `.          |   '   _,.. /
   /   `.  `\"'\"'\"\"'\"   _,^--------\"`.        |    `.'_  _/
  /... _.`:.________,.'              `._,.-..|        \"'
 `.__.'                                 `._  /
                                           \"' </pre>";
        return;
    }

    $mysqli = mySQLi();

    $query = "SELECT Prefix, ASN, IPver, Validity
        FROM `".$GLOBALS['date']."`
        WHERE Validity LIKE '$validity'
        GROUP BY `Prefix`";

    if ($result = $mysqli->query($query)) {

    /* fetch associative array */
    print "<table class=\"table table-striped table-bordered table-hover\" id=\"pagetable\">";
    print "<col width=\"40%\"><col width=\"15%\"><col width=\"15%\"><col width=\"15%\"><col width=\"15\">";
    print "<thead><tr><th><b>Prefix</b></th><th><b>ASN</b></th><th><b>IP Version</b></th><th><b>Validity</b></th><th><b>Info</b></th></tr></thead><tbody>";
    while ($row = $result->fetch_assoc()) {
    //if (substr('_abcdef', 0, 1) === '_')
        
        if (strpos($row["Validity"],"I") !== false){
                //$roa = getrelroa($row["Prefix"]);
                $class = "error";
                $validity = "<span class=\"label label-important\">Invalid</span>";
                $button = "<div class=\"btn-group\"><button class=\"btn\" id=\"".$row['Prefix']."\" rel=\"popover\" data-content=\"\">Show details</button></div>";
                }
        elseif ($row["Validity"] == "V"){
                //$roa = getrelroa($row["Prefix"]);
                $class = "success";
                $validity = "<span class=\"label label-success\">Valid</span>";
                $button = "<div class=\"btn-group\"><button class=\"btn\" id=\"".$row['Prefix']."\" rel=\"popover\" data-content=\"\">Show details</button></div>";
                }
        else {$class = "warning";
              $validity = "<span class=\"label label-warning\">Unknown</span>";
              $button = " ";
                }
        print "<tr class=\"$class\">
                <td>".$row["Prefix"]."</td>
                <td>".$row["ASN"]."</td>
                <td>".$row["IPver"]."</td>
                <td>$validity</td>
                <td><center>$button</center></td>
               </tr>";
    }
    print "</tbody></table>";
    /* free result set */
    $result->free();
    }
}

function rirtable(){
    $rirs = array('RIPE', 'LACNIC', 'ARIN', 'APNIC', 'AFRINIC');

    print "<table class=\"table table-striped table-bordered table-hover\" id=\"pagetable\"><thead>
    <tr>
    <th width=\"200px\"><b>RIR</b></th>
    <th><b>Total</b></th>
    <th><b>Valid</b></th>
    <th><b>Invalid</b></th>
    <th><b>Unknown</b></th>
    <th><b>RPKI Adoption Rate</b></th>
    </tr></thead><tbody>";

    foreach ($rirs as &$value) {
            $total = query_totals_per_rir('%', $value);
            $valid = query_totals_per_rir('V', $value);
            $validper = round($valid/$total*100,2);

            $invalid = query_totals_per_rir('I%', $value);
            $invalidper = round($invalid/$total*100,2);

            $unknown = $total-$valid-$invalid;
            $unknownper = round($unknown/$total*100,2);

            $adop = round(($total-$unknown)/$total*100,2);
            $lowercase = strtolower($value);
            print "<tr><td><a href='$lowercase.html'>$value</a></td>
            <td><span class=\"label label-info\">$total (100%)</span></td>
            <td><span class=\"label label-success\">$valid ($validper%)</span></td>
            <td><span class=\"label label-important\">$invalid ($invalidper%)</span></td>
            <td><span class=\"label label-warning\">$unknown ($unknownper%)</span></td>
            <td><span class=\"label label-inverse\">$adop%</span></td>
            </tr>";
            };

    print "</tbody></table>";
}

function newgeochart($validity, $title, $chart){    
    $rirs = array('ARIN', 'LACNIC', 'RIPE', 'AFRINIC', 'APNIC');
    $arin = array('CA','AI','AG','BS','BB','BM','KY','DM','GD','GP','JM','MQ','MS','BL','KN','LC','PM','VC','MF','TC','VG','US','AQ','BV','HM','SH');
    $lacnic = array('AR','AW','BZ','BO','BR','CL','CO','CR','CU','DO','EC','SV','FK','GF','GT','GY','HT','HN','MX','AN','NI','PA','PY','PE','GS','SR','TT','UY','VE');
    $ripe = array('AX','AL','AD','AM','AT','AZ','BH','BY','BE','BA','BG','HR','CY','CZ','DK','EE','FO','FI','FR','GE','DE','GI','GR','GL','GG','VA','HU','IS','IR','IQ','IE','IM','IL','IT','JE','JO','KZ','KW','KG','LV','LB','LI','LT','LU','MK','MT','MD','MC','ME','NL','NO','OM','PS','PL','PT','QA','RO','RU','SM','SA','RS','SK','SI','ES','SJ','SE','CH','SY','TJ','TR','TM','UA','AE','GB','UZ','YE');
    $afrinic = array('DZ','AO','BJ','BW','BF','BI','CM','CV','CF','TD','KM','CG','CD','CI','DJ','EG','GQ','ER','ET','GA','GM','GH','GN','GW','KE','LS','LR','LY','MG','MW','ML','MR','MU','YT','MA','MZ','NA','NE','NG','RE','RW','ST','SN','SC','SL','SO','ZA','SS','SD','SZ','TZ','TG','TN','UG','EH','ZM','ZW');
    $apnic = array('AF','AS','AU','BD','BT','IO','BN','KH','CN','CX','CC','CK','FJ','PF','TF','GU','HK','IN','ID','JP','KI','KP','KR','LA','MO','MY','MV','MH','FM','MN','MM','NR','NP','NC','NZ','NU','NF','MP','PK','PW','PG','PH','PN','WS','SG','SB','LK','TW','TH','TL','TK','TO','TV','VU','VN','WF');
    
    $total = array();
    $isvalid = array();
    foreach ($rirs as &$value) {
        $total[] = query_totals_per_rir('%', $value);
        $isvalid[] = query_totals_per_rir($validity, $value);
    }

    if ($validity=='V')
    {
    $colors = "['#fd2323', '#ed9200', '#e4d417', '#95c12d']";
    }
    else
    {
    $colors = "['#95c12d', '#e4d417', '#ed9200', '#fd2323']";
    }

    print "google.setOnLoadCallback(draw$chart);
        function draw$chart() {
        var data = google.visualization.arrayToDataTable([
            ['Country', 'Amount (%)'],";
    foreach ($arin as &$value) {
        print "['$value',".round($isvalid[0]/$total[0]*100,2)."],";
    }
    foreach ($lacnic as &$value) {
        print "['$value',".round($isvalid[1]/$total[1]*100,2)."],";
    }
    foreach ($ripe as &$value) {
        print "['$value',".round($isvalid[2]/$total[2]*100,2)."],";
    }
    foreach ($afrinic as &$value) {
        print "['$value',".round($isvalid[3]/$total[3]*100,2)."],";
    }
    foreach ($apnic as &$value) {
        print "['$value',".round($isvalid[4]/$total[4]*100,2)."],";
    }
    print " ]);

        // Create and draw the visualization.
            var options = {
                title: '$title',
                colorAxis: {colors:$colors}
            };
        
        var chart = new google.visualization.GeoChart(document.getElementById('$chart'));
        chart.draw(data, options);
      }";
}
?>
