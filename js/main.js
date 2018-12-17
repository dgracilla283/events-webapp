var teamSpeakerExists   = false;
var attendeesIsChanged  = false;
var eventOwnerIsChanged = false;

function validateFormEventStep2(form) {
	form.validate({
		rules: {
			title: {
				required: true,
				validName: true
			},
			location: {
				required: true,
				validName: true
			},
			start_date_time: {
				required: true,
				validDate: true,
				dateVsCurrentDate: true
			},
			start_time: {
				required: true
			},
			end_date_time: {
				required: true,
				endDate: true,
				validDate: true,
				dateVsCurrentDate: true
			},
			end_time: {
				required: true,
				endTime: true
			}
		},
		messages: {
			 title: {
				 required: "Event title is required."
			 },
			 location: {
				 required: "Event location is required."
			 },
			 start_date_time: {
				 required: "Event start date is required."
			 },
			 start_time: {
				 required: "Event start time is required."
			 },
			 end_date_time: {
				 required: 'Event end date is required.'
			 },
			 end_time: {
				 required: "Event end time is required."
			 }
		},
		errorPlacement: function(error, element) {
			var name = $(element).attr('name');
			error.appendTo( element.parents('div.controls'));
			if ($('.itinerary_date').parent().next().hasClass('error')) {
				$('.itinerary_date').parent().next().remove();
			}
		},
		focusInvalid: false,
		onkeyup: false
	});

	$(document).on('click', '#btn-confirm-event-duplicate', function(e) {
		$('.itinerary_date').each(function(){
			$(this).rules('add', {
				itineraryDateStart: true,
				itineraryVsCurrentDate: true
			});
		})
	})
}

function validateFormEvent(form) {
	form.validate({
		rules: {
			title: {
				required: true,
				validName: true
			},
			location: {
				required: true,
				validName: true
			},
			start_date_time: {
				required: true,
				validDate: true,
				dateVsCurrentDate: true
			},
			start_time: {
				required: true
			},
			end_date_time: {
				required: true,
				endDate: true,
				validDate: true,
				dateVsCurrentDate: true
			},
			end_time: {
				required: true,
				endTime: true
			}
		},
		messages: {
			 title: {
				 required: "Event title is required."
			 },
			 location: {
				 required: "Event location is required."
			 },
			 start_date_time: {
				 required: "Event start date is required."
			 },
			 start_time: {
				 required: "Event start time is required."
			 },
			 end_date_time: {
				 required: 'Event end date is required.'
			 },
			 end_time: {
				 required: "Event end time is required."
			 }
		},
		errorPlacement: function(error, element) {
			var name = $(element).attr('name');
			error.appendTo( element.parents('div.controls'));
		},
		focusInvalid: false,
		onkeyup: false
	});
}

$(function(){
	var $body = $('body'),
		dateObj = new Date(),
		loader = $('<span>').addClass('loader'),
		$frmEvent = $('#form-event'),
		$frmUser = $('form.form-user'),
		$frmGuestUser = $('form.form-guest-user'),
		$frmItinerary = $('form.form-itinerary'),
		$frmAddEditGuest = $('#form-add-edit-guest'),
		$frmAddEditBreakout = $('#form-add-edit-breakout'),
		$frmPresentation = $('form#form-presentation'),
		$duplicate_event_form = $('form#duplicate_event_form');

	//form validation
	var errorMessage = '',
		getErrorMessage = function() {
			return errorMessage;
		};
	var fullDate = new Date();
	var twoDigitMonth = ((fullDate.getMonth().length+1) === 1)? (fullDate.getMonth()+1) : '0' + (fullDate.getMonth()+1);
 	var currentDate =  twoDigitMonth + "/" + fullDate.getDate() + "/" + fullDate.getFullYear();

	$.validator.addMethod("dateVsCurrentDate", function(value, element) {
		var objDate1 = new Date(currentDate);
			objDate2 = new Date(value.replace(/-/g,"/"));

	   return objDate2.getTime() >= objDate1.getTime();
	},'Date should be later than or equal to current date.');

	$.validator.addMethod("itineraryVsCurrentDate", function(value, element) {
		var objDate1 = new Date(currentDate);
			objDate2 = new Date(value.replace(/-/g,"/"));

	   return objDate2.getTime() >= objDate1.getTime();
	},'Itinerary Date should be later than or equal to current date.');

	$.validator.addMethod("itineraryDateStart", function(value, element) {
		var $parentForm = $(element).parents('form'),
			$startDateTime = $parentForm.find('input[name=start_date_time]');
			objDate1 = new Date($startDateTime.val().replace(/-/g,"/")),
			objDate2 = new Date(value.replace(/-/g,"/"));

	   return objDate2.getTime() >= objDate1.getTime();
	},'Itinerary Date should be later than or equal to start date.');

	$.validator.addMethod("endDate", function(value, element) {
		var $parentForm = $(element).parents('form'),
			$startDateTime = $parentForm.find('input[name=start_date_time]');
			objDate1 = new Date($startDateTime.val().replace(/-/g,"/")),
			objDate2 = new Date(value.replace(/-/g,"/"));

	   return objDate2.getTime() >= objDate1.getTime();
	},'End date should be later than or equal to start date.');

	$.validator.addMethod("endTime", function(value, element) {
		var $parentForm = $(element).parents('form'),
			startTime = $parentForm.find('input[name=start_time]').val() || '12:00 AM',
			endDateTimeValue = $parentForm.find('input[name=end_date_time]').val(),
			startDateValue = $parentForm.find('input[name=start_date_time]').val(),
			objDate1 = new Date(startDateValue.replace(/-/g,"/") +' '+  startTime),
			objDate2 = new Date(endDateTimeValue.replace(/-/g,"/") +' '+  value);

	   return objDate2.getTime() > objDate1.getTime();
	},'End time should be later than start time.');

	$.validator.addMethod("validDate", function(value, element) {
		var date = value.replace(/-/g,"/");
		return  isDate(date);
	},'Invalid date.');

	$.validator.addMethod("validEventDate", function(value, element) {
		var $parentForm = $(element).parents('form'),
			endDateTimeValue = $parentForm.find('input[name=end_date_time]').val(),
			validTime = true, timeEntered = new Date(value),
			objDate1 = new Date(CMS.eventEndDateTime),
			objDate2 = new Date(endDateTimeValue.replace(/-/g,"/") +' '+  value);
		return objDate2.getTime() <= objDate1.getTime();
	},'Time should not be later than event time end.');


	$.validator.addMethod("validEventStartDate", function(value, element) {
		var $parentForm = $(element).parents('form'),
			startDateTimeValue = $parentForm.find('input[name=start_date_time]').val();
			objDate1 = new Date(CMS.eventStartDateTime),
			objDate2 = new Date(startDateTimeValue.replace(/-/g,"/") +' '+  value);

		return objDate2.getTime() >= objDate1.getTime();
	},'Time should be later than event time start.');


	$.validator.addMethod("validName", function(value, element) {
		var alphaNumericOnly = /^[A-Za-z0-9\s&(){}|;:'",.<>\/?\\-]+$/,
			isValid = true;
		if(!value)
			return isValid;
		if(value.length == 1){
			isValid = false;
			errorMessage = 'Please enter more than 1 characters.';
		}
		if (!alphaNumericOnly.test(value)){
			errorMessage = 'Please enter alpha numeric characters only.';
			isValid = false;
		}
		return true;
	}, getErrorMessage);

	$.validator.addMethod('firstCharAlphaOnly', function(value, element) {
		return !$.isNumeric(value[0]);
	}, 'First character should not be numeric.');


	var validateFormUser = function(form) {
		form.validate({
			rules: {
				email: {
					required: true,
					email: true
				},
				first_name: {
					required: true,
					validName: true,
					firstCharAlphaOnly: true
				},
				last_name: {
					required: true,
					validName: true,
					firstCharAlphaOnly: true
				},
				password: {
					required: true
				},
				password2: {
					required: true,
					equalTo: "#password1"
				},
				affiliation: {
					validName: true
				},
				title: {
					validName: true
				},
				industry: {
					validName: true
				}
			},
			messages: {
				 email: {
					required: 'Email is required.',
					email: 'Invalid email address.',
					remote: 'Email already exists.'
				 },
				 first_name: {required: "First name is required."},
				 last_name: {required: "Last name is required."},
				 password: {required: "Password is required."},
				 password2: {
					required: "Re-type Password is required.",
					equalTo: "Password fields have to match."
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
					url: "/admin/validate_email",
					type: "get"
				}
			});
		}
	};

	var validateFormGuestUser = function(form) {
		form.validate({
			rules: {
				first_name: {
					required: true,
					validName: true,
					firstCharAlphaOnly: true
				},
				last_name: {
					required: true,
					validName: true,
					firstCharAlphaOnly: true
				}
			},
			messages: {
				 first_name: {required: "First name is required."},
				 last_name: {required: "Last name is required."},
				 primary_user_id: {required: "Companion is required."}
			},
			focusInvalid: false,
			onkeyup: false
		});
	};

	var vaidateFormItinerary = function(form) {
		form.validate({
			rules: {
				title: {
					required: true,
					validName: true
				},
				location: {
					validName: true
				},
				start_date_time: {
					required: true,
					validDate: true,
					dateVsCurrentDate: true
				},
				start_time: {
					required: true,
					validEventStartDate: true
				},
				end_date_time: {
					required: true,
					endDate: true,
					validDate: true,
					dateVsCurrentDate: true
				},
				end_time: {
					required: true,
					endTime: true,
					validEventDate: true
				}
			},
			errorPlacement: function(error, element) {
				error.appendTo( element.parents('div.controls'));
			},
			focusInvalid: false,
			onkeyup: false,
			messages: {
				 title: {
					 required: "Itinerary title is required."
				 },
				 start_date_time: {
					 required: "Itinerary start date is required."
				 },
				 start_time: {
					 required: "Itinerary start time is required."
				 },
				 end_date_time: {
					 required: 'Itinerary end date is required.'
				 },
				 end_time: {
					 required: "Itinerary end time is required."
				 }
			}
		});
	};
	var validateFormBreakout = function (form) {
		form.validate({
			rules: {
				title: 'required',
				location: {
					validName: true
				}
			},
			messages: {title: "Breakout title is required."},
			focusInvalid: false,
			onkeyup: false
		});
	};

	var validateFormPresentationCategory = function (form) {
		form.validate({
			rules: {
				name: 'required'
			},
			messages: {name: "Category name is required."},
			focusInvalid: false,
			onkeyup: false
		});
	};
	var validateFormGuest = function(form){
		form.validate({
			rules: {
				email: {
					required: true,
					email: true,
					remote: { url: "/admin/validate_email", type: "get"}
				},
				first_name: {
					required: true,
					validName: true,
					firstCharAlphaOnly: true
				},
				last_name: {
					required: true,
					validName: true,
					firstCharAlphaOnly: true
				},
				affiliation: {
					validName: true
				},
				title: {
					validName: true
				},
				industry: {
					validName: true
				}
			},
			messages: {
				 email: {
					required: 'Email is required.',
					email: 'Invalid email address.',
					remote: 'Email already exists.'
				 },
				 first_name: {
					 required: "First name is required."
				 },
				 last_name: {
					 required: "Last name is required."
				 }
			},
			focusInvalid: false,
			onkeyup: false
		});
	};
	var validateFormPresentation = function (form) {
		form.validate({
			rules: {
				title: {
					required: true
				},
				url: {
					required: true
				}
			},
			messages: {
				title: {
					required : "Title is required."
				},
				url: {
					required : "Link is required."
				}
			},
			focusInvalid: false,
			onkeyup: false
		});
	};

	vaidateFormItinerary($frmItinerary);
	validateFormEvent($frmEvent);
	validateFormUser($frmUser);
	validateFormGuestUser($frmGuestUser);
	validateFormEventStep2($duplicate_event_form);
	validateFormPresentation($frmPresentation);

	 var formReset = function(container){
		container.find('input[type=text]:first').focus();
		container.find('input[type=text], textarea').val('').removeClass('error');
		container.find('select option:selected').removeAttr('selected');
		container.find('input[type=checkbox]').removeAttr('checked');
		container.find('a.btn-save').html('Save Changes');
		container.find('label.error').remove();
		var spk = container.find('div.speakers-container');
		if(spk.find('div.control-group').length > 0) {
			spk.find('div.control-group:gt(0)').remove();
		}
	};

	var presentationShowUrl = function() {
		$('#presentationGrpFile').addClass('hide');
		$('#presentationGrpLink').removeClass('hide');
	};
	var presentationShowFile = function() {
		$('#presentationGrpFile').removeClass('hide');
		$('#presentationGrpLink').addClass('hide');
	};

	$(document).on('click', '.rbtnDisplayType', function(e) {
		if ($(this).val() == 'url') {
			presentationShowUrl();
		} else {
			presentationShowFile();
		}
	});

	if ($('.rbtnDisplayType:checked').val() == 'url') {
		presentationShowUrl();
	} else {
		presentationShowFile();
	}
	switch($body.attr('id')){
		case 'duplicate_event_form':
			$('#date-start, #date-end, .itinerary_date').datepicker({
				dateFormat: 'mm-dd-yy',
				minDate: dateObj,
				onClose: function(){
					$(this).blur();
				}
			});
			$('#time-start, #time-end').timepicker({
				showPeriod: true,
				showLeadingZero: true,
				onSelect: function() {
					$(this).blur();
				}
			});
			$('#add-more-maps').on('click', function(e){
				e.preventDefault();
				var input = '<div class="new-input-file"><input type="text" name="new_map_photo_title[]" placeholder="Map Title" />'
					+ '<input type="file" name="new_map_photo[]" /></div>';
				$('#map-photo-item-new').append(input);
			});
			CKEDITOR.replace('additional-info');
			validateFormEventStep2($duplicate_event_form);

			break;
		case 'dashboard':
			$('#myTab').on('click','a', function (e) {
				e.preventDefault();
				$(this).tab('show');
				$(this).blur();
			});
			var loadEvents = function(paramUrl, paramDiv){
				$.ajax({
					url: '/admin/' + paramUrl,
					success: function(data) {
						$(paramDiv).html(data);
					}
				});
			};
			loadEvents('get_events_published', '#div-published');
			loadEvents('get_events_unpublished', '#div-unpublished');
			loadEvents('get_events_completed', '#div-completed');

			$(document).on('click', 'a.btn-event-delete', function(){
				var $delePopup = $('#popup-event-delete'),
					eventName = $(this).parents('tr').find('td:eq(0)').html();
				$delePopup.find('div.modal-body').html('<p>Are you sure you want to delete <b>'+eventName+'</b> event?</p>');
				$delePopup.attr('data-event-id',$(this).attr('data-event-id'));
				$delePopup.modal('show');
			});
			$(document).on('click', '#btn-confirm-event-delete', function(){
				$('#popup-event-delete').find('div.modal-body').html(loader);
				var eid = $(this).parents('div.modal').attr('data-event-id');
				window.location = '/admin/delete_event?id='+eid;
			});
		break;
		case 'edit_event':
		case 'add_event':
			$('#date-start, #date-end').datepicker({
				dateFormat: 'mm-dd-yy',
				minDate: dateObj,
				onClose: function(){
					$(this).blur();
				}
			});
			$('#time-start, #time-end').timepicker({
				showPeriod: true,
				showLeadingZero: true,
				onSelect: function() {
					$(this).blur();
				}
			});
			$('#add-more-maps').on('click', function(e){
				e.preventDefault();
				var input = '<div class="new-input-file"><input type="text" name="new_map_photo_title[]" placeholder="Map Title" />'
					+ '<input type="file" name="new_map_photo[]" /></div>';
				$('#map-photo-item-new').append(input);
			});
			CKEDITOR.replace( 'additional-info' );
		break;
		case 'view_event':
			var $itineraries = $('#itineraries'),
				$guests = $('#guests'),
				$owners = $('#owners'),
				$guestAddPopup = $('#guest-add'),
				$eventOwnerAddPopup = $('#event-owner-add'),
				$deleteGuestPopup = $('#guest-delete'),
				$deleteEventOwnerPopup = $('#event-owner-delete'),
				$breakoutAddEditPopup = $('#breakout-add-edit-popup'),
				$breakoutDeletePopup = $('#breakout-delete-popup'),
				$breakoutAttendeesPopup = $('#breakout-attendees-popup'),
				$addAgendaAttendeesPopup = $('#agenda-attendees'),
				$activityPreferencePopup = $('#activity-preference'),
				$presentation_categories = $('#presentation_categories'),
				$presentationPopup = $('#presentation-popup');

			$('#myTab').on('click','a', function (e) {
				e.preventDefault();
				$(this).tab('show');
				$(this).blur();
			});


			// add itineraries
			$('#add-itinerary').on('click', function(e){
				e.preventDefault();
				$('#program-add').modal('show');
			});

			$('#program-add').on('shown', function(){
				//formReset($(this));
				var $container = $(this).find('div.modal-body');
				formReset($(this));
				$.ajax({
				  url: '/admin/add_itinerary',
				  data: 'eid=' + CMS.event_id,
				  type: 'GET',
				  success: function(data) {
					  $container.html(data);
					  $container.find('input[type=text]:first').focus();
					  vaidateFormItinerary($container.find('form'));
					  loadDatePickers();
				  }
				});
			}).on('hide', function(){
				if(CMS.editListener){
					if (!confirm('You have made changes on the form. Are you sure you want discard your changes?')){
						return false;
					}else {
						CMS.editListener = false;
					}
				}
			});

			// add presentation category
			$('#add-presentation-category').on('click', function(e){
				e.preventDefault();
				$('#presentation-category-add').modal('show');
			});

			$('#presentation-category-add').on('shown', function(){
				//formReset($(this));
				var $container = $(this).find('div.modal-body');
				formReset($(this));
				$.ajax({
				  url: '/admin/add_presentation_category',
				  data: 'eid=' + CMS.event_id,
				  type: 'GET',
				  success: function(data) {
					  $container.html(data);
					  $container.find('input[type=text]:first').focus();
					  validateFormPresentationCategory($container.find('form'));
				  }
				});
			}).on('hide', function(){
				if(CMS.editListener){
					if (!confirm('You have made changes on the form. Are you sure you want discard your changes?')){
						return false;
					}else {
						CMS.editListener = false;
					}
				}
			});

			$body.on('submit', 'form.form-presentation-category' , function(e){
				if($(this).valid()){
					e.preventDefault();
					$.ajax({
					  url: $(this).attr('action'),
					  data: $(this).serialize(),
					  type: 'POST',
					  success: function(data) {
						  if(data !== null){
							  $('div.modal').modal('hide');
							  loadPresentationCategories();
						  }else{
							  alert('Internal Server Error');
						  }
					  }
					});
				}
			});

			// edit presentation categories
			$presentation_categories.on('click','a.edit-presentation-category', function(e){
				e.preventDefault();
				$('#presentation-category-edit')
					.attr('data-presentation-category-id',$(this).attr('data-presentation-category-id'))
					.modal('show');
			});
			$('#presentation-category-edit').on('shown', function(){
				var $container = $(this).find('div.modal-body');
				formReset($(this));
				$.ajax({
					  url: '/admin/edit_presentation_category',
					  data: 'id='+$(this).attr('data-presentation-category-id')+"&eid="+CMS.event_id,
					  type: 'GET',
					  success: function(data) {
						  $container.html(data);
						  $container.find('input[type=text]:first').focus();
						  validateFormPresentationCategory($container.find('form'));
					  }
				});
			}).on('hide', function(){
				var $container = $(this).find('div.modal-body');
				if(CMS.editListener){
					if (!confirm('You have made changes on the form. Are you sure you want discard your changes?')){
						return false;
					}else {
						CMS.editListener = false;
					}
				}
				$container.html(loader);
			});

			// delete presentation categories
			$presentation_categories.on('click','a.btn-delete-presentation-category', function(e){
				e.preventDefault();
				var presentationCategoryName = $(this).parents('tr').find('td:eq(0)').html(),
					presentationCategoryID = $(this).attr('data-presentation-category-id');
					$delePopup = $('#presentation-category-delete');

				$delePopup.find('div.modal-body').html('<p>Are you sure you want to delete <b>'+presentationCategoryName+'</b> category?</p>');
				$delePopup.attr('data-presentation-category-id',presentationCategoryID);
				$delePopup.modal('show');
			});
			$('#confirm-presentation-category-delete').on('click', function(){
				var presentationCategoryID = $('#presentation-category-delete').attr('data-presentation-category-id');
				$.ajax({
				  url: '/admin/delete_presentation_category',
				  data: 'id='+presentationCategoryID+'&eid='+CMS.event_id,
				  type: 'GET',
				  success: function(data) {
					  $('div.modal').modal('hide');
					  loadPresentationCategories();
				  }
				});
			});

			$body.on('keyup keydown','input, textarea', function(){
				CMS.editListener = true;
			});
			$body.on('change','select', function(){
				CMS.editListener = true;
			});
			$body.on('click','input[type=checkbox]', function(){
				CMS.editListener = true;
			});

			$body.on('submit', 'form.form-itinerary' , function(e){
				if($(this).valid()){
					e.preventDefault();
					$.ajax({
					  url: $(this).attr('action'),
					  data: $(this).serialize(),
					  type: 'POST',
					  success: function(data) {
						  if(data !== null){
							  $('div.modal').modal('hide');
							  loadItineraries();
						  }else{
							  alert('Internal Server Error');
						  }
					  }
					});
				}
			});
			// edit itineraries
			$itineraries.on('click','a.edit-program', function(e){
				e.preventDefault();
				$('#program-edit')
					.attr('data-program-id',$(this).attr('data-program-id'))
					.modal('show');
			});
			$('#program-edit').on('shown', function(){
				var $container = $(this).find('div.modal-body');
				formReset($(this));
				$.ajax({
					  url: '/admin/edit_itinerary',
					  data: 'id='+$(this).attr('data-program-id')+"&eid="+CMS.event_id,
					  type: 'GET',
					  success: function(data) {
						  $container.html(data);
						  $container.find('input[type=text]:first').focus();
						  vaidateFormItinerary($container.find('form'));
						  loadDatePickers();
					  }
				});
			}).on('hide', function(){
				var $container = $(this).find('div.modal-body');
				if(CMS.editListener){
					if (!confirm('You have made changes on the form. Are you sure you want discard your changes?')){
						return false;
					}else {
						CMS.editListener = false;
					}
				}
				$container.html(loader);
			});

			// delete itineraries
			$itineraries.on('click','a.delete-program', function(e){
				e.preventDefault();
				var programName = $(this).parents('tr').find('td:eq(1)').html(),
					programID = $(this).attr('data-program-id');
					$delePopup = $('#program-delete');

				$delePopup.find('div.modal-body').html('<p>Are you sure you want to delete <b>'+programName+'</b> program?</p>');
				$delePopup.attr('data-program-id',programID);
				$delePopup.modal('show');
			});
			$('#confirm-program-delete').on('click', function(){
				var programID = $('#program-delete').attr('data-program-id');
				$.ajax({
				  url: '/admin/delete_itinerary',
				  data: 'id='+programID+'&eid='+CMS.event_id,
				  type: 'GET',
				  success: function(data) {
					  $('div.modal').modal('hide');
					  loadItineraries();
				  }
				});
			});
			// add more speaker
			$body.on('click','a.btn-add-more-speaker', function(e){
				var $parentContainer = $(this).parents('div.speakers-container');
					$parentGroup = $(this).parents('div.control-group');
					newSelection = $parentGroup.clone(),
					$selectedSpeakers = $parentContainer.find('select option:selected');

				$selectedSpeakers.each(function(){
					if(this.value)
						newSelection.find('option[value='+this.value+']').remove();
				});
				newSelection.find('select option:eq(0)').attr('selected',true);
				newSelection.appendTo($parentContainer);
				e.preventDefault();
			});
			// remove speaker
			$body.on('click','a.btn-remove-speaker', function(e){
				var $parentContainer = $(this).parents('div.speakers-container');
					$parentGroup = $(this).parents('div.control-group'),
					$speakerName = $parentGroup.find('select');

				if($speakerName.val()){
					var confirmDelete = confirm('Are you sure you want to delete '+$speakerName.find('option:selected').text()+'?');
					if(!confirmDelete){
						return false;
					}
				}
				if($parentContainer.find('div.control-group').length > 1) {
					$parentGroup.remove();
				}else {
					$parentGroup.find('select option:eq(0)').attr('selected', true);
				}
				CMS.editListener = true;
				e.preventDefault();
			});
			// email speaker
			$body.on('click','a.btn-email-speaker', function(e){
				var $parentContainer = $(this).parents('div.speakers-container');
					$parentGroup = $(this).parents('div.control-group'),
					$speakerName = $parentGroup.find('select');
					$itineraryId = $('input[name=itinerary_id]').val();
					$breakoutId = $('input[name=breakoutID]').val();
					$referenceType = $('input[name=reference_type]').val();
					$breakoutData = '';

				if ($speakerName.val()){
					var confirmEmail = confirm('Are you sure you want to send speaker invite to ' + $speakerName.find('option:selected').text() + '?');
					if (confirmEmail){
						if ($breakoutId){
							$breakoutData = '&bid='+$breakoutId;
						}
						$.ajax({
							  url: '/admin/send_speaker_invite',
							  data: 'id='+$itineraryId+'&eid='+CMS.event_id+'&uid='+$speakerName.val()+'&rtype='+$referenceType+$breakoutData,
							  type: 'POST',
							  success: function(data) {

							  }
							});
					}else{
						return false;
					}
				}
				CMS.editListener = true;
				e.preventDefault();
			});
			// add event attendees
			$('#add-guest').on('click', function(e){
				e.preventDefault();
				$guestAddPopup.find('div.modal-header h3').text('Manage Event Attendees');
				$guestAddPopup.modal('show');

			});
			$guestAddPopup.on('shown',function(){
				var $popupContainer = $(this).find('div.modal-body'),
					userID = $(this).attr('data-user-id') ? $(this).attr('data-user-id') : 0;

				attendeesIsChanged = false;

				$(document).on('keypress', '#guest-add .search-user', function (evt) {
					var charCode = evt.charCode || evt.keyCode;
					if (charCode  == 13) {
						return false;
					}
				});

				formReset($(this));
				$.ajax({
				  url: '/admin/add_event_attendees',
				  data: 'eid='+CMS.event_id,
				  type: 'GET',
				  success: function(data) {
					  $popupContainer.html(data);
					  $popupContainer.find('input[type=text]:first').focus();
					  validateFormGuest($popupContainer.find('form'));
					  $('#guest-add .search-user').domsearch('#guest-add ul#attendees');
				  }
				});
			});

			// add event attendees
			$('#add-event-owner').on('click', function(e){
				e.preventDefault();
				$eventOwnerAddPopup.find('div.modal-header h3').text('Manage Event Owners');
				$eventOwnerAddPopup.modal('show');

			});
			$eventOwnerAddPopup.on('shown',function(){
				var $popupContainer = $(this).find('div.modal-body'),
					userID = $(this).attr('data-user-id') ? $(this).attr('data-user-id') : 0;

				eventOwnerIsChanged = false;

				$(document).on('keypress', '#event-owner-add .search-user', function (evt) {
					var charCode = evt.charCode || evt.keyCode;
					if (charCode  == 13) {
						return false;
					}
				});

				formReset($(this));
				$.ajax({
				  url: '/admin/add_event_owners',
				  data: 'eid='+CMS.event_id,
				  type: 'GET',
				  success: function(data) {
					  $popupContainer.html(data);
					  $popupContainer.find('input[type=text]:first').focus();
					  validateFormGuest($popupContainer.find('form'));
					  $('#event-owner-add .search-user').domsearch('#event-owner-add ul#attendees');
				  }
				});
			}).on('hide', function(){
				$(this).find('div.modal-body').html(loader);
			});


			$('div.modal').on('click','#attendees a', function(){
				var $userid = $(this).attr('data-userid'),
					$startDate = $('#breakout-attendees-popup').find('.startDateTime').val(),
					$endDate = $('#breakout-attendees-popup').find('.endDateTime').val(),
					$userBlock = $(this).parents('li'),
					$form = $(this).parents('form'),
					$name = $(this).find('span.user_name').text();

				attendeesIsChanged = true;
				eventOwnerIsChanged = true;

				if($form.length) {
					if($(this).hasClass('selected')){
						if($(this).hasClass('has-companions')) {
							if(confirm('Warning: '+$name+' has companions! Removing him to the event attendees will also remove his companions.')) {
								$(this).removeClass('selected');
								$userBlock.find('input[type=hidden]').remove();
							}
						}else {
							$(this).removeClass('selected');
							$userBlock.find('input[type=hidden]').remove();
						}
					}else {
						$(this).addClass('selected');
						$userBlock.append('<input type="hidden" value="'+$userid+'" name="user_id[]" />');
						//alert($(this).parent().parent().parent().attr('class'));//find('.startDateTime').val());
						$.ajax({
							  url: '/admin/show_concurrent_activity',
							  data: 'user_id='+$userid+'&start_date='+$startDate+'&end_date='+$endDate,
							  type: 'GET',
							  cache: false,
							  success: function(data) {
								if(data!=""){
									//alert(data);
								}

							  }
						});
					}
					CMS.editListener = true;
				}
			});

			$guestAddPopup.on('hide', function(){
				if (attendeesIsChanged == true) {
					if(CMS.editListener){
						var saveChanges = confirm('You have made changes on the form. Are you sure you want discard your changes?');
						if (!saveChanges) {
							$(this).find('div.modal-body').html(loader);
							var $popupContainer = $(this).find('div.modal-body'),
								userID = $(this).attr('data-user-id') ? $(this).attr('data-user-id') : 0;

							formReset($(this));
							$.ajax({
							  url: '/admin/add_event_attendees',
							  data: 'eid='+CMS.event_id,
							  type: 'GET',
							  success: function(data) {
								  $popupContainer.html(data);
								  $popupContainer.find('input[type=text]:first').focus();
								  validateFormGuest($popupContainer.find('form'));
								  $('#search-user').domsearch('ul#attendees');
							  }
							});
							CMS.editListener = false;
							return false;
						} else {
							$(this).find('div.modal-body').html(loader);
							$(this).removeAttr('data-user-id');
							$(this).find('div.modal-header h3').html('Add Guest');
							CMS.editListener = false;
						}
					}
				}
			});

			$eventOwnerAddPopup.on('hide', function(){
				if (eventOwnerIsChanged == true) {
					if(CMS.editListener){
						if (!confirm('You have made changes on the form. Are you sure you want discard your changes?')){
							return false;
						}else {
							$(this).find('div.modal-body').html(loader);
							$(this).removeAttr('data-user-id');
							$(this).find('div.modal-header h3').html('Add Event Owner');
							CMS.editListener = false;
						}
					}
				}
			});

			$body.on('submit','#form-add-edit-guest', function(e){
				var  $parentPopupContainer = $(this).parents('div.modal');
				if($(this).valid()) {
					e.preventDefault();
					$.ajax({
					  url: $(this).attr('action'),
					  data: $(this).serialize(),
					  type: 'POST',
					  success: function(data) {
						  if(data != undefined){
							  $('div.modal').modal('hide');
							  loadGuests();
						  }
					  }
					});
				}
			});
			$body.on('submit','#form-add-event-attendees', function(e){
				var  $parentPopupContainer = $(this).parents('div.modal');
				if($(this).valid()) {
					e.preventDefault();
					$.ajax({
					  url: $(this).attr('action'),
					  data: $(this).serialize(),
					  type: 'POST',
					  dataType: 'json',
					  success: function(data) {
						  if(data != undefined){
							  if(data.selectCompanions){
								  $parentPopupContainer.find('div.modal-header h3').text('Manage Companions');
								  $parentPopupContainer.find('div.modal-body').html(data.selectCompanions);
								  $parentPopupContainer.find('a.btn-save').text('Save Changes');
								  $parentPopupContainer.find('[data-dismiss=modal]').removeAttr('data-dismiss').addClass('reload-attendees-onclose');
							  }else{
								  $('div.modal').modal('hide');
								  loadGuests();
							  }
						  }
					  }
					});
				}
			});
			$body.on('submit','#form-add-event-owners', function(e){
				var  $parentPopupContainer = $(this).parents('div.modal');
				if($(this).valid()) {
					e.preventDefault();
					$.ajax({
					  url: $(this).attr('action'),
					  data: $(this).serialize(),
					  type: 'POST',
					  dataType: 'json',
					  success: function(data) {
						  if(data != undefined){
							  $('div.modal').modal('hide');
								loadOwners();
						  }
					  }
					});
				}
			});

			$body.on('click', '.reload-attendees-onclose', function(){
				$('div.modal').modal('hide');
				loadGuests();
			});
			$body.on('click', '.reload-event-owners-onclose', function(){
				$('div.modal').modal('hide');
				loadOwners();
			});

			$body.on('submit','#form-add-companion', function(e){
				e.preventDefault();
				$.ajax({
				  url: $(this).attr('action'),
				  data: $(this).serialize(),
				  type: 'POST',
				  success: function(data) {
					  $('div.modal').modal('hide');
					  loadGuests();
				  }
				});
			});

			// email attendees
			$('#email-guests').on('click', function(e){
				e.preventDefault();
				if($('div#guests').find('div.alert').length > 0) {
					alert('Guest list is empty');
				} else {
					var $guestsEmailPopup = $('#guests-email-popup');
					$guestsEmailPopup.modal('show')
						.find('.modal-body').find('span.loader').hide().end()
						.html('<p><b>Warning!</b> You are going to send an email blast invite to all the listed attendees. This may take a while. Proceed?</p>');
				}
			});
			$('#btn-confirm-event-email-invite').on('click', function(){
				var $guestsEmailPopup = $('#guests-email-popup');
				$guestsEmailPopup.find('.modal-body').html('<span class="loader">&nbsp;</span><br />').end()
					.find('.modal-footer').hide();

				$.ajax({
					url: '/admin/email_event_invite',
					data: 'eid='+CMS.event_id,
					type: 'GET',
					success: function(data) {
						console.log(data);
						$guestsEmailPopup.html(data);
					}
				});
			});

			// delete event attendees
			$guests.on('click','a.btn-delete-guest', function(e){
				var guestName = $(this).parents('tr').find('td:eq(0)').html(),
					userID = $(this).attr('data-user-id'),
					warningMessage = '';
				e.preventDefault();
				if(($(this).hasClass('has-companions'))){
					warningMessage = '<strong>Warning:</strong> Removing <strong>'+guestName+'</strong> will also remove his companion on the event.<br />';
				}
				warningMessage += 'Are you sure you want to delete <strong>'+guestName+'</strong> for this event?';
				$deleteGuestPopup.attr('data-user-id',userID);
				$deleteGuestPopup.find('div.modal-body').html(warningMessage);
				$deleteGuestPopup.modal('show');
			});
			$deleteGuestPopup.on('click','#btn-confirm-delete-guest', function(){
				var userID = parseInt($deleteGuestPopup.attr('data-user-id'));
				$.ajax({
				  url: '/admin/delete_event_guest',
				  data: 'eid='+CMS.event_id+'&uid='+userID,
				  type: 'GET',
				  success: function(data) {
					  $deleteGuestPopup.modal('hide');
					  loadGuests();
				  }
				});
			});

			// delete event owners
			$owners.on('click','a.btn-delete-event-owner', function(e){
				var guestName = $(this).parents('tr').find('td:eq(0)').html(),
					userID = $(this).attr('data-user-id'),
					warningMessage = '';
				e.preventDefault();
				warningMessage += 'Are you sure you want to delete <strong>'+guestName+'</strong> for this event?';
				$deleteEventOwnerPopup.attr('data-user-id',userID);
				$deleteEventOwnerPopup.find('div.modal-body').html(warningMessage);
				$deleteEventOwnerPopup.modal('show');
			});
			$deleteEventOwnerPopup.on('click','#btn-confirm-delete-event-owner', function(){
				var userID = parseInt($deleteEventOwnerPopup.attr('data-user-id'));
				$.ajax({
				  url: '/admin/delete_event_owner',
				  data: 'eid='+CMS.event_id+'&uid='+userID,
				  type: 'GET',
				  success: function(data) {
					  $deleteEventOwnerPopup.modal('hide');
					  loadOwners();
				  }
				});
			});

			// add breakout
			$itineraries.on('click','a.btn-add-breakout', function(e){
				var iid = parseInt($(this).attr('data-program-id'));
				$breakoutAddEditPopup.find('div.modal-body').html(loader);
				$breakoutAddEditPopup.attr('data-program-id',iid);
				$breakoutAddEditPopup.attr('data-breakout-id',0);
				$breakoutAddEditPopup.find('div.modal-header h3').html('Add Activity');
				$breakoutAddEditPopup.modal('show');
				e.preventDefault();
			});

			$breakoutAddEditPopup.on('shown', function(){
				var iid = parseInt($(this).attr('data-program-id')),
					bid = $(this).attr('data-breakout-id') ? parseInt($(this).attr('data-breakout-id')) : 0,
					$modalBody = $(this).find('div.modal-body');

				formReset($(this));
				$.ajax({
				  url: '/admin/add_edit_breakout',
				  data: 'iid='+iid+'&bid='+bid+'&eid='+CMS.event_id,
				  type: 'GET',
				  success: function(data) {
					  $modalBody.html(data);
					  $modalBody.find('input[type=text]:first').focus();
					  validateFormBreakout($modalBody.find('form'));
					  loadDatePickers();
				  }
				});
			}).on('hide', function(){
				if(CMS.editListener){
					if (!confirm('You have made changes on the form. Are you sure you want discard your changes?')){
						return false;
					}else {
						$(this).find('div.modal-body').html(loader);
						CMS.editListener = false;
					}
				}
			});
			$breakoutAddEditPopup.on('click','#btn-add-team', function(e){
				var $teamName = $('#team-name'),
					$teamMembers = $('#team-members');
					$list = $('#table-assigned-team');
					$hiddenFieldTeamMembers = $hiddenFieldTeamName= html = '',
					$modalBody = $breakoutAddEditPopup.find('div.modal-body');

				if(!$teamName.val()) {
					alert('Please enter team name.');
					return false;
				}
				if($teamMembers.find('option:selected').length == 0) {
					alert('Please select team member.');
					return false;
				}

				var $speakerSelect = $('select.speaker-select');

				$teamMembers.find('option:selected').each(function(){
					var el = $(this),
						assigned = false;
					$speakerSelect.each(function(){
						if($(this).val() == el.val()) {
							assigned = true;
							return false;
						}
					});
					if(assigned) {
						alert(el.text() +' is already on speaker list.');
					}else {
						var name = $(this).text(),
							$hiddenFieldTeamMembers = '<input type="hidden" value="'+this.value+'" name="team_members[]" />',
							$hiddenFieldTeamName = '<input type="hidden" value="'+$teamName.val()+'" name="team_name[]" />';

						html += '<tr><td>'+name+'</td>';
						html += '<td class="wordBreak">'+ $teamName.val()+'</td>';
						html += '<td><a class="icon-black icon-trash btn-delete-team-member" title="Delete Team Member" data-option-id="'+this.value+'" href="#"></a>';
						html += $hiddenFieldTeamMembers+$hiddenFieldTeamName+'</td></tr>';
						$(this).remove();
					}
				});

				$list.append(html);
				$(this).blur();
				e.preventDefault();
				$teamName.val('');
				$modalBody.animate({ scrollTop: $modalBody.height() }, 1000);
			});
			$breakoutAddEditPopup.on('click','a.btn-save', function(e){
				if (teamSpeakerExists) {
					$('#breakout-add-edit-popup').find('a.btn-save').children('span').remove();
					$('#breakout-add-edit-popup').find('a.btn-save').text('Save Changes');
				}
				teamSpeakerExists = false;
			});
			$body.on('click','a.btn-delete-team-member', function(){
				var $teamMembers = $('#team-members'),
					optionName = $(this).parents('tr').find('td:eq(0)').html(),
					optionValue = parseInt($(this).attr('data-option-id')),
					$newOption = $('<option />').text(optionName).val(optionValue);
				if(confirm('Are you sure you want to delete '+optionName+'?')) {
					$teamMembers.prepend($newOption);
					$(this).parents('tr').remove();
				}
				CMS.editListener = true;
			});
			$body.on('submit','#form-add-edit-breakout', function(e){
				var teams = $('#table-assigned-team tr'),
					teamMember = '',
					teamMemberName = ''
					arrayList = [];

				$('.speakers-container').find('select').each(function() {
					teamMemberName = $(this).find('option:selected').text();
					if (teamMemberName != '') {
						teams.each(function(index, value) {
							teamMember = $(this).find('td:first').text();
							if (teamMember != '') {
								if (teamMemberName == teamMember) {
									arrayList.push(teamMember);
									teamSpeakerExists = true;
								}
							}
						});
					}
				});

				if (teamSpeakerExists) {
					e.preventDefault();
					for (var i = 0; i < arrayList.length; i++) {
						alert(arrayList[i] + ' is already on the team list.');
					};
					return false;
				} else {
					if($(this).valid()) {
						e.preventDefault();
						$.ajax({
							  url: $(this).attr('action'),
							  data: $(this).serialize(),
							  type: 'POST',
							  success: function(data) {
								  if(data !== null){
									  $('div.modal').modal('hide');
									  loadItineraries();
								  }else{
									  alert('Internal Server Error');
								  }
							  }
						});
					}
				}
			});
			$body.on('click','#btn-add-team-agenda', function(e){
				var $teamName = $('#team-name'),
					$teamMembers = $('#team-members');
					$list = $('#table-assigned-team');
					$hiddenFieldTeamMembers = $hiddenFieldTeamName= html = '',
					$modalBody = $breakoutAddEditPopup.find('div.modal-body');

				if(!$teamName.val()) {
					alert('Please enter team name.');
					return false;
				}
				if($teamMembers.find('option:selected').length == 0) {
					alert('Please select team member.');
					return false;
				}

				var $speakerSelect = $('select.speaker-select');

				$teamMembers.find('option:selected').each(function(){
					var el = $(this),
						assigned = false;
					$speakerSelect.each(function(){
						if($(this).val() == el.val()) {
							assigned = true;
							return false;
						}
					});
					if(assigned) {
						alert(el.text() +' is already on speaker list.');
					}else {
						var name = $(this).text(),
							$hiddenFieldTeamMembers = '<input type="hidden" value="'+this.value+'" name="team_members[]" />',
							$hiddenFieldTeamName = '<input type="hidden" value="'+$teamName.val()+'" name="team_name[]" />';

						html += '<tr><td>'+name+'</td>';
						html += '<td>'+ $teamName.val()+'</td>';
						html += '<td><a class="icon-black icon-trash btn-delete-team-member" title="Delete Team Member" data-option-id="'+this.value+'" href="#"></a>';
						html += $hiddenFieldTeamMembers+$hiddenFieldTeamName+'</td></tr>';
						$(this).remove();
					}
				});

				$list.append(html);
				$(this).blur();
				e.preventDefault();
				$teamName.val('');
				$modalBody.animate({ scrollTop: $modalBody.height() }, 1000);
			});
			//edit breakout
			$itineraries.on('click', 'a.btn-edit-breakout', function(e) {
				var breakoutID = $(this).attr('data-breakout-id'),
					itineraryID = $(this).attr('data-program-id');

				$breakoutAddEditPopup.find('div.modal-body').html(loader);
				$breakoutAddEditPopup.attr('data-program-id', itineraryID);
				$breakoutAddEditPopup.attr('data-breakout-id', breakoutID);
				$breakoutAddEditPopup.find('div.modal-header h3').html('Edit Activity');
				$breakoutAddEditPopup.modal('show');
				e.preventDefault();
			});

			//delete breakout
			$itineraries.on('click', 'a.btn-delete-breakout', function(e) {
				var breakOutTitle = $(this).parents('tr').find('td:eq(0)').html(),
					breakOutID = $(this).attr('data-breakout-id');

				e.preventDefault();
				$breakoutDeletePopup.attr('data-breakout-id',breakOutID);
				$breakoutDeletePopup.find('div.modal-body').html('Are you sure you want to delete <strong>'+breakOutTitle+'</strong> for this event?');
				$breakoutDeletePopup.modal('show');
			});
			$breakoutDeletePopup.on('click','#btn-confirm-delete-breakout', function(){
				var breakOutID = parseInt($breakoutDeletePopup.attr('data-breakout-id'));
				$.ajax({
				  url: '/admin/delete_breakout',
				  data: 'bid='+breakOutID+'&eid='+CMS.event_id,
				  type: 'GET',
				  success: function(data) {
					  $breakoutDeletePopup.modal('hide');
					  loadItineraries();
				  }
				});
			});

			//show breakout charts
			$itineraries.on('click', 'a.btn-show-breakout', function(e) {
				var groupID = $(this).attr('data-bgroup-id'),
					$tableItineraries = $('#table-itineraries'),
					icon = $(this).find('i');

				if(icon.hasClass('icon-circle-arrow-down')) {
					$tableItineraries.find('tr.breakout-'+groupID).slideDown();
					icon.removeClass('icon-circle-arrow-down').addClass('icon-circle-arrow-up');
				} else {
					$tableItineraries.find('tr.breakout-'+groupID).slideUp();
					icon.removeClass('icon-circle-arrow-up').addClass('icon-circle-arrow-down');
				}
				e.preventDefault();
				$(this).blur();
			});

			// add/view attendees
			$itineraries.on('click', 'a.btn-breakout-attendees, a.btn-add-agenda-attendees', function(e) {
				var activityID = $(this).attr('data-activity-id'),
					activityType = $(this).attr('data-activity-type'),
					$hasPreferences = $(this).attr('data-haspreferences'),
					startDateTime = $(this).parent().parent().find('.startDateTime').val(),
					endDateTime = $(this).parent().parent().find('.endDateTime').val();
				e.preventDefault();
				$breakoutAttendeesPopup.attr('data-activity-id',activityID)
					.attr('data-activity-type',activityType)
					.attr('data-haspreferences',$hasPreferences);
				$breakoutAttendeesPopup.find('.startDateTime').val(startDateTime);
				$breakoutAttendeesPopup.find('.endDateTime').val(endDateTime);
				$breakoutAttendeesPopup.modal('show');
			});

			$breakoutAttendeesPopup.on('shown', function(){

				formReset($(this));
				var activityID =  parseInt($(this).attr('data-activity-id')),
					activityType = $(this).attr('data-activity-type'),
					$modalBody = $(this).find('div.modal-body'),
					hasPreferences = parseInt($(this).attr('data-haspreferences')),
					$button = $(this).find('a.btn-save');
				if(hasPreferences) {
					$button.attr({href: '/admin/manage_activity_attendees?id='+activityID+'&eid='+CMS.event_id+'&rtype='+activityType});
					$button.text('Manage Attendees');
					$ajaUrl = '/admin/view_attendees';
				}else {
					$button.text('Save Changes');
					$ajaUrl = '/admin/add_agenda_attendees';
				}

				$.ajax({
				  url: $ajaUrl,
				  data: 'id='+activityID+'&eid='+CMS.event_id+'&rtype='+activityType,
				  type: 'GET',
				  cache: false,
				  success: function(data) {
					  $modalBody.html(data);
					  $modalBody.find('input[type=text]:first').focus();
					  $('#search-user').domsearch('ul#attendees');
				  }
				});
			}).on('hide', function(){
				if(CMS.editListener){
					if (!confirm('You have made changes on the form. Are you sure you want discard your changes?')){
						return false;
					}else {
						CMS.editListener = false;
					}
				}
				$(this).find('div.modal-body').html(loader);
			});

			var loadItineraries = function() {
					$.ajax({
					  url: '/admin/get_event_itineraries',
					  data: 'id='+CMS.event_id,
					  type: 'GET',
					  cache: false,
					  success: function(data) {
						  $itineraries.html(data);
					  }
					});
				};
			var loadGuests = function(){
					$.ajax({
					  url: '/admin/get_event_guests',
					  data: 'id='+CMS.event_id,
					  type: 'GET',
					  cache: false,
					  success: function(data) {
						  $guests.html(data);
					  }
					});
				};
			var buildPresentationBtnLinks = function() {
				// manage presentation
				var btnPresentationCatId,
					btnPresentationEventId,
					managePresentationLink = '';

				$('.btn-presentation').each(function(e) {
					btnPresentationCatId   = $(this).attr('data-presentation-category-id');
					btnPresentationEventId = $(this).attr('data-event-id');
					managePresentationLink = 'id=' + btnPresentationCatId + '&eid=' + btnPresentationEventId;
					$(this).attr('href', '/admin/manage_presentations?' + managePresentationLink);
				})
			};
			var loadPresentationCategories = function(){
				$.ajax({
					url: '/admin/get_presentation_categories',
					data: 'id='+CMS.event_id,
					type: 'GET',
					cache: false,
					success: function(data) {
						$presentation_categories.html(data);
					},
					complete: function() {
						buildPresentationBtnLinks();
					}
				});
			};
			var loadOwners = function(){
				$.ajax({
				  url: '/admin/get_event_owners',
				  data: 'id='+CMS.event_id,
				  type: 'GET',
				  cache: false,
				  success: function(data) {
					  $owners.html(data);
				  }
				});
			};
			var loadDatePickers = function(){
				var minDate = new Date(CMS.eventStartDateTime),
					maxDate = new Date(CMS.eventEndDateTime);
				$('input.datepicker').datepicker({
					dateFormat: 'mm-dd-yy',
					minDate: minDate,
					maxDate: maxDate,
					onClose: function(){
						var $dateEnd = $(this).parents('div.modal-body').find('input.date-end');
						if($(this).hasClass('date-start') && !$dateEnd.val()) {
							$dateEnd.val($(this).val()).blur();
						}
						$(this).blur();
					}
				});
				$('input.timepicker').timepicker({
					showPeriod: true,
					showLeadingZero: true,
					onSelect: function(){
						$(this).blur();
					}
				});
			};

			$(document).on('focus', '.timepicker', function(e) {
				var timepickerHeight = $('.ui-timepicker').height();
				var inputPosition = $(this).position();
				var cssPosition = inputPosition.top - 55;
				$('.ui-timepicker').css('top',  + cssPosition);
			});

			loadItineraries();
			loadGuests();
			loadDatePickers();
			loadOwners();
			loadPresentationCategories();

			// add activity preference
			$itineraries.on('click', 'a.btn-activity-preferences', function(e) {
				var referenceID = $(this).attr('data-program-id'),
					referenceType = $(this).attr('data-program-type');
				$activityPreferencePopup.attr('data-program-id', referenceID);
				$activityPreferencePopup.attr('data-program-type', referenceType);
				$activityPreferencePopup.modal('show');
			});
			$activityPreferencePopup.on('shown', function(e){
				var referenceID = $(this).attr('data-program-id'),
					referenceType = $(this).attr('data-program-type'),
					$modalBody = $(this).find('div.modal-body'),
					$button = $(this).find('a.btn-save');

				var $data = 'id='+referenceID+'&eid='+CMS.event_id+'&rtype='+referenceType;

				$button.attr('href', '/admin/manage_activity_preferences?'+$data);

				e.stopPropagation();
				$.ajax({
				  url: '/admin/view_activity_preferences',
				  data: $data,
				  type: 'GET',
				  cache: false,
				  success: function(data) {
					  $modalBody.html(data);
				  }
				});
			}).on('hide', function(){
				$(this).find('div.modal-body').html(loader);
			});
			$body.on('click','#btn-select-all', function(){
				var	$parentForm = $(this).parents('form'),
					$attendees = $parentForm.find('#attendees a');

				$attendees.each(function(){
					var $userid = $(this).attr('data-userid'),
						$userBlock = $(this).parents('li');

					$(this).addClass('selected');
					$userBlock.append('<input type="hidden" value="'+$userid+'" name="user_id[]" />');
				});
			});

			$body.on('click','#btn-deselect-all', function(){
				var	$parentForm = $(this).parents('form'),
					$attendees = $parentForm.find('#attendees a');

				$attendees.each(function(){
					var $userBlock = $(this).parents('li');
					$(this).removeClass('selected');
					$userBlock.find('input[type=hidden]').remove();
				});
			});

		break;
		case 'add_user':
			var $users = $('#user_list'),
				$view_user = $('#view_user'),
				$deleteGuestPopup = $('#guest-delete');

			$users.on('click','a.btn-delete-user', function(e){
				var guestName = $(this).parents('tr').find('td:eq(0)').html(),
					userID = $(this).attr('data-user-id');
				e.preventDefault();
				$deleteGuestPopup.attr('data-user-id',userID);
				$deleteGuestPopup.find('div.modal-body').html('Are you sure you want to delete <strong>'+guestName+'</strong> as user?');
				$deleteGuestPopup.modal('show');
			});
			$deleteGuestPopup.on('click','#btn-confirm-delete-guest', function(e){
				var userID = parseInt($deleteGuestPopup.attr('data-user-id'));
				e.preventDefault();
				$.ajax({
				  url: '/admin/delete_user',
				  data: 'uid='+userID,
				  type: 'GET',
				  beforeSend: function() {
				  	$deleteGuestPopup.modal('hide');
				 	$users.html('<br /><center><span class="loader">&nbsp;</span></center><br />');
				  },
				  success: function(data) {
					  loadUsers();
				  }
				});
			});
			var loadUser = function(id){
				$.ajax({
					  url: '/admin/get_user',
					  data: 'id=' + id,
					  type: 'GET',
					  success: function(data) {
						  $view_user.html(data);
					  }
					});
			};
			var loadUsers = function(){
				$.ajax({
				  url: '/admin/get_users',
				  type: 'GET',
				  success: function(data) {
					  $users.html(data);
				  }
				});
			};
			$body.on('click','#user-pagination a', function(e){
				$.ajax({
				  url: this.href,
				  type: 'GET',
				  success: function(data) {
					  $users.html(data);
				  }
				});
				e.preventDefault();
			});

			loadUsers();
			break;
		case 'add_guest':
			var $users = $('#user_guest_list'),
				$view_user = $('#view_user'),
				$deleteGuestPopup = $('#guest-delete');

			$users.on('click','a.btn-delete-user', function(e){
				var guestName = $(this).parents('tr').find('td:eq(0)').html(),
					userID = $(this).attr('data-user-id');
				e.preventDefault();
				$deleteGuestPopup.attr('data-user-id',userID);
				$deleteGuestPopup.find('div.modal-body').html('Are you sure you want to delete <strong>'+guestName+'</strong> as companion?');
				$deleteGuestPopup.modal('show');
			});
			$deleteGuestPopup.on('click','#btn-confirm-delete-guest', function(e){
				var userID = parseInt($deleteGuestPopup.attr('data-user-id'));
				e.preventDefault();
				$.ajax({
				  url: '/admin/delete_guest_user',
				  data: 'uid='+userID,
				  type: 'GET',
				  beforeSend: function() {
				  	$deleteGuestPopup.modal('hide');
				 	$users.html('<br /><center><span class="loader">&nbsp;</span></center><br />');
				  },
				  success: function(data) {
					  loadGuestUsers();
				  }
				});
			});
			var loadUser = function(id){
				$.ajax({
					  url: '/admin/get_user',
					  data: 'id=' + id,
					  type: 'GET',
					  success: function(data) {
						  $view_user.html(data);
					  }
					});
			};
			var loadGuestUsers = function(){
				$.ajax({
				  url: '/admin/get_guest_users',
				  type: 'GET',
				  success: function(data) {
					  $users.html(data);
				  }
				});
			};
			$body.on('click','#user-pagination a', function(e){
				$.ajax({
				  url: this.href,
				  type: 'GET',
				  success: function(data) {
					  $users.html(data);
				  }
				});
				e.preventDefault();
			});

			loadGuestUsers();
		break;
		case 'manage_activity_attendees':

			var $addAttendeePopup = $('#add-attendee-popup'),
				$deleteAttendeePopup = $('#delete-attendee-popup'),
				$btnAddAttendee = $('#btn-add-attendee');

			var validateAddAttendee = function(form) {
				form.validate({
					rules: {
						user_id: 'required'
					},
					messages: {
						user_id: {required: "Select attendee!"}
					},
					errorPlacement: function(error, element) {
						error.appendTo( element.parents('div.controls'));
					},
					focusInvalid: false,
					onkeyup: false
				});
			};

			$btnAddAttendee.on('click', function(e) {
				$addAttendeePopup.find('div.modal-header h3').text('Add Activity Attendee');
				$addAttendeePopup.attr('data-user-id', '0');
				$addAttendeePopup.modal('show');
			});
			$addAttendeePopup.on('shown', function(e){
				var activityID =  parseInt($(this).attr('data-activity-id')),
					userID = parseInt($(this).attr('data-user-id')),
					activityType = $(this).attr('data-activity-type'),
					$modalBody = $(this).find('div.modal-body');
				$.ajax({
				  url: '/admin/add_edit_activity_attendee',
				  data: 'uid='+userID+'&id='+CMS.activity_id+'&eid='+CMS.event_id+'&rtype='+CMS.activity_type,
				  type: 'GET',
				  cache: false,
				  success: function(data) {
					  $modalBody.html(data);
					  validateAddAttendee($modalBody.find('form'));
				  }
				});
			}).on('hide', function(){
				$(this).find('div.modal-body').html(loader);
			});

			$body.on('submit','#form-add-activity-attendee', function(e){
				if($(this).valid()) {
					e.preventDefault();
					$.ajax({
						  url: $(this).attr('action'),
						  data: $(this).serialize(),
						  type: 'POST',
						  success: function(data) {
							  if(data !== null){
								 location.reload(true);
							  }else{
								  alert('Internal Server Error');
							  }
						  }
					});
				}
			});
			// edit activity
			$body.on('click','a.btn-edit-attendee', function(){
				$addAttendeePopup.find('div.modal-header h3').text('Edit Activity Attendee');
				$addAttendeePopup.attr('data-user-id', $(this).attr('data-user-id'));
				$addAttendeePopup.modal('show');
			});
			// delete activity attendee
			$body.on('click','a.btn-delete-attendee', function(){
				var name = $(this).parents('tr').find('td:first').text(),
					userID = $(this).attr('data-user-id');
				$deleteAttendeePopup.find('div.modal-body').html('Are you sure you want to delete <strong>'+name+'</strong> on attendee list?');
				$deleteAttendeePopup.attr('data-user-id', userID);
				$deleteAttendeePopup.modal('show');
			});

			$deleteAttendeePopup.on('click','#btn-yes-delete-attendee', function(e) {
				var userID = $deleteAttendeePopup.attr('data-user-id');
				e.preventDefault();
				$(this).html(loader);
				$.ajax({
					url: '/admin/delete_activity_attendee',
					data: 'uid='+userID+'&id='+CMS.activity_id+'&eid='+CMS.event_id+'&rtype='+CMS.activity_type,
					type: 'GET',
					cache: false,
					success: function(data) {
						if(data !== null){
							location.reload(true);
						  }else{
							  alert('Internal Server Error');
						  }
					}
				});
			});

		break;
		case 'manage_activity_preferences':
			var $addPreferencePopup = $('#add-activity-preference-popup'),
				$deletePreferencePopup = $('#delete-activity-preference-popup');

			var validatePreferences = function(form){
				form.validate({
					rules: {
						title: 'required',
						optionDisplayType: 'required',
						'options[]': 'required'
					},
					messages: {
						 title: {required: "Preference Title is required."},
						 optionDisplayType: {required: "Option Display Type is required."},
						 'options[]': {required: "Option is required."}
					},
					errorPlacement: function(error, element) {
						error.appendTo( element.parents('td'));
					},
					focusInvalid: false,
					onkeyup: false
				});
			};
			// add activity preference
			$('#btn-add-preference').on('click', function(){
				$addPreferencePopup.find('div.modal-header h3').text('Add Activity Preference');
				$addPreferencePopup.attr('data-apid',0);
				$addPreferencePopup.modal('show');
			});
			$addPreferencePopup.on('shown', function(){
				formReset($(this));
				var apid = $(this).attr('data-apid') ? $(this).attr('data-apid') : 0,
					activityID =  parseInt($(this).attr('data-activity-id')),
					$modalBody = $(this).find('div.modal-body');
				$.ajax({
				  url: '/admin/add_edit_activity_preference',
				  data: 'apid='+apid+'&id='+CMS.activity_id+'&eid='+CMS.event_id+'&rtype='+CMS.activity_type,
				  type: 'GET',
				  cache: false,
				  success: function(data) {
					  $modalBody.html(data);
					  validatePreferences($modalBody.find('form'));
				  }
				});
			}).on('hide', function(){
				$(this).find('div.modal-body').html(loader);
			});
			$addPreferencePopup.on('click','a.btn-add-more-option', function(){
				var $parentTable = $(this).parents('table'),
					$parentTr = $(this).parents('tr'),
					$parentTrClone = $parentTr.clone();
				$parentTrClone.find('td:first').html('Option '+($('tr.activity-options').length + 1));
				$parentTrClone.find('input[type=text]').attr('name','options[]').val('').focus();
				$parentTrClone.find('label.error').remove();
				$parentTrClone.find('input.error').removeClass('error');
				$parentTable.append($parentTrClone);
			});

			$addPreferencePopup.on('click','a.btn-remove-option', function(){
				var $parentTr = $(this).parents('tr'),
					numOptions = $('tr.activity-options').length,
					$optionValue = $parentTr.find('input[type=text]');
				if(numOptions == 1 && !$optionValue.val()){
				}else{
					if(confirm('Are you sure you want to remove this option?')){
						if(numOptions > 1) {
							$parentTr.remove();
						}else {
							$optionValue.val('');
						}
					}
				}
			});
			$body.on('submit','#form-activity-preference', function(e){
				if($(this).valid()) {
					e.preventDefault();
					$.ajax({
						  url: $(this).attr('action'),
						  data: $(this).serialize(),
						  type: 'POST',
						  success: function(data) {
							  if(data !== null){
								 location.reload(true);
							  }else{
								  alert('Internal Server Error');
							  }
						  }
					});
				}
			});

			// edit actvity preference
			$body.on('click','a.btn-edit-preference', function(e){
				var apid = $(this).attr('data-apid');
				$addPreferencePopup.find('div.modal-header h3').text('Edit Activity Preference');
				$addPreferencePopup.attr('data-apid', apid);
				$addPreferencePopup.modal('show');
			});

			// delete preference

			$body.on('click','a.btn-delete-preference', function(e){
				var apid = $(this).attr('data-apid'),
					prefTitle = $(this).parents('tr').find('td:first').text();
				$deletePreferencePopup.attr('data-apid', apid);
				$deletePreferencePopup.find('div.modal-body').html('<p>Are you sure you want to delete <strong>'+prefTitle+'</strong>?</p>');
				$deletePreferencePopup.modal('show');
				e.preventDefault();
			});

			$deletePreferencePopup.on('click','#btn-yes-delete-activity-preference', function(e){
				var apid = $deletePreferencePopup.attr('data-apid');
				e.preventDefault();
				$(this).html(loader);
				$.ajax({
					url: '/admin/delete_activity_preference',
					data: 'apid='+apid+'&rid='+CMS.activity_id+'&eid='+CMS.event_id+'&rtype='+CMS.activity_type,
					type: 'GET',
					cache: false,
					success: function(data) {
						if(data !== null){
							location.reload(true);
						  }else{
							  alert('Internal Server Error');
						  }
					}
				});
			});


		break;
		case 'manage_requests' :
			$('#tab-content').on('click', 'input.check-all', function(){
				var $parentTable = $(this).parents('table');
				if($(this).is(':checked')){
					$parentTable.find('input[type=checkbox]').attr('checked', true);
				}else{
					$parentTable.find('input[type=checkbox]').attr('checked', false);
				}
			});
			$('form.frm-manage-request').on('submit', function(e){
				var $status = $(this).find('select[name=status]'),
					$checked = $(this).find('input:checked');
				if($checked.length == 0){
					alert('Please select request to moderate.');
					return false;
				}
				if(!$status.val()){
					alert('Please select status.');
					$status.focus();
					return false;
				}
			});
		break;
		case 'manage_presentations':
			var $addPresentationPopup = $('#add-presentation-popup'),
				$deletePresentationPopup = $('#delete-presentation-popup');

			var validatePresentations = function(form){
				form.validate({
					rules: {
						title: 'required',
						url: 'required'
					},
					messages: {
						 title: {required: "Presentation Title is required."},
						 url: {required: "Link is required."}
					},
					errorPlacement: function(error, element) {
						error.appendTo( element.parents('td'));
					},
					focusInvalid: false,
					onkeyup: false
				});
			};

			// delete presentation
			$body.on('click','a.btn-delete-presentation', function(e){
				var pid = $(this).attr('data-pid'),
					presentationTitle = $(this).parents('tr').find('td:first').text();
				$deletePresentationPopup.attr('data-pid', pid);
				$deletePresentationPopup.find('div.modal-body').html('<p>Are you sure you want to delete <strong>'+presentationTitle+'</strong>?</p>');
				$deletePresentationPopup.modal('show');
				e.preventDefault();
			});

			$deletePresentationPopup.on('click','#btn-yes-delete-presentation', function(e){
				var pid = $deletePresentationPopup.attr('data-pid');
				e.preventDefault();
				$(this).html(loader);
				$.ajax({
					url: '/admin/delete_presentation',
					data: 'pid='+pid,
					type: 'GET',
					cache: false,
					success: function(data) {
						if(data !== null){
							location.reload(true);
						  }else{
							  alert('Internal Server Error');
						  }
					}
				});
			});

			$("#table-presentations tbody").sortable().disableSelection();
			//alert('test');

			$('#btn-save-order').on('click', function(e){
				var ids = [];
				e.preventDefault();

				$('input[name=presentation_order]').each(function() {
					ids.push(this.value);
				});

				$.ajax({
						url: '/admin/update_presentation_order',
						data: {ids:ids},
						type: 'POST',
						beforeSend: function() {
							$("#order-update-message").removeClass('alert').show().html(loader);
						},
						success: function(data) {
							if(data !== null){
								$("#order-update-message").addClass('alert').html('Order Updated').delay(1000).fadeOut();
							} else {
								alert('Internal Server Error');
							}
						},
						complete: function() {
							$("#order-update-message").removeClass('alert').show().html('');
						}
				});
			});

		break;

	}

	$(document).on('click', '#btn-search-users', function(e) {
		var searchText    = $.trim($('#txtSearchUser').val());
		var thisHref      = '/admin/get_user/name/' + searchText;
		var thisContainer = $('#user_list');

		if ($(this).parents('#add_guest').length > 0) {
			thisHref = '/admin/get_guest_user/name/' + searchText;
			thisContainer = $('#user_guest_list');
		}

		if (searchText == '') {
			thisHref = '/admin/get_users';
			if ($(this).parents('#add_guest').length > 0) {
				thisHref = '/admin/get_guest_users';
			}
		}

		$.ajax({
			url: thisHref,
			type: 'GET',
			beforeSend: function() {
				thisContainer.html('<br /><center><span class="loader">&nbsp;</span></center><br />');
			},
			success: function(data) {
				thisContainer.html(data);
			}
		});
	})

	$(document).on('click', '#user_list #user-pagination, #user_guest_list #user-pagination', function(e) {
		$('#user_list, #user_guest_list').html('<br /><center><span class="loader">&nbsp;</span></center><br />');
	});

	$('#cancel-add').click(function(e){
		window.location = '/admin/add_user';
		e.preventDefault();
	});

	$('a.btn-save').on('click', function(e){
		var $parentForm = $(this).parents('div.modal').find('form');
		if($parentForm.length) {
			if($parentForm.valid()){
				$parentForm.submit();
				$(this).html(loader);
			}
			e.preventDefault();
		}
		CMS.editListener = false;
	});

	$body.on('blur','input[name=end_date_time]', function(){
		var $endTime = $('input[name=end_time]');
		if($endTime.val())
			$endTime.valid();
	});

	$body.on('blur','input[name=start_date_time]', function(){
		var $startTime = $('input[name=start_time]');
		if($startTime.val())
			$startTime.valid();
	});

	//validate date (02/30/2013 should be invalid)
	var isDate = function (txtDate, separator) {
		var aoDate, ms, month, day, year;
		if (separator === undefined) {
			separator = '/';
		}
		aoDate = txtDate.split(separator);
		if (aoDate.length !== 3) {
			return false;
		}
		month = aoDate[0] - 1;
		day = aoDate[1] - 0;
		year = aoDate[2] - 0;
		if (year < 1000 || year > 3000) {
			return false;
		}
		ms = (new Date(year, month, day)).getTime();
		aoDate = new Date();
		aoDate.setTime(ms);
		if (aoDate.getFullYear() !== year ||
			aoDate.getMonth() !== month ||
			aoDate.getDate() !== day) {
			return false;
		}
		return true;
	};

});