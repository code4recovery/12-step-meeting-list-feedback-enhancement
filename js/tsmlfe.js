/*
	Javascript functions for Enhanced Feedback Single Meetings template
*/

function switchVisible(approximate) {
	var map_container = document.getElementById('map');
	var requests_container = document.getElementById('requests');

	if (map_container.style.display == 'none') {
		if (approximate == 'no') {
			map_container.style.display = 'block';
		}
		requests_container.style.display = 'none';
	}
	else {
		if (approximate == 'no') {
			map_container.style.display = 'none';
		}
		requests_container.style.display = 'block';
	}
}

function setRequestHeaderDisplay(x) {
	var anmrf_title = document.getElementById('anmrf_title');
	var mcrf_title = document.getElementById('mcrf_title');
	var mrrf_title = document.getElementById('mrrf_title');

	if (x == 'mrrf_title') {
		anmrf_title.style.display = 'none';
		mcrf_title.style.display = 'none';
		mrrf_title.style.display = 'block';

		document.getElementById('add_new_request').style.display = 'none';
		document.getElementById('change_request').style.display = 'none';

		document.getElementById('submit_change').style.display = 'none';
		document.getElementById('submit_new').style.display = 'none';
		document.getElementById('submit_remove').style.display = 'block';

	} else if (x === 'anmrf_title') {
		anmrf_title.style.display = 'block';
		mcrf_title.style.display = 'none';
		mrrf_title.style.display = 'none';

		document.getElementById('add_new_request').style.display = 'block';
		document.getElementById('change_request').style.display = 'none';

		document.getElementById('submit_change').style.display = 'none';
		document.getElementById('submit_new').style.display = 'block';
		document.getElementById('submit_remove').style.display = 'none';

	} else if (x === 'mcrf_title') {
		anmrf_title.style.display = 'none';
		mcrf_title.style.display = 'block';
		mrrf_title.style.display = 'none';

		document.getElementById('add_new_request').style.display = 'none';
		document.getElementById('change_request').style.display = 'block';

		document.getElementById('submit_change').style.display = 'block';
		document.getElementById('submit_new').style.display = 'none';
		document.getElementById('submit_remove').style.display = 'none';

	}
}

function toggleAdditionalInfoDisplay(x, y) {
	var x, y;   // function scope vars

	// search for elements just once
	x = document.getElementById(x);

	if (y == "show") {
		x.style.display = "block";
	}
	else {
		x.style.display = "none";
	}
}

function inactive(x) {
	x = document.getElementById(x);
	x.value = + " (Inactive)";
}