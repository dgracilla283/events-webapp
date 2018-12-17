<!DOCTYPE html>
<html lang="en">
<head>	
	<meta charset="utf-8">
	<title>RCG Events</title>
	<?php include('css.php'); ?>
	<link rel="shortcut icon" href="/img/favicon.ico" />		
</head>
<body id="<?php echo $method; ?>">
<div class="navbar">
	<div class="navbar-inner">
		<div class="container-fluid">
			<a class="btn btn-navbar" data-toggle="collapse" data-target=".top-nav.nav-collapse,.sidebar-nav.nav-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</a>
			<a class="brand" href="/admin/dashboard/">RCG EVENTS CMS</a>
			<div class="btn-group pull-right" >
				<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
					<i class="icon-user"></i><span class="hidden-phone"> admin</span>
					<span class="caret"></span>
				</a>
				<ul class="dropdown-menu">					
					<li><a href="/admin/logout">Logout</a></li>
				</ul>
			</div>				
		</div>
	</div>
</div>
<div class="container-fluid">
	<div class="row-fluid">	
		<div class="span2 main-menu-span">
			<div class="well nav-collapse sidebar-nav">
				<ul class="nav nav-tabs nav-stacked main-menu">
					<li class="nav-header hidden-tablet">Main</li>
					<li<?php echo ($method == 'dashboard') ? ' class="active"' : '';?>><a class="ajax-link" href="/admin/dashboard/"><i class="icon icon-black icon-home"></i><span class="hidden-tablet">Dashboard</span></a></li>
					<li<?php echo ($method == 'add_event' || $method == 'view_event') ? ' class=active' : '';?>><a class="ajax-link" href="/admin/add_event/"><i class="icon icon-black icon-calendar"></i><span class="hidden-tablet">Add Event</span></a></li>
					<li<?php echo ($method == 'add_user') ? ' class="active"' : '';?>><a class="ajax-link" href="/admin/add_user/"><i class="icon icon-black icon-user"></i><span class="hidden-tablet">Manage Primary Attendees</span></a></li>
					<li<?php echo ($method == 'add_guest') ? ' class="active"' : '';?>><a class="ajax-link" href="/admin/add_guest/"><i class="icon icon-black icon-users"></i><span class="hidden-tablet">Manage Companions</span></a></li>
					<li<?php echo ($method == 'manage_requests') ? ' class="active"' : '';?>><a class="ajax-link" href="/admin/manage_requests/"><i class="icon icon-black icon-tag"></i><span class="hidden-tablet">Manage Requests</span></a></li>					
				</ul>
				 					
			</div>	
		</div>	