$('document').ready(function() { 
	/* handling form validation */
	if(sessionStorage.getItem('token')){
		window.location.href = "welcome.html";
	}
	$("#login-form").validate({
		rules: {
			password: {
				required: true,
			},
			username: {
				required: true,
			},
		},
		messages: {
			password:{
			  required: "please enter your password"
			 },
			user_email: "please enter your username",
		},
		submitHandler: submitForm	
	});	   
	/* Handling login functionality */
	function submitForm() {		
		var data = $("#login-form").serialize();		
				
		var dataPost ={
			"username": $("#username").val(),
			"password":$("#password").val()
		};
		 var dataString = JSON.stringify(dataPost);
		 console.log(dataString);
		$.ajax({				
			type: "POST",
			url  : 'http://127.0.0.1:8000/auth',
			data : dataString,
			beforeSend: function(){	
				$("#error").fadeOut();
				$("#login_button").html('<span class="glyphicon glyphicon-transfer"></span> &nbsp; sending ...');
			},
			error: function (error) {	
				console.log(error.responseJSON.error);					
					$("#error").fadeIn(1000, function(){						
						$("#error").html('<div class="alert alert-danger"> <span class="glyphicon glyphicon-info-sign"></span> &nbsp; '+error.responseJSON.error+' !</div>');
						$("#login_button").html('<span class="glyphicon glyphicon-log-in"></span> &nbsp; Sign In');
					});
			},
			success : function(response){	
				console.log(response);	
				sessionStorage.setItem('token', response.access_token)	;	
				console.log(sessionStorage.getItem('token'));	 		
				if(response!=null){							
					sessionStorage.setItem('token', response.access_token)		
					$("#login_button").html('<img src="ajax-loader.gif" /> &nbsp; Signing In ...');
					 setTimeout(' window.location.href = "welcome.html"; ',4000);
				}
			}
		});
		return false;
	}   
});