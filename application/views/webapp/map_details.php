<?php $this->load->view('webapp/includes/header') ?>
<div data-role="content">
	 	<h3 class="title" style="text-align:center;"><?php echo $map['title'] ?></h3>
	 </div>
<div data-theme="b" data-role="header" data-position="fixed" id="header-inner">
	<div class="header-inner-content">                 
		<a href="/" id="btn-home">HOME</a>
	    <a href="javascript:" onclick="history.go(-1); return false;" id="btn-back">BACK</a>
        <h3>Map Details</h3>                 
    </div>
</div> 
<div data-role="content" class="center">
    <div class="ui-bar-c ui-corner-all ui-shadow" style="padding:1em;">
   	<img src="/img/upload/map/<?php echo $map['s_fname'] ?>" alt="<?php echo $map['title'] ?>" />
	</div>  	 
</div> 	 
<?php $this->load->view('webapp/includes/footer') ?>
