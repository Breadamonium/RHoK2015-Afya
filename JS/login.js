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

	$('.username').keyup(function() {
		var that = $(this);
		abortExistingRequest(ajaxRequest);
		ajaxRequest = $.ajax(ajaxObject(that));
		ajaxRequest.success(function(username_exists) {
			if(that.val() == "") return;
			if(that.attr('id') == "username_registration" && username_exists) {
				that.next().removeClass('hidden');
			} else if(that.attr('id') == "username_login") {
				if(!username_exists) {
					that.next().removeClass('hidden');
				} else {
					$('#signin-submit').removeClass("disabled");
					$('#signin-submit').removeAttr('disabled');
				}
			}
		});
	});

	$('#register-form').submit(function() {
		var that = $(this);
		abortExistingRequest(ajaxRequest);
		ajaxRequest = $.ajax({
			name: that.attr('name')
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
	})


});
