$(document).bind('mobileinit', function() {
	$.mobile.ajaxEnabled = false;
	$.mobile.allowCrossDomainPages = true;
    $.mobile.zoom.enabled = false;
    $.mobile.buttonMarkup.hoverDelay = 0; //defaults 200
    $.mobile.defaultDialogTransition = 'none';
    $.mobile.defaultPageTransition = 'none';
});
$(function(){
	var $body = $('body'),
		$frmForgotPassword = $('form.form-forgot-password'),
		$frmLogin = $('#form-login');

	$('body').on('click','a', function(){
		//except for popup
		if($(this).attr('data-rel') !== 'popup' &&
			$(this).attr('id') !== 'btn-form-add-companion'
		) {
			$.mobile.showPageLoadingMsg();
		}
	});


	if($('#map').length && APP.eventLocation) {

		var geocoder, map;
	    var  initialize = function() {
	        geocoder = new google.maps.Geocoder();
	        var latlng = new google.maps.LatLng(0, 0);
	        var mapOptions = {
	          zoom: 17,
	          center: latlng,
	          mapTypeId: google.maps.MapTypeId.ROADMAP
	        };
	        map = new google.maps.Map(document.getElementById('map'), mapOptions);
	    };
	    initialize();
	    geocoder.geocode( { 'address': APP.eventLocation}, function(results, status) {
	          if (status == google.maps.GeocoderStatus.OK) {
	        	  var eventCoordsLatLng = results[0].geometry.location;
	        	  if(APP.eventCoordsLat && APP.eventCoordsLng) {
	        		eventCoordsLatLng = new google.maps.LatLng(APP.eventCoordsLat, APP.eventCoordsLng);
	        	  }
	        	  map.setCenter(eventCoordsLatLng);
	        	  var marker = new google.maps.Marker({
	                map: map,
	                position: map.getCenter()
	        	  });
	          } else {
	            //alert('Geocode was not successful for the following reason: ' + status);
	        	$('#map').html('Unable to find location in Google Maps');
	          }
	          google.maps.event.addListener(marker, "click", function() {
	        	  infowindow.open(map,marker);
	          });
	          var infowindow = new google.maps.InfoWindow({
	        	  content:"<p>"+APP.eventTitle+"<br>"+APP.eventLocation+"</p>"
	          });
	         infowindow.open(map,marker);
	    });
	}

	var validateFormForgotPassword = function(form) {
		form.validate({
			rules: {
				email: {
					required: true,
		         	email: true
				}
			},
			messages: {
		         email: {
		         	required: 'Email is required.',
		         	email: 'Invalid email address.',
		         	remote: 'Email already exists.'
		         }
		    },
		    focusInvalid: false,
		    onkeyup: false
		});
		$email = form.find('input[name=email]');
		$userid = form.find('#user-id');

		if(!$userid.val() && $email.length) {
			$email.rules('add', {
				remote: {
	         		url: "/webapp/validate_email",
	         		type: "get"
	         	}
			});
		}
	};
	validateFormForgotPassword($frmForgotPassword);

	//Add custom form rules here
	$.validator.addMethod('alphanumeric', function(value, element) {
		return this.optional(element) || /^[a-z0-9\-_ ]+$/i.test(value);
	}, 'Only alphanumeric, dash, underscore characters are allowed');

	$.validator.addMethod('emailRestrictDomain', function(value, element) {
		return this.optional(element) || value.indexOf('@rcggs.com', email.length - '@rcggs.com'.length) !== -1;
	}, 'Email must be a valid RCG e-mail address (your.name@rcggs.com).');


	//Account update validate
	$('form#user_account_update').validate({
		rules: {
			email : 'required',
			first_name : {
				'alphanumeric' 	: true,
				'required'		: true
			},
			last_name : {
				'alphanumeric'	: true,
				'required'		: true
			},
			affiliation : {
				'alphanumeric'	: true
			},
			industry : {
				'alphanumeric'	: true
			},
			title : {
				'alphanumeric'	: true
			},
			new_password: {
				minlength: 6
			}
		}
	});

	$('#form-login').submit(function(){
		$('label.error').remove();
		if(!$(this).find('input[name=username]').val() || !$(this).find('input[name=password]').val()) {
			$(this).prepend('<label class="error">Please enter username/password</label>');
			return false;
		}
	});
	//User Registration
	$('form#user_registration').validate({
		rules: {
			email :  {
				'email' 	: true,
				'required'		: true,
				'emailRestrictDomain': true
			},
			first_name : {
				'alphanumeric' 	: true,
				'required'		: true
			},
			last_name : {
				'alphanumeric'	: true,
				'required'		: true
			},
			affiliation : {
				'alphanumeric'	: true
			},
			industry : {
				'alphanumeric'	: true
			},
			title : {
				'alphanumeric'	: true
			},
			password: {
				minlength: 6,
				'required'		: true
			},
			password_confirm :  {
				minlength: 6,
				equalTo: "#password"
			}
		},
		messages: {
			email: {
				remote: 'Email already exists.'
			}
		}
	});

	$email = $('form#user_registration').find('input[name=email]');

	if($email.length) {
		$email.rules('add', {
			remote: {
         		url: "/webapp/validate_email",
         		type: "get"
         	}
		});
	}

	//Forgot password update validate
	$('form#forgot_password').validate({
		rules: {
			email : 'required'
		},
		messages : {
			email : {
				required: 'E-mail is required to reset the password'
			}
		},
		errorPlacement: function(error, element) {
			$('.error-container').html(error);
		}

	});

	//-- User joins or cancels an event
	$('#events-list').find('ul').find('li').on('click', 'span', function(e){
		e.preventDefault();
		var url = $(this).attr('data-url');
		$.ajax({
			method : 'GET',
			url : url,
			success : function(data) {
				//-- refreshes page
				$.mobile.changePage(
					window.location.href, {
				      allowSamePageTransition : true,
				      transition              : 'none',
				      showLoadMsg             : false,
				      reloadPage              : true
				 });
			}
		});
	});

	$('#btn-form-join-event').click(function() {
		$(this).parents('form').submit();
	});

	$('form#form-join-event').on('submit', function(e) {

		e.preventDefault();
		$('#btn-form-join-event').unbind('click');
		var redirectUrl = $(this).attr('data-redirect');
		$.ajax({
			type : 'POST',
			url : '/webapp/join',
			data : $(this).serialize(),
			success : function(data) {
				//-- refreshes page
				window.location = redirectUrl;
			}
		});
	});

	$('#btn-form-cancel-join-event').click(function() {
		$(this).parents('form').submit();
	});
	$('form#form-cancel-join-event').on('submit', function(e) {
		e.preventDefault();

		var redirectUrl = $(this).attr('data-redirect');
		$.ajax({
			type : 'POST',
			url : '/webapp/cancel_join',
			data : $(this).serialize(),
			success : function(data) {
				//-- refreshes page
				window.location = redirectUrl;
			}
		});
	});

	//-- Add companion
	$('#btn-form-add-companion').click(function() {
		$(this).parents('form').submit();
	});

	$('form#form-add-companion').on('submit', function(e) {
		e.preventDefault();
		var redirectUrl = $(this).attr('data-redirect');

		if($(this).find('input[name="first_name"]').val() === '' ||
				$(this).find('input[name="last_name"]').val() === ''
		) {


			$(this).find('.error').html('Fields cannot be empty');
		} else {
			$(this).find('.error').html('');
			$.ajax({
				type: 'POST',
				url: '/webapp/add_companion',
				data: $(this).serialize(),
				success : function(data) {
					window.location = redirectUrl;
				}
			});
		}
	});


	$('input#primary-user-checkbox').click(function(e){
		if($(this).is(':checked')) {
			//check also other boxes
			$('input[name="companions[]"]').attr('checked', true).checkboxradio('refresh');;
		}
	});

	$('.cbxCompanions').click(function(e){
		if($(this).is(':not(:checked)')) {
			//check also other boxes
			$('input#primary-user-checkbox').attr('checked', false).checkboxradio('refresh');;
		}
	});

	$body.on('click','a.btn-join-activity', function(){
		var $btn = $(this),
			id = parseInt($btn.attr('data-id')),
			$refType = $btn.attr('data-reftype'),
			$container = $btn.parents('div.join-activity'),
			userID = parseInt($btn.attr('data-userid'));
		$.ajax({
		   url:'/webapp/request_join',
		   data: 'id='+id+'&uid='+userID+'&eid='+APP.eventID+'&rtype='+$refType+'&puid='+APP.userID,
		   type: 'GET',
		   success: function(data) {
			   //$container.html(data);
			   jQuery.mobile.changePage(window.location.href, {
			        allowSamePageTransition: true,
			        transition: 'none',
			        reloadPage: true
			    });
		   }
		});
	});

	$body.on('click','a.btn-cancel-join', function(){
		var $btn = $(this),
			id = parseInt($btn.attr('data-id')),
			$container = $btn.parents('div.join-activity');
		if(confirm('Are you sure you want to cancel your request? ')){
			$.ajax({
			   url:'/webapp/cancel_request_join',
			   data: 'id='+id+'&puid='+APP.userID,
			   type: 'GET',
			   success: function(data) {
				   $container.html(data);
			   }
			});
		}
	});

	//-- Speaker accepts or declines an event
	$('#speaker-schedule-list, .speaker-schedule-list').find('ul').find('li').on('click', 'a.btn-accept', function(e){
		e.preventDefault();
		var url = $(this).attr('href');
			$.ajax({
				method : 'GET',
				url : url,
				success : function(data) {
					//-- refreshes page
					// $.mobile.changePage(
					// 	window.location.href, {
					//       allowSamePageTransition : true,
					//       transition              : 'none',
					//       showLoadMsg             : false,
					//       reloadPage              : true
					//  });
					location.reload();
				}
			});
	});

	$('#speaker-schedule-list, .speaker-schedule-list').find('ul').find('li').on('click', 'a.btn-decline', function(e){
		e.preventDefault();
		var url = $(this).attr('href');
		var comment = prompt("Are you sure you want to cancel your request?\nPlease enter your reason in the textbox below.", "");
		if (comment){
			$.ajax({
				method : 'GET',
				url : url + '&comment='+comment,
				success : function(data) {
					//-- refreshes page
					// $.mobile.changePage(
					// 	window.location.href, {
					//       allowSamePageTransition : true,
					//       transition              : 'none',
					//       showLoadMsg             : false,
					//       reloadPage              : true
					//  });
					location.reload();
				}
			});
		} else {
			$('.ui-loader').hide();
		}
	});

	$('.presentation-listing-main a').click(function() {
		setTimeout(function(){$('.ui-loader').hide();},200);
	});

	$('#download-link, #btnEventDetailsCancel').on('click', function() {
		$('.ui-loader').hide();
	});
});