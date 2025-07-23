(function ($) {
  "use strict";

	 // Menu scrollspy
    $('body').scrollspy({ target: '#navbarmain' });

    
 $(window).scroll(function () {
    var window_top = $(window).scrollTop() + 1;
    if (window_top > 50) {
      $('.main_menu,.main_menu_2').addClass('menu_fixed animated fadeInDown');
    } else {
      $('.main_menu,.main_menu_2').removeClass('menu_fixed animated fadeInDown');
    }
  });


          /*
     * ----------------------------------------------------------------------------------------
     *  SMOTH SCROOL JS
     * ----------------------------------------------------------------------------------------
     */

    $('a.smoth-scroll').on('click', function (e) {
        var anchor = $(this);
        $('html, body').stop().animate({
            scrollTop: $(anchor.attr('href')).offset().top - 0
        }, 1000);
        e.preventDefault();
    });


//  Testimonial Slider
	$('.testimonial-wrap').slick({
		slidesToShow: 1,
		infinite: true,
		arrows: false,
		autoplay: true,
		autoplaySpeed: 5000
	});


// Portfolio Popup

  $('.portfolio').each(function () {
        $(this).find('.popup-gallery').magnificPopup({
            type: 'image',
            gallery: {
                enabled: true
            }
        });
    });


	//  Map
  
	var map;

	function initialize() {
		var mapOptions = {
			zoom: 13,
			center: new google.maps.LatLng(50.97797382271958, -114.107718560791)
			// styles: style_array_here
		};
		map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
	}

	var google_map_canvas = $('#map-canvas');

	if (google_map_canvas.length) {
		google.maps.event.addDomListener(window, 'load', initialize);
	}



}(jQuery));