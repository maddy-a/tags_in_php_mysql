<?PHP
/* This is the configuration file that initializes the database connection to handle the tags.The information about the database has to be entered here*/

require_once("tag_handler.php");
require_once("formvalidator.php");
$tag_handler= new Taghandler();
$tag_handler->InitDB(/*hostname*/'localhost',
                      /*username*/'openexhibits',
                      /*password*/'lo8ca1OKCU9NTQNX9TOGq4',
                      /*database name*/'openexhibits');
					$username="openexhibits";
					$password="lo8ca1OKCU9NTQNX9TOGq4";
					$database="openexhibits";
					mysql_connect(localhost,$username,$password);
					@mysql_select_db($database) or die( "Unable to select database");
//$tag_handler->SetRandomKey('qSRcVS6DrTzrPvr');
?>
