<?php include('includes/header.php'); ?>
	 <div data-theme="b" data-role="header" data-tap-toggle="false" data-update-page-padding="false" data-position="fixed" id="header-inner">
		<div class="header-inner-content">
			<a href="/" id="btn-home" data-ajax="false">HOME</a>
			<a id="btn-back" href="/webapp/dashboard?id=<?php echo $eventId;?>" data-ajax="false">BACK</a>
			<h3>Presentations</h3>
		</div>
	 </div>
	 <div id="inner-main-navigation" class="presentation-listing">
		<ul data-role="listview" data-divider-theme="a">
			<?php if (!empty($presentationCategories)) : ?>
				<?php foreach ($presentationCategories as $presentationCategory) { ?>
					<li data-theme="c">
                <a href="/webapp/presentation_category?id=<?php echo $presentationCategory['presentationCategoryID'] . '&eventId=' . $eventId; ?>" data-transition="slide">                   	
                  
	               <div class="title"><?php echo $presentationCategory['name']; ?></div>
	               <div class="forward-button"></div>
                </a>
            </li>         
				<?php } ?>
			<?php else: ?>
					<p class="marTt15px marLt15px">No records found.</p>
			<?php endif; ?>
		</ul>
	 </div>
 <?php include('includes/footer.php'); ?>