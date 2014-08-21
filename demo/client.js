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

		// Check if there is a console
		if (typeof console !== 'undefined') {

			// Show server response in console
			console.log(data);
		}
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