<?php include('includes/header.php'); ?>
	 <div data-theme="b" data-role="header" data-tap-toggle="false" data-update-page-padding="false" data-position="fixed" id="header-inner">
		<div class="header-inner-content">
			<a href="/" id="btn-home" data-ajax="false">HOME</a>
			<a id="btn-back" href="/webapp/presentations?id=<?php echo $eventId;?>" data-ajax="false">BACK</a>
			<h3><?php echo $presentationCategories[0]['name']; ?></h3>
		</div>
	 </div>
	 <div id="inner-main-navigation" class="presentation-listing-main">
		<ul data-role="listview" data-divider-theme="a">
			<?php if (!empty($presentations)) : ?>
				<?php foreach ($presentations as $presentation) { ?>
					<li data-theme="c">
               <a href="<?php echo prep_url($presentation['url']); ?>" target="_blank" data-ajax="false">
	               <div class="title"><?php echo $presentation['title']; ?></div>
                </a>
            </li>
				<?php } ?>
			<?php else: ?>
					<p class="marTt15px marLt15px">No records found.</p>
			<?php endif; ?>
		</ul>
	 </div>
 <?php include('includes/footer.php'); ?>