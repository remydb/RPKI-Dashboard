<?php
require ('include/functions.php');

$query = $_POST['q'];
$limit = $_POST['limit'];
//$query = '106';
//$limit = 8;

echo "[";
echo getASlistjson($query, $limit);
echo "]";
?>