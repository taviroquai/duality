/**
 * User client
 */

'use strict';

var Client = {};

Client.routes = function (window, $) {

	$.getJSON('example/json', {get: 'test'}, function (data) {
		if (typeof console !== 'undefined') {
			console.log(data);
		}
	});

}

Client.init = function () {

	if (typeof window !== 'undefined' && typeof jQuery !== 'undefined') {
		jQuery(function($) {
			Client.routes(window, $);
		});
	}

};