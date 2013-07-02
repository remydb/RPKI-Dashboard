<?php
require ('include/functions.php');

$prefix = $_POST['p'];

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

$query = "SELECT Validity FROM `".$GLOBALS['date']."` WHERE `Prefix` = ?";

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
    if ($item == 'IA'){
    $state = "AS mismatch";}
    if ($item == 'IP'){
    $state = "Fixed length mismatch";}
    if ($item == 'IQ'){
    $state = "Range length exceeded";}
    if ($item == 'IB'){
    $state = "Range exceeded and AS mismatch";}
}
$stmt->close();
$output = "<table class=\"table table-striped table-bordered table-hover\"><thead><tr><th colspan='3'><center>ROAs</center></th></tr><tr><th>AS</th><th>Prefix</th><th>Max length</th></tr></thead><tbody>";
//$output = "AS\tPrefix\tMax Length\n";
foreach ($roas as $roa)
{
    if ($roa != ''){
        $query = "SELECT ASN, IP_Prefix, Max_Length FROM `".$GLOBALS['vrptable']."` WHERE `id` = ?";

        if (!($stmt = $mysqli->prepare($query))) {
            echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
        }
        $stmt->bind_param("s", $roa);
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
$output .= "</tbody></table><center><p><span class='label label-important'>$state</span></p></center>";
$shellprefix = escapeshellarg($prefix);
$shellcmd = shell_exec("/var/www/bin/whois -a $shellprefix | grep ^origin | awk '{print $2}' | tr -d 'AS'");
$shellout = nl2br($shellcmd);
//echo $shellcmd;
$output .= "<table class=\"table table-striped table-bordered table-hover\"><thead><tr><th><center>ASN from Whois DB</center></th></tr></thead><tbody><tr><td><center>$shellout</center></td></tr></tbody></table>";
echo $output;
?>
