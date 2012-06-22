<?PHP
require_once("./include/member_handler.php");
//require_once("./include/projectmanagement.php");
$fgmembersite = new FGMembersite();
//$projectmgmt= new Projectmanagement();

$fgmembersite->SetWebsiteName('http://myphpforum.herokuapp.com/');

$fgmembersite->SetAdminEmail('275.madhu@gmail.com');

$url=parse_url(getenv("CLEARDB_DATABASE_URL"));

$fgmembersite->InitDB(/*hostname*/$url["host"],
                      /*username*/$url["user"],
                      /*password*/$url["pass"],
                      /*database name*/substr($url["path"]));
					mysql_connect(
					            $server = $url["host"],
					            $username = $url["user"],
					            $password = $url["pass"]);
					            $db=substr($url["path"],1);

					 mysql_select_db($db);
			
$fgmembersite->SetRandomKey('qSRcVS6DrTzrPvr');


?>