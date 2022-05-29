(function( $ ) {

	$(document).on('ready', function () {
		var header = document.getElementById("wpcontent");
		var sticky = header.offsetTop;

		window.onscroll = function() {
			if (window.pageYOffset > sticky) {
				$('#wa-notifier-admin-header').addClass('sticky');
			} else {
				$('#wa-notifier-admin-header').removeClass('sticky');
			}
		};
	});

})( jQuery );
