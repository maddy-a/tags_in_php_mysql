<?php 

	$q=$_GET['q'];

	if (strlen($q)>=2){
    $dbi = parse_ini_file('../themes/openexhibits/db.ini');
    $con = mysql_connect('localhost', $dbi['user'], $dbi['password']);
    mysql_select_db($dbi['database']);
	$sql = "select tag from wp_tags where tag like '%".$q."%'";
	$result = mysql_query($sql);
    while($row = mysql_fetch_array($result))
    {
	    echo $row['tag'];
    }
    mysql_close($con);
	}

?>
