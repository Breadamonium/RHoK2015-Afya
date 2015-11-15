$(document).ready(function() {

	$('#login-form-link').click(function(e) {
		$("#login-form").delay(100).fadeIn(100);
		$("#register-form").fadeOut(100);
		$('#register-form-link').removeClass('active');
		$(this).addClass('active');
		e.preventDefault();
	});
	$('#register-form-link').click(function(e) {
		$("#register-form").delay(100).fadeIn(100);
		$("#login-form").fadeOut(100);
		$('#login-form-link').removeClass('active');
		$(this).addClass('active');
		e.preventDefault();
	});

	/*$('#currTime').on('click', function () {
		var today = new Date();
		var hour = today.getHours();
		var min = today.getMinutes();
		if(hour < 10) hour = "0" + hour;
		if(min < 10) min = "0" + min;
		$('#time').val(""+hour+":"+min);
	});*/

	var disableSignIn = true;
	var disableSignOut = true;
	var disableRegister = true;

	var ajaxRequest = null;

	var categorychoice = '';

	var abortExistingRequest = function(request) {
		if(request != null) {
			request.abort();
		}
	}

	var ajaxObject = function(that){
		return {
			method: "post",
			url: "../AJAX/ajax.php",
			data: { 
				name: that.attr('name'), 
				value: that.val() 
			},
			dataType: 'text'
		};
	};

	var addError = function(that, message) {
		that.next().html(message)
		that.next().removeClass('hidden');
		//$('#register-submit').addClass('disabled');
		//disableRegister = true;
	};

	var removeError = function(that) {
		that.next().addClass('hidden');
		//$('#register-submit').removeClass('disabled');
		//disableRegister = false;
	};

	var resetRegistrationForm = function() {
		$('#username_registration').val('');
		$('#firstname').val('');
		$('#lastname').val('');
		$('#email').val('');
		$('#phonenumber').val('');
		$('#findout').val('');
		$('#groupregistrationcheck').attr('checked', false);
		$('#ageregistrationcheck').attr('checked', false);
		categorychoice = '';
		$('#categorydropdownchoice').html('Category');
		//disableRegister = true;
	};

	var resetLoginForm = function() {
		$('#grouplogincheck').attr('checked', false);
		$('#username_login').val('');
		$('#numpeopleingroup').val(1);
		$('.btn-login').addClass("disabled");

	};

	var hasLoginError = false;
	var hasRegisterError = false;

	$('#username_registration').focusout(function() {
		var that = $(this);
		if(that.val().trim().length < 5 && that.val().trim().length > 0) {
			addError(that, "Username needs be at least 5 characters.");
			hasRegisterError = true;
		} else {
			hasRegisterError = false;
		} 
	});

	$('#phonenumber').keyup(function() {
		var that = $(this);
		if(that.val() == "") {
			removeError(that);
			hasRegisterError = false;
			return;
		}
		var pattern = /^\d+$/;
		if(!pattern.test(that.val())) {
			addError(that, "Numbers only.");
		} else {
			removeError(that);
			hasRegisterError = false;
		}
	});

	$('#username_registration').keyup(function() {
		var that = $(this);
		if(that.val().trim().length < 5) {
			hasRegisterError = true;
			return;
		}
		abortExistingRequest(ajaxRequest);
		that.next().addClass('hidden');
		ajaxRequest = $.ajax({
			method: "post",
			url: "../AJAX/ajax.php",
			data: { 
				name: that.attr('id'), 
				value: that.val(),
				isGroup: $('#groupregistrationcheck').prop('checked')  
			},
			dataType: 'text'
		});
		ajaxRequest.success(function(username_exists) {
			ajaxRequest = null;
			console.log(username_exists);
			if(username_exists == "1") {
				that.next().html("Username already exists.");
				that.next().removeClass('hidden');
				$('#register-submit').addClass('disabled');
				//disableRegister = true;
				hasRegisterError = true;
			} else {
				$('#register-submit').removeClass('disabled');
				hasRegisterError = false;
			}
		});
	});

	$('#username_login').keyup(function() {
		var that = $(this);
		if(that.val().trim().length < 5) return;
		abortExistingRequest(ajaxRequest);
		that.next().addClass('hidden');
		ajaxRequest = $.ajax({
			method: "post",
			url: "../AJAX/ajax.php",
			data: { 
				name: that.attr('id'), 
				value: that.val(),
				isGroup: $('#grouplogincheck').prop('checked')  
			},
			dataType: 'json'
		});
		ajaxRequest.success(function(response) {
			ajaxRequest = null;
			if(that.val() == "") return;
			if(!response.exists) {
				addError(that, "Username does not exist.");
				hasLoginError = true;
			} else {
				hasLoginError = false;
				if(response.signedIn == "true") {
					$('#signin-submit').addClass("disabled");
					//disableSignIn = true;
					$('#signout-submit').removeClass("disabled");
					//disableSignOut = false;
				} else {
					$('#signout-submit').addClass("disabled");
					//disableSignOut = true;
					$('#signin-submit').removeClass("disabled");
					//disableSignIn = false;
				}
			}
		});
	});

	/*$('.username').keyup(function() {
		var that = $(this);
		if(that.val().trim().length < 5) return;
		abortExistingRequest(ajaxRequest);
		that.next().addClass('hidden');
		ajaxRequest = $.ajax(ajaxObject(that));
		ajaxRequest.success(function(username_exists) {
			if(username_exists == "Error") {
				addError(that, "There is a database error. Please try at another time.");
				hasError = true;
			}
			ajaxRequest = null;
			if(that.val() == "") return;
			if(that.attr('id') == "username_registration" && username_exists) {
				that.next().html("Username already exists.");
				that.next().removeClass('hidden');
				$('#register-submit').addClass('disabled');
				$('#register-submit').attr('disabled', true);
			} else if(that.attr('id') == "username_login") {
				if(!username_exists) {
					addError(that, "Username does not exist.");
					hasError = true;
				} else {
					$('#signin-submit').removeClass("disabled");
					$('#signin-submit').removeAttr('disabled');
					$('#register-submit').removeClass('disabled');
					$('#register-submit').attr('disabled', false);
				}
			}
		});
	});*/

	$('#grouplogincheck').click(function() {
		$('#username_login').keyup();
		if(this.checked) {
			$('#grouplogin').removeClass('hidden');
			$('#username_login').attr("placeholder", "Group Name (Required)");
		} else {
			$('#grouplogin').addClass('hidden');
			$('#username_login').attr("placeholder", "Username (Required)");
		}
	});

	$('#groupregistrationcheck').click(function() {
		if(this.checked) {
			$('#username_registration').attr("placeholder", "Group Name (Required)");
		} else {
			$('#username_registration').attr("placeholder", "Username (Required)");
		}
		$('#username_registration').keyup();
	});

	$('#register-form').submit(function() {
		var that = $(this);
		var firstname = $('#firstname').val();
		var lastname = $('#lastname').val();
		if(firstname.length == 0) {
			addError($('#firstname'), "First Name is required.");
			hasRegisterError = true;
		}
		if(lastname.length == 0) {
			addError($('#firstname'), "First Name is required.");
			hasRegisterError = true;
		}
		$('#phonenumber').keyup();
		$('#username_registration').focusout();
		if(hasRegisterError) {
			hasRegisterError = false;
			return false;
		}
		$('#username_registration').keyup();
		if(hasRegisterError) {
			hasRegisterError = false;
			return false;
		}
		abortExistingRequest(ajaxRequest);
		ajaxRequest = $.ajax({
			method: "post",
			url: "../AJAX/ajax.php",
			data: {
				name: that.attr('name'),
				username: $('#username_registration').val(),
				firstname: $('#firstname').val(),
				lastname: $('#lastname').val(),
				email: $('#email').val(),
				phonenumber: $('#phonenumber').val(),
				findout: $('#findout').val(),
				categorychoice: categorychoice,
				isGroup: $('#groupregistrationcheck').prop('checked'),
				isUnder18: $('#ageregistrationcheck').prop('checked')
			},
			dataType: 'text'
		});
		ajaxRequest.success(function(msg) {
			ajaxRequest = null;
			if(msg) {
				$('#username_login').val($('#username_login').val());
				$('#signin-submit').removeClass('disabled');
				//disableSignIn = false;
				$('#login-success-msg').html("Thanks for registering!");
				$('#login-success-msg').removeClass('hidden');
				$('#login-form-link').click();
				resetRegistrationForm();
				resetLoginForm();

			}
		});
		return false;
	});

	$('#login-form').submit(function() {
		var that = $(this);
		abortExistingRequest(ajaxRequest);
		if(hasLoginError) {
			return false;
		}
		ajaxRequest = $.ajax({
			method: "post",
			url: "../AJAX/ajax.php",
			data: {
				name: that.attr('name'),
				username: $('#username_login').val(),
				isGroup: $('#grouplogincheck').prop('checked'),
				time: new Date().getTime()/1000,
				size: $('#numpeopleingroup').val()
			},
			dataType: 'text'
		});
		ajaxRequest.success(function(msg) {
			ajaxRequest = null;
			if(msg) {
				$('#username_login').val('');
				$('#login-success-msg').html(msg);
				$('#login-success-msg').removeClass('hidden');
				resetRegistrationForm();
				resetLoginForm();
			}
		});
		return false;

	});

	$('.btn').click(function() {
		if($(this).hasClass("disabled")) {
			return false;
		}
	});

	$('.categorychoice').click(function(){
		categorychoice = $(this).text();
		$('#categorydropdownchoice').html(categorychoice);
		if(categorychoice == "Other") {
			categorychoice = '';
		}
	});


});
