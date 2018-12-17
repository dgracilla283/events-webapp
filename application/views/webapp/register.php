<?php include('includes/header.php'); ?>
     <div data-theme="b" data-role="header" data-position="fixed" id="header-inner">
    	<div class="header-inner-content">                 
	    	<a href="/" id="btn-home">HOME</a>
	        <a href="/webapp/" id="btn-back">BACK</a> 
	        <h3>Register</h3> 
	    </div>               
     </div>  
<div data-role="content">
        <form action="/webapp/login"  method="post" data-ajax="false">
                    <label for="textinput1"> Username</label>
                    <input name="username" id="textinput1" placeholder="" value="" type="text">
           			<label for="textinput3"> Password </label>
                    <input name="password" id="textinput3" placeholder="" value="" type="password">
                    <label for="textinput3"> Captcha  </label>
                    <?php  echo $cap['image'];?>
                    <input name="password" id="textinput3" placeholder="" value="" type="password">
            		<input data-inline="true" data-theme="a" value="Submit" type="submit">
        </form>
    </div>
 <?php include('includes/footer.php'); ?>