$(function(){	
	alert('test');
    var $frmEvent = $('#form-event'),
    	$frmUser = $('form.form-user'), 
    	$frmItinerary = $('form-itinerary');
    	$frmForgotPassword = $('form.form-forgot-password');
    
    // custom validation 
    jQuery.validator.addMethod("endDate", function(value, element) {           
        var startDateTime = new Date($('#date-start').val().replace(/-/g,"/"));
        var endDateTime = new Date(value.replace(/-/g,"/")); 
       return endDateTime.getTime() >= startDateTime.getTime();
    },'End Date should be later than start date'); 
    
    
    $frmEvent.validate({
         rules: {
            title: "required",
            location: "required", 
            start_date_time: "required",
            end_date_time: { 
                required: true,
                endDate: true
            }
          },
          messages: {
             title: "Event title is required.",
             location: "Event location is required.", 
             start_date_time: "Event start date is required.",
             end_date_time: {
                 required: 'Event end date is required.'
             }
             },
             errorPlacement: function(error, element) {       
                error.appendTo( element.parents('div.controls'));                  
             },             
             focusInvalid: false
    });
    
    $frmUser.validate({
    	rules: {
    		email: {
    			required: true, 
    			email: true
    		}, 
    		first_name: 'required', 
    		last_name: 'required', 
    		password: {
    			required: true, 
    			password: true
    		}, 
    		password2: {
    			required: true, 
    			password: true
    		}
    	},            
        focusInvalid: false
    });
    
    $frmItinerary.validate({
    	rules: {
    		title: 'required', 
    		location: 'required',    		 
    		start_date_time: "required",
            end_date_time: { 
                required: true,
                endDate: true
            }
    	},            
        focusInvalid: false
    }); 
    
    
}); 