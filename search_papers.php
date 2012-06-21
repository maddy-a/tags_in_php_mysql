<?php include("tag_search_header.php"); 
?>
	<div id="sectionContent">
		<div id="primary">
			<div id="downloadBoxes">

<div class="clearBoth">&nbsp;<!-- keeps content in container -->&nbsp;</div>

<?php
include('wp-content/themes/openexhibits/pagination/ps_pagination.php');

$pageNav;

$fFeaturedList = array();

$fName=$_GET['tag_name'];

// Original PHP code by Chirp Internet: www.chirp.com.au
function myTruncate($string, $limit, $break=".", $pad="...") {
  // return with no change if string is shorter than $limit
  if(strlen($string) <= $limit) return $string;

  $string = substr($string, 0, $limit);
  if (false !== ($breakpoint = strrpos($string, $break))) {
    $string = substr($string, 0, $breakpoint);
  }

  return $string . $pad;
}

//include("wp-content/oe-includes/inc_tag_search_list.php");

echo '<table border="0" cellspacing="0" cellpadding="0" id="paperList">
	<tbody>
';

	if(strlen($_GET['tag_name']) != 0)
	{
		$i=0;
	$dbi = parse_ini_file('db.ini');
	$con = mysql_connect('localhost', $dbi['user'], $dbi['password']);
	   mysql_select_db($dbi['database']);

	$select_query="select post_id from wp_posts_tags inner join wp_tags on wp_posts_tags.tag_id=wp_tags.id where wp_tags.tag='" . $fName . "'";
	
	$result = mysql_query($select_query);
	
	while($row = mysql_fetch_array($result))
	{
		//Listing starts 
	echo '<tr class="alternate">';
	   
	 $postid=$row['post_id'];
		$select_paper=sprintf("select post_title,guid,year(post_date) as post_year,
								monthname(post_date) as post_month,dayname(post_date) as post_dayname,day(post_date) as post_day  
								from wp_posts where id='".$postid."'",mysql_real_escape_string($postid));
		$select_post_meta=sprintf("select meta_value from wp_postmeta where post_id='".$postid."' and 
								(meta_key='author' or meta_key='category' or meta_key = 'excerpt') order by case meta_key 
								when 'category' then 1 when 'excerpt' then 2 when 'author' then 3 end",mysql_real_escape_string($postid));
								
		$result_paper = mysql_query($select_paper);
		$meta_paper = mysql_query($select_post_meta);
		
		while($row_paper = mysql_fetch_array($result_paper))
		{ 	
				
			/* meta data is caught in the array $meta_data in the order specified in the query above. 
			   	0 for category
				1 for excerpt
				2 for author
			*/
			$j=0;$meta_data=array();
			while($meta_values = mysql_fetch_array($meta_paper))
			{	
				$meta_data[$j]=$meta_values['meta_value'];
				$j++;
			}
				
		//	print_r($meta_data);
	
			//$oDate = new DateTime($row->createdate);
		//	$sDate = $oDate->format("m/d/y g:i A");
			
			echo "<td class='icon'><a href='".$row_paper['guid']."'>
			<img src='/wp-content/themes/openexhibits/images/papersIcons/icon_papers_pdf.png' title='' border='0'>
			</a></td><td><div class='category'>".$meta_data['0']."</div><div class='head'>";
			echo "<h4><a href='".$row_paper['guid']."'> ".$row_paper['post_title']."</a></h4>";
			echo "<h5>POSTED:".strtoupper($row_paper['post_dayname']).',&nbsp'.strtoupper($row_paper['post_month']).'&nbsp'
					.$row_paper['post_day'].',&nbsp&nbsp'.$row_paper['post_year']."</h5>";
			echo "<p>".$meta_data['1']."</p></div></div>";
			echo "<ul class='navDownloadBox'>
					<li class='left'><a href='".$row_paper['guid']."' class='button download'>Download</a></li>
					<li class='right'><a href='".$row_paper['guid']."' class='button moreInfo'>More Info</a></li>
					</ul></td>";
		//	echo "<h5><a href='".$row_paper['guid']."'>Click here To be Redirected to the Paper</a>";echo "</li><br/><br/>";
		}
		echo "</tr>";
	}
	
	mysql_close($con);
		
	}
	

echo '</tbody></table>';


?>


  </div><!-- /#downloadBoxes -->
</div><!-- /#primary -->

</div><!-- /#sectionContent -->
	<div id="secondary" class="widget-area" role="complementary">
		<!-- sidebar - add a paper -->

		<div class="sidebar sbAddPaper">
			
			<div id="optionAddPaper">				
				<a href="/wp-admin/post-new.php?post_type=paper" class="button add">Add a Paper</a>

				<h3>NUI, HCI and UX Articles &amp; Resources</h3>
				<p>Papers is a repository of insightful articles from a variety of sources.  Topics include: analyses / applications of gestures, natural user interface examples and theory, introductions to new, innovative interaction methods and much more.</p>
				
		  	</div>
			
				<h3>Popular Tags</h3>
				<?php
					require_once("/home/openexhibits/deploy/wp-content/oe-includes/tags/tag_handler_config.php");
					$tag_handler->PopularTags();
				?>
			
		</div>
		
	</div><!-- /#secondary -->

    <div class="clearBoth">&nbsp;<!-- keeps content in container -->&nbsp;</div>
    
  </div><!-- #content -->
  
</div><!-- #container -->
</div>
</div>
	<!-- begin footer include -->
<?php //get_sidebar(); ?>
<?php include("tag_search_footer.php"); 
?>
