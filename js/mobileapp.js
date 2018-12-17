$(function(){	
	var $body = $('body'), 
		dateObj = new Date(),
		loader = $('<span>').addClass('loader'),
		$frmUser = $('form.form-user'),
		$frmAddEditGuest = $('#form-add-edit-guest'),
		$frmAddEditBreakout = $('#form-add-edit-breakout'); 
	
	//form validation 
	var errorMessage = '', 
	 	getErrorMessage = function() {
			return errorMessage; 
		};
		
	$.validator.addMethod("endDate", function(value, element) {  
		var $parentForm = $(element).parents('form'), 
			$startDateTime = $parentForm.find('input[name=start_date_time]');	 
	    	objDate1 = new Date($startDateTime.val().replace(/-/g,"/")), 
	    	objDate2 = new Date(value.replace(/-/g,"/"));
		
	   return objDate2.getTime() >= objDate1.getTime(); 
	},'End date should be later than equal to start date.'); 
	
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
	    return isValid;
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
		         	required: 'is required.', 
		         	email: 'is Invalid.', 
		         	remote: 'already exists.'
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
	
	validateFormUser($frmUser);
	
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

}); 