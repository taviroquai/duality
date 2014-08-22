/**
 * Example Duality Client
 */

// Do not call anything from outside!
'use strict';

// Create a client namespace
var Client = {};

// Define client routes configuration
Client.routes = function (window, $) {

	// Just call server at /example/json
	$.getJSON('example/json', {get: 'test'}, function (data) {

		// Add data to document
		$('#container').append('<h4>' + data.msg + '</h4>');
		$('#container').append('<table />');
		$.each(data.items, function (i, item) {
			$('#container table').append('<tr><td>' + item.id + '</td><td>' + item.email + '</td></tr>');
		});
	});
}

// Define client initialization
Client.init = function () {

	// Check if we are on a browser environment
	if (typeof window !== 'undefined' && typeof jQuery !== 'undefined') {

		// Call jQuery on document ready
		jQuery(function($) {

			// Call client routes configuration
			Client.routes(window, $);
		});
	}
};