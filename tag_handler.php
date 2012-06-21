<?PHP

/* This is the place hwere all the functions related to tags are handled! Search entering new tags and also retrieving new tags can be done here. Only logged in users can add new tags. 
*/
require_once("formvalidator.php");

class Taghandler{
	var $username;
    var $pwd;
    var $database;
    var $connection;
    var $rand_key;

// Initialize Database context



function InitDB($host,$uname,$pwd,$database)
	{
    	$this->db_host  = $host;          
		$this->username = $uname;         
		$this->pwd  = $pwd;              
		$this->database  = $database;    
    
	}
	function RedirectToURL($url)
    {
        header("Location: $url");
        exit;
    }

/*  The main operation would be adding a tag, removing a tag and search papers by tag. Also displaying tags belonging to a paper */  
function Addtag()
{
	 if(!isset($_POST['submitted']))
        {
           return false;
        }

	if (!is_user_logged_in() )
	{
		return false;
	}

	if(empty($_POST['tag']))
     {
            $this->HandleError("Empty Tag!");
            return false;
      }
	$tag = trim($_POST['tag']);
	$postid = (int)trim($_POST['post_id']);
	$userid = (int)trim($_POST['user_id']);
	if(!$this->AddTagToDatabase($tag,$postid,$userid))
     {
        return false;
     }
	

	return true;
}

/* Function whch displays and handles the text box that enters the tags */

function EnterTags(&$x,&$user_id)
{   
	$ret = "<div id=\"optionPapersTagForm\" style='float:left;'>
		<h4>Enter New Tags for this Paper:</h4>
		
		<form action='' method='post' accept-charset='UTF-8' name='tags'>
			<label for=\"tagForm\">Tag(s):</label>
		
			<input type='hidden' name='submitted' id='submitted' value='1'/>
			<input type='hidden' name='post_id' value='" . $x . "' />
			<input type='hidden' name='user_id' value='" . $user_id . "' />
			<input type='text' id='mytags' class='textField' name='tag' onkeyup='showResult(this.value)' />
			<div id='livesearch'></div>
			<input type='submit' name='Submit' value='Add Tag' class=\"button\" />
			<span id='tags_tag_errorloc' class='error'></span>
		</form>
	</div>  ";
		
		echo $ret;
}
// This function is used to display the tags when user clicks on the papers section
function DisplayTags(&$x,&$user_id)
{
	if(!$this->DBLogin())
 {
      $this->HandleError("Database login failed!");
      return false;
    }

	$this->getTags($x,$user_id);
}
// This function gets the tags that belong to a particular post from DB
function getTags(&$x,&$user_id)
{	
	$i=0;$j=0;

	$tag_select_query = "Select tag_id,tag from wp_tags 
							inner join wp_posts_tags on wp_posts_tags.tag_id=wp_tags.id 
							where wp_posts_tags.post_id='". $x . "'";
	
	
	if(!mysql_query($tag_select_query))
	{
		echo "<li>Could not Get Tags from Database. Contact Admin!</li>";
	}
	else
	{
	$result = mysql_query($tag_select_query);
	$num=mysql_num_rows($result);
	if($num){
	echo "<div id=\"optionPaperTags\"><h4>Tags:</h4>";
	echo "<ul class='tagit ui-widget'>";
		while ($i < $num) {
		$tag=mysql_result($result,$i,"tag");
		$id=mysql_result($result,$i,"tag_id");
		echo "<li class='tagit-choice ui-widget-content '>
			  <a href='/wp-content/themes/openexhibits/search_papers.php?tag_name=$tag'>".$tag."</a>";
			if(is_admin() || ($this->Is_User_Tag($id,$user_id,$x))){
			
			echo "<form action='' method='post' id='delete_tag' accept-charset='UTF-8'>
				<input type='hidden' name='delete' id='submitted' value='1'/>
				<input type='hidden' name='tagid' id='submitted' value='$id'/>
				<input type='hidden' name='userid' id='submitted' value='$user_id'/>
				<input type='hidden' name='postid' id='submitted' value='$x'/>
				<input type='submit' name='Submit'  class='delete_tag_button' value='x' onClick=\"return confirm('Are you sure you want to delete the tag?');\"/></form>";}
				echo "</li>";	
				$i++;
		}}
		else{
			echo "";
		}	
	}
	
	echo "</ul></div>";
}

// This function checks if the tags belong to a particular user. SO user can delete only those particular tags. 

function Is_User_Tag(&$id,&$user_id,&$x)
{
	if(!$this->DBLogin())
 {
      $this->HandleError("Database login failed!");
      return false;
    }
	$user_select_query= "select id from wp_users_tags where tag_id='".$id."' and user_id='".$user_id."' and post_id='".$x."'" ;
					if(!mysql_query($user_select_query))
					{
						echo "<li>Contact Admin</li>";
					}
					else
					{
						$result = mysql_query($user_select_query,$this->connection);  
						if($result && mysql_num_rows($result) > 0)
					    {
					        return true;
					    }
						else{
							return false;
						}
					}
	
}

function Deletetag()
{
	if(!$this->DBLogin())
 {
      $this->HandleError("Database login failed!");
      return false;
    }

	if(!isset($_POST['delete']))
    {
       return false;
    }

	$tagid=$_POST['tagid'];
	$userid=$_POST['userid'];
	$postid=$_POST['postid'];
// Seperate function so only valid users will delete the tags.

if(!$this->DeleteTagfromPosts($tagid,$postid))
{
	echo "Reference not deleted from Post";
	return false;
}

if(!$this->DeleteTagfromUsers($tagid,$userid,$postid))
{
	echo "Reference not deleted from user";
	return false;
}
return true;
}

function DeleteTagfromPosts(&$tagid,&$postid)
{
	if(!is_user_logged_in())
	{
		return false;
	}
	$post_tag_delete="delete from wp_posts_tags where post_id='$postid' and tag_id='$tagid'";
	if(!mysql_query($post_tag_delete))
	{
		return false;
	}
	return true;
}

function DeleteTagFromUsers(&$tagid,&$userid,&$postid)
{
	if(!is_user_logged_in())
	{
		return false;
	}
	
	$user_tag_delete="delete from wp_users_tags where user_id='$userid' and tag_id='$tagid' and post_id='$postid'";
	if(!mysql_query($user_tag_delete))
	{
		return false;
	}
	return true;	
}


// Finding Popular tags
function PopularTags()
{		
	if(!$this->DBLogin())
    {
        $this->HandleError("Database login failed!");
        return false;
    }
	if(!$this->Popularity());
	{
		return false;
	}
	return true;
}

function Popularity()
{
	$i=0;
	$count_popularity_query="select tag,tag_id,count(post_id) as popularity from wp_posts_tags inner join wp_tags on wp_tags.id=wp_posts_tags.tag_id group by tag_id order by count(post_id) desc limit 10";
	if(!mysql_query( $count_popularity_query ,$this->connection))
    {
	echo "Something went wrong while checking for popular tags";
	return false;
	}
	else{
		$result = mysql_query($count_popularity_query);
		$num=mysql_num_rows($result);
		echo "<ul class='tagit ui-widget'>";
			while ($i < $num) {
			$tag=mysql_result($result,$i,"tag");
			$p=mysql_result($result,$i,"popularity");
			echo "<br/><li class='tagit-choice ui-widget-content' style='list-style:none;'>
				  <a href='/wp-content/themes/openexhibits/search_papers.php?tag_name=$tag'>".$tag."</a> x <b>".$p."</b></li>";$i++;
			}
			echo "</ul>";
	return true;	
	}
		return true;	
}

// End of Finding Popular tags

//  Start of adding tags

function AddTagToDatabase(&$tag,&$postid,&$userid)
{
 
 
    if(!$this->IsTagUniqueInPost($tag,$postid))
    {
        $this->HandleError("This tag is already registered");
        return false;
    }
    
    
    if(!$this->InsertTagIntoDB($tag,$postid,$userid))
    {
        $this->HandleError("Inserting to Database failed!");
        return false;
    }
    return true;
}

    

function IsTagUniqueInPost(&$tag,&$postid)
{
    $field_val = $this->SanitizeForSQL($tag);
    $qry = "select tag from wp_tags inner join wp_posts_tags on wp_posts_tags.tag_id=wp_tags.id where tag='".$field_val."' and wp_posts_tags.post_id='".$postid."'";
    $result = mysql_query($qry,$this->connection);   
    if($result && mysql_num_rows($result) > 0)
    {
        return false;
    }
    return true;
}

function IsTagUnique(&$tag)
{
    $field_val = $this->SanitizeForSQL($tag);
    $qry = "select tag from wp_tags where tag='".$field_val."'";
    $result = mysql_query($qry,$this->connection);   
    if($result && mysql_num_rows($result) > 0)
    {
        return false;
    }
    return true;
}

function InsertTagIntoDB(&$tag,&$postid,&$userid)
{   
	$field_val1 = $this->SanitizeForSQL($tag);
	
	$field_val2 =$this->SanitizeForSQL($postid);
	$user =$this->SanitizeForSQL($userid);
	if($this->IstagUnique($tag))
	{
		$insert_query1 = "insert into wp_tags(tag)values('".$field_val1."')";

		if(mysql_query( $insert_query1 ,$this->connection))
	    {
	        $select_query = "Select id from wp_tags where tag='".$field_val1."'";
			$result = mysql_query($select_query);
			$tagid=mysql_result($result,0,"id");
		    $insert_query2= "insert into wp_posts_tags(post_id,tag_id)values('".$field_val2."','".$tagid."')";
			$insert_query3= "insert into wp_users_tags(user_id,tag_id,post_id)values('".$user."','".$tagid."','".$field_val2."')";
	        if(!mysql_query( $insert_query2 ,$this->connection) || !mysql_query( $insert_query3 ,$this->connection))
				{
					echo "DB insertion error";
					return false;
				}
				else{
					return true;
				}
	    }
	else{
	    return false;
	}
	}
	else
	{
		$select_query = "Select id from wp_tags where tag='".$field_val1."'";
		$result = mysql_query($select_query);
		$tagid=mysql_result($result,0,"id");
	    $insert_query2= "insert into wp_posts_tags(post_id,tag_id)values('".$field_val2."','".$tagid."')";
		$insert_query3= "insert into wp_users_tags(user_id,tag_id,post_id)values('".$user."','".$tagid."','".$field_val2."')";
        if(!mysql_query( $insert_query2 ,$this->connection)  || !mysql_query( $insert_query3 ,$this->connection))
			{
				echo "DB insertion error";
				return false;
			}
			
		return true;
	}

    
}

//  Start of adding tags


   //-------Private Helper functions-----------
function DBLogin()
{

    $this->connection = mysql_connect($this->db_host,$this->username,$this->pwd);

    if(!$this->connection)
    {   
        $this->HandleDBError("Database Login failed! Please make sure that the DB login credentials provided are correct");
        return false;
    }
    if(!mysql_select_db($this->database, $this->connection))
    {
        $this->HandleDBError('Failed to select database: '.$this->database.' Please make sure that the database name provided is correct');
        return false;
    }
    if(!mysql_query("SET NAMES 'UTF8'",$this->connection))
    {
        $this->HandleDBError('Error setting utf8 encoding');
        return false;
    }
    return true;
}
function GetErrorMessage()
   {
       if(empty($this->error_message))
       {
           return '';
       }
       $errormsg = nl2br(htmlentities($this->error_message));
       return $errormsg;
   }    

  function HandleError($err)
    {
        $this->error_message .= $err."\r\n";
    }

 /*
    Sanitize() function removes any potential threat from the
    data submitted. Prevents email injections or any other hacker attempts.
    if $remove_nl is true, newline chracters are removed from the input.
    */
  function SanitizeForSQL($str)
    {
        if( function_exists( "mysql_real_escape_string" ) )
        {
              $ret_str = mysql_real_escape_string( $str );
        }
        else
        {
              $ret_str = addslashes( $str );
        }
        return $ret_str;
    }


    function Sanitize($str,$remove_nl=true)
    {
        $str = $this->StripSlashes($str);

        if($remove_nl)
        {
            $injections = array('/(\n+)/i',
                '/(\r+)/i',
                '/(\t+)/i',
                '/(%0A+)/i',
                '/(%0D+)/i',
                '/(%08+)/i',
                '/(%09+)/i'
                );
            $str = preg_replace($injections,'',$str);
        }

        return $str;
    }    
    function StripSlashes($str)
    {
        if(get_magic_quotes_gpc())
        {
            $str = stripslashes($str);
        }
        return $str;
    }

}
?>