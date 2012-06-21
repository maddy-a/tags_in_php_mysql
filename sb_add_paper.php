<!-- "sb" prefix = "Sidebar" -->
<div class="sidebar sbAddPaper">

	<div id="optionAddPaper">
		<a href="/wp-admin/post-new.php?post_type=paper" class="button add">Add a Paper</a>
		
		<h3>NUI, HCI and UX Articles &amp; Resources</h3>
		<p>Papers is a repository of insightful articles from a variety of sources.  Topics include: analyses / applications of gestures, natural user interface examples and theory, introductions to new, innovative interaction methods and much more.</p>
			<h3>Popular Tags</h3>
			<?php
				require_once("/home/openexhibits/deploy/wp-content/oe-includes/tags/tag_handler_config.php");
				$tag_handler->PopularTags();
			?>  	

</div>
  
</div>
