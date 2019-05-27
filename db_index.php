<?php
 error_reporting(E_ALL);
ini_set('display_errors', 1);
$dateTimeStr = date("Y-m-d_G-i-s");

  $db_database = "14705_1_backup";
 
 $mysqli = new mysqli("localhost", "root", "localhost", $db_database);
 

 
$con = mysqli_connect("localhost","root","localhost") or die("Username/Passwort falsch"); 
$anz1=$anz2=0;


/* return name of current default database */
if ($result = $mysqli->query("SELECT DATABASE()")) {
    $row = $result->fetch_row();
    printf("Default database is %s.\n", $row[0]);
    $result->close();
}


$query_select = "SELECT DATABASE()";
$mysqli->query($query_select);

$f = fopen("./dump_".$dateTimeStr.".sql", "w+");

$query_tables = "SHOW TABLES;";
$tables = $mysqli->query($query_tables);





while ($cells =$tables->fetch_array()) {
    $table = $cells[0];
    $anz1++;
    //fwrite($f,"DROP TABLE $table;");  
    $res = $mysqli->query("SHOW CREATE TABLE `$table`;");





    
   if ($res) {
        $create = $res->fetch_array();
        $create[1] .= ";";
        $line = str_replace("\n", "", $create[1]);
        fwrite($f, $line."\n");
        $data = $mysqli->query("SELECT * FROM `$table`");
        $num = $data->field_count; 
        while ($row = $data->fetch_array()){
            $anz2++;
            $line = "INSERT INTO `$table` VALUES(";
            for ($i=1;$i<=$num;$i++) {
                $line .= "'".$mysqli->real_escape_string($row[$i-1])."', "; 
            }
            $line = substr($line,0,-2);
            fwrite($f, $line.");\n");
        }
    }
}
fclose($f);
$kopie=copy("./dump_".$dateTimeStr.".sql","./dump.sql");
unlink("./dump_".$dateTimeStr.".sql");

?>
<div style="cont">
 
<div class="headline">Sicherung</div>
 
<h1>  
<?php
echo "Es wurden ".$anz2." Datensätze in ".$anz1." Tabellen gesichert.";  
if(!$kopie)echo "Kopie dump.sql konnte nicht angelegt werden.";
 
?>
</h1>


