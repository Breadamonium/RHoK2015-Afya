//Assumes JQuery will be included

var existingAjaxRequest = null;
var dataAjaxHanderURL = 'getData.php';

function abortExistingAjaxRequest() {
	if(existingAjaxRequest != null) {
		existingAjaxRequest.abort();
	}
}


//Each function returns an AJAX object. Need to call the success() handler to get data.

function get_user_hours(firstname, lastname) {
	abortExistingAjaxRequest();
	existingAjaxRequest = $.ajax({
		method: 'post',
		url: dataAjaxHanderURL,
		data: {
			name: 'get_user_hours',
			firstname: firstname,
			lastname: lastname
		},
		dataType: 'json'
	});
	return existingAjaxRequest;
}

function get_group_hours(groupname) {
	console.log(groupname);
	abortExistingAjaxRequest();
	existingAjaxRequest = $.ajax({
		method: 'post',
		url: dataAjaxHanderURL,
		data: {
			name: 'get_group_hours',
			groupname: groupname
		},
		dataType: 'json'
	});
	return existingAjaxRequest;
}

function get_aggregate_user_hours(startdate, enddate) {
	abortExistingAjaxRequest();
	existingAjaxRequest = $.ajax({
		method: 'post',
		url: dataAjaxHanderURL,
		data: {
			name: 'get_aggregate_user_hours',
			startdate: startdate,
			enddate: enddate
		},
		dataType: 'json'
	});
	return existingAjaxRequest;
}

function get_aggregate_group_hours(startdate, enddate) {
	abortExistingAjaxRequest();
	existingAjaxRequest = $.ajax({
		method: 'post',
		url: dataAjaxHanderURL,
		data: {
			name: 'get_aggregate_group_hours',
			startdate: startdate,
			enddate: enddate
		},
		dataType: 'json'
	});
	return existingAjaxRequest;
}

function get_user_timesheet(firstname, lastname, startdate, enddate) {
	abortExistingAjaxRequest();
	existingAjaxRequest = $.ajax({
		method: 'post',
		url: dataAjaxHanderURL,
		data: {
			name: 'get_user_timesheet',
			firstname: firstname,
			lastname: lastname,
			startdate: startdate,
			enddate: enddate
		},
		dataType: 'json'
	});
	return existingAjaxRequest;
}

function get_group_timesheet(groupname, startdate, enddate) {
	abortExistingAjaxRequest();
	existingAjaxRequest = $.ajax({
		method: 'post',
		url: dataAjaxHanderURL,
		data: {
			name: 'get_group_timesheet',
			groupname: groupname,
			startdate: startdate,
			enddate: enddate
		},
		dataType: 'json'
	});
	return existingAjaxRequest;
}

function get_user_info(firstname, lastname) {
	abortExistingAjaxRequest();
	existingAjaxRequest = $.ajax({
		method: 'post',
		url: dataAjaxHanderURL,
		data: {
			name: 'get_user_info',
			firstname: firstname,
			lastname: lastname
		},
		dataType: 'json'
	});
	return existingAjaxRequest;
}


