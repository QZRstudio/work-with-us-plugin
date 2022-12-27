jQuery(document).ready(function ($) {

	/* 
		We prefer to use Vue.js for this, but we make it work with jQuery
		to keep it simple for a lighter version of this plugin. 
		We usually don't use jQuery anymore, especially in the backend, 
		that's because is not very reactive and it is very verbose.
	*/

	// When the value of tag selector change, show/hide the right CTA-editor
	$('select[name="tag"]').change(function () {
		let value = $(this).val();
		changeTag(value);
	});
	changeTag($('select[name="tag"]').val());

	// This will update all the CTA-editor with the right value, and also the url
	function changeTag(value) {
		if (value == -1) {
			$('input[type="submit"]').addClass('disabled');
		}
		else {
			$('input[type="submit"]').removeClass('disabled');
		}
		$('.cta-editor').removeClass('active');
		$('.cta-settings').removeClass('active');
		$('div[data-option="settings"]').removeClass('dashicons-no-alt').addClass('dashicons-admin-generic');
		$('.cta-editor[data-tag-id="' + value + '"]').addClass('active');
		let statusValue = $('#cta-status-' + value);
		// check if statusValue is checked
		if (statusValue.is(':checked')) {
			$('.cta-editor[data-tag-id="' + value + '"] .current-status').addClass('active');
		} else {
			$('.cta-editor[data-tag-id="' + value + '"] .current-status').removeClass('active');
		}

		// Updating the urls for the form and in the browser, so at the refresh we don't lose the selected tag
		$('.wrap form').attr('action', 'options-general.php?page=ilpost-plugin&selected=' + value);
		window.history.pushState("", "", 'options-general.php?page=ilpost-plugin&selected=' + value);
	}

	// On change the status of the CTA, update the status of the CTA-editor and re-render everything
	$('.status').click(function () {
		changeTag($('select[name="tag"]').val());
	});

	// The advacned settings button, on click will show/hide the settings and also change the icon. 
	// A LOT OF CODE FOR A SIMPLE BUTTON, but it works :D 
	$('div[data-option="settings"]').click(function () {
		currentElement = $('.cta-editor.active .cta-settings');
		if (currentElement.hasClass('active')) {
			currentElement.removeClass('active');
			$(this)
				.removeClass('dashicons-no-alt')
				.addClass('dashicons-admin-generic');

		} else {
			currentElement.addClass('active');
			$(this)
				.addClass('dashicons-no-alt')
				.removeClass('dashicons-admin-generic');
		}
	});

});