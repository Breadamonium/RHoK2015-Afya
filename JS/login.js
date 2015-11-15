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

	$('#currTime').on('click', function () {
		var today = new Date();
		var hour = today.getHours();
		var min = today.getMinutes();


		$('#time').val(""+hour+":"+min);
	});

	var ajaxRequest = null;

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

	$('#username_registration').focusout(function() {
		var that = $(this);
		if(that.val().trim().length < 5 && that.val().trim().length > 0) {
			that.next().html("Username needs be at least 5 characters.")
			that.next().removeClass('hidden');
		}
	});

	$('.username').keyup(function() {
		var that = $(this);
		if(that.val().trim().length < 5) return;
		abortExistingRequest(ajaxRequest);
		ajaxRequest = $.ajax(ajaxObject(that));
		ajaxRequest.success(function(username_exists) {
			console.log(username_exists);
			if(that.val() == "") return;
			if(that.attr('id') == "username_registration" && username_exists) {
				that.next().html("Username already exists.")
				that.next().removeClass('hidden');
			} else if(that.attr('id') == "username_login") {
				if(!username_exists) {
					that.next().html("Username does not exist");
					that.next().removeClass('hidden');
				} else {
					$('#signin-submit').removeClass("disabled");
					$('#signin-submit').removeAttr('disabled');
				}
			}
		});
	});

	$('#grouplogincheck').click(function() {
		if(this.checked) {
			$('#grouplogin').removeClass('hidden');
		} else {
			$('#grouplogin').addClass('hidden');
		}
	});

	$('#register-form').submit(function() {
		var that = $(this);
		abortExistingRequest(ajaxRequest);
		ajaxRequest = $.ajax({
			name: that.attr('name'),
			firstname: $('#firstname').val(),
			lastname: $('#lastname').val(),
			email: $('#email').val(),
			phonenumber: $('#phonenumber').val(),
			isGroup: $('#groupregistrationcheck').prop('checked')
		});
		ajaxRequest.success(function(msg) {
			if(msg) {
				$('#username_login').val($('#username_login').val());
				$('#signin-submit').removeClass('disabled');
				$('#signin-submit').removeAttr('disabled');
				$('#login-success-msg').removeClass('hidden');
				$('#login-form-link').click();

			}
		});
		return false;
	});


});
