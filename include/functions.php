<?php
$colours = array("3366cc","dc3912","ff9900","109618","990099","0099c6","dd4477","66aa00","b82e2e","6e97e9");
$threehours = 10800;
$date = date("d-m-y", time()-$threehours)."_routes";
$vrptable = date("d-m-y", time()-$threehours)."_vrp";

function mySQLi()
{
    $mysqli = new mysqli("localhost", "root", "noedelsoep", "bgp");
    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    }
    return $mysqli; 
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

function query_totals($status)
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

function newpichart($a, $b, $c, $lega, $legb, $legc, $title, $chart){
        $vara = query_totals($a);
        $varb = query_totals($b);
        $varc = query_totals($c);
        $total = $vara + $varb + $varc;
        $pera = round($vara / $total * 100, 2);
        $perb = round($varb / $total * 100, 2);
        $perc = round($varc / $total * 100, 2);
        
    print "google.setOnLoadCallback(draw$chart);
        function draw$chart() {
        var data = google.visualization.arrayToDataTable([
            ['Topping', 'Slices'],
        ['$lega - $pera%', $vara],
        ['$legb - $perb%', $varb],
        ['$legc - $perc%', $varc]
        ]);

        // Create and draw the visualization.
        var options = {'title':'$title','width':800,'height':600,'is3D':1};
        new google.visualization.PieChart(document.getElementById('$chart')).
            draw(data, options);
      }";
}

function newpichart2($a, $b, $c, $d, $lega, $legb, $legc, $legd, $title, $chart){
        $vara = query_totals($a);
        $varb = query_totals($b);
        $varc = query_totals($c);
        $vard = query_totals($d); 
    $total = $vara + $varb + $varc + $vard;
        $pera = round($vara / $total * 100, 2);
        $perb = round($varb / $total * 100, 2);
        $perc = round($varc / $total * 100, 2);
    $perd = round($vard / $total * 100, 2);
        print "google.setOnLoadCallback(draw$chart);
        function draw$chart() {
        var data = google.visualization.arrayToDataTable([
                ['Topping', 'Slices'],
                ['$lega - $vara ($pera%)', $vara],
                ['$legb - $varb ($perb%)', $varb],
                ['$legc - $varc ($perc%)', $varc],
        ['$legd - $vard ($perd%)', $vard]
        ]);

        // Create and draw the visualization.
        var options = {'title':'$title','width':800,'height':600,'is3D':1};
        new google.visualization.PieChart(document.getElementById('$chart')).
            draw(data, options);
      }";
}

function newpichartperas($asn, $a, $b, $c, $lega, $legb, $legc, $title, $chart){
        $vara = query_totals_peras($a, $asn);
        $varb = query_totals_peras($b, $asn);
        $varc = query_totals_peras($c, $asn);
        $total = $vara + $varb + $varc;
        $pera = round($vara / $total * 100, 2);
        $perb = round($varb / $total * 100, 2);
        $perc = round($varc / $total * 100, 2);
        
    print "google.setOnLoadCallback(draw$chart);
        function draw$chart() {
        var data = google.visualization.arrayToDataTable([
            ['Topping', 'Slices'],\n['$lega - $pera%', $vara],\n['$legb - $perb%', $varb],\n['$legc - $perc%', $varc]
        ]);

        // Create and draw the visualization.
        var options = {'title':'$title','width':800,'height':600,'is3D':1};
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
                window.location.replace('http://academic.slowpoke.nl/peras.php?asn=' + label.substr(2).split(' ')[0]);
            }
        }
      }";
}

function newlinechart($validity, $validity2, $title, $chart, $as){
        
        //Dirty way to create an array with dates of last 7 days
        //Should consider using dateperiod/dateinterval instead
        $daterange = array();
        $daterange[0] = date("d-m-y", time()-604800)._routes; //7 days ago  
        $daterange[1] = date("d-m-y", time()-518400)._routes; //6 days ago
        $daterange[2] = date("d-m-y", time()-432000)._routes; //5 days ago
        $daterange[3] = date("d-m-y", time()-345600)._routes; //4 days ago
        $daterange[4] = date("d-m-y", time()-259200)._routes; //3 days ago
        $daterange[5] = date("d-m-y", time()-172800)._routes; //2 days ago      
        $daterange[6] = date("d-m-y", time()-86400)._routes;  //yesterday
        $daterange[7] = date("d-m-y")._routes;  //today

        $mysqli = mySQLi();

        $stack = array();
        foreach ($daterange as $xdate){
            $query = "SELECT COUNT(*) 
                FROM `$xdate` 
                WHERE Validity LIKE '$validity' AND ASN LIKE '$as'";
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
                    WHERE Validity LIKE '$validity2' AND ASN LIKE '$as'";
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
          ['Date','Valid', 'Invalid'],";

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

function getrelroa($prefix)
{
    $mysqli = mySQLi();
    $query = "SELECT VRP FROM `".$GLOBALS['date']."` WHERE `Prefix` = ?";

    if (!($stmt = $mysqli->prepare($query))) {
        echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }
    $stmt->bind_param("s", $prefix);
    if (!$stmt->execute()) {
         echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
    }
    $stmt->bind_result($item);
    //$a = "";
    while ($stmt->fetch())
    {
        $a = $item;
    }
    $stmt->close();
    $roas = explode(',', $a);
    $output = "<table ><tr><td>AS</td><td>Prefix</td><td>Max length</td></tr>";
    //$output = "AS\tPrefix\tMax Length\n";
    foreach ($roas as $roa)
    {
        if ($roa != ''){
            $query = "SELECT ASN, IP_Prefix, Max_Length FROM `".$GLOBALS['vrptable']."` WHERE `IP_Prefix` = ?";

            if (!($stmt = $mysqli->prepare($query))) {
                echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
            }
            $stmt->bind_param("s", $prefix);
            if (!$stmt->execute()) {
                 echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
            }
            $stmt->bind_result($x, $y, $z);
            while ($stmt->fetch())
            {
                $roaasn = $x;
                $roaprefix = $y;
                $roamaxlen = $z;
            }
            $stmt->close();
            $output .= "<tr><td>$roaasn</td><td>$roaprefix</td><td>$roamaxlen</td></tr>";
        }
    }
    $mysqli->close();
    $output .= "</table>";
    return $output;
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
        print "<col width=\"65%\"><col width=\"15%\"><col width=\"20%\">";
    print "<thead><tr><th><b>Prefix</b></th><th><b>Validity</b></th><th><b>ROAs</b></th></b></tr></thead><tbody>";
        while ($row = $result->fetch_assoc()) {
        //if (substr('_abcdef', 0, 1) === '_')
            
            if (strpos($row["Validity"],"I") !== false){
                    //$roa = getrelroa($row["Prefix"]);
                    $class = "error";
                    $validity = "<span class=\"label label-important\">Invalid</span>";
                    $button = "<div class=\"btn-group\"><button class=\"btn\" id=\"".$row['Prefix']."\" rel=\"popover\" data-content=\"\">Show ROAs</button></div>";
                    }
            elseif ($row["Validity"] == "V"){
                    //$roa = getrelroa($row["Prefix"]);
                    $class = "success";
                    $validity = "<span class=\"label label-success\">Valid</span>";
                    $button = "<div class=\"btn-group\"><button class=\"btn\" id=\"".$row['Prefix']."\" rel=\"popover\" data-content=\"\">Show ROAs</button></div>";
                    }
            else {$class = "warning";
                  $validity = "<span class=\"label label-warning\">Unknown</span>";
                  $button = " ";
                    }
            print "<tr class=\"$class\">
                    <td>".$row["Prefix"]."</td>
                    <td>$validity</td>
                    <td>$button</td>
                   </tr>";
        }
        print "</tbody></table>";
        /* free result set */
        $result->free();
        }
}

function rirtable(){
    $rirs = array('RIPE', 'LACNIC', 'ARIN', 'APNIC', 'AFRINIC');

    print "<table class=\"table table-striped table-bordered table-hover\">
    <tr>
    <td width=\"200px\"><b>RIR</b></td>
    <td><b>Total</span></b></td>
    <td><b>Valid</span></b></td>
    <td><b>Invalid</span></b></td>
    <td><b>Unknown</span></b></td>
    <td><b>RPKI Adoption Rate</span></b></td>
    </tr>";

    foreach ($rirs as &$value) {
            $total = query_totals_per_rir('%', $value);
            $valid = query_totals_per_rir('V', $value);
            $validper = round($valid/$total*100,2);

            $invalid = query_totals_per_rir('I%', $value);
            $invalidper = round($invalid/$total*100,2);

            $unknown = $total-$valid-$invalid;
            $unknownper = round($unknown/$total*100,2);

            $adop = round(($total-$unknown)/$total*100,2);

            print "<td>$value</td>
            <td><span class=\"badge badge-info\">$total (100%)</span></td>
            <td><span class=\"label label-success\">$valid ($validper%)</span></td>
            <td><span class=\"label label-important\">$invalid ($invalidper%)</span></td>
            <td><span class=\"label label-warning\">$unknown ($unknownper%)</span></td>
            <td><span class=\"label label-inverse\">$adop%</span></td>
            </tr>";
            };

    print "</table>";
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
