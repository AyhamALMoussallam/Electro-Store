(function($) {
	"use strict"

	// Mobile Nav toggle
	$('.menu-toggle > a').on('click', function (e) {
		e.preventDefault();
		$('#responsive-nav').toggleClass('active');
	})

	// Fix cart dropdown from closing
	$('.cart-dropdown').on('click', function (e) {
		e.stopPropagation();
	});

	/////////////////////////////////////////

	// Products Slick
	$('.products-slick').each(function() {
		var $this = $(this),
				$nav = $this.attr('data-nav');

		$this.slick({
			slidesToShow: 4,
			slidesToScroll: 1,
			autoplay: true,
			infinite: true,
			speed: 300,
			dots: false,
			arrows: true,
			appendArrows: $nav ? $nav : false,
			responsive: [{
	        breakpoint: 991,
	        settings: {
	          slidesToShow: 2,
	          slidesToScroll: 1,
	        }
	      },
	      {
	        breakpoint: 480,
	        settings: {
	          slidesToShow: 1,
	          slidesToScroll: 1,
	        }
	      },
	    ]
		});
	});

	// Products Widget Slick
	$('.products-widget-slick').each(function() {
		var $this = $(this),
				$nav = $this.attr('data-nav');

		$this.slick({
			infinite: true,
			autoplay: true,
			speed: 300,
			dots: false,
			arrows: true,
			appendArrows: $nav ? $nav : false,
		});
	});

	/////////////////////////////////////////

	window.initProductImageSliders = function () {
		if (!$('#product-imgs').length || !$('#product-imgs .product-preview').length) {
			return;
		}

		var $main = $('#product-main-img');
		var $thumbs = $('#product-imgs');

		if ($main.hasClass('slick-initialized')) {
			$main.find('.product-preview').trigger('zoom.destroy');
			$main.slick('unslick');
		}

		if ($thumbs.hasClass('slick-initialized')) {
			$thumbs.slick('unslick');
		}

		// Thumbnails first so asNavFor syncs the active slide on load
		$thumbs.slick({
			slidesToShow: 3,
			slidesToScroll: 1,
			arrows: true,
			centerMode: true,
			focusOnSelect: true,
			centerPadding: 0,
			vertical: true,
			initialSlide: 0,
			asNavFor: '#product-main-img',
			responsive: [{
				breakpoint: 991,
				settings: {
					vertical: false,
					arrows: false,
					dots: true,
				}
			}],
		});

		$main.slick({
			infinite: true,
			speed: 300,
			dots: false,
			arrows: false,
			fade: true,
			initialSlide: 0,
			asNavFor: '#product-imgs',
		});

		$main.slick('setPosition');
		$thumbs.slick('setPosition');
		$main.slick('slickGoTo', 0, true);
		$thumbs.slick('slickGoTo', 0, true);

		function initActiveZoom() {
			$main.find('.product-preview').trigger('zoom.destroy');
			$main.find('.slick-slide.slick-active .product-preview').zoom();
		}

		initActiveZoom();

		$main.off('afterChange.productZoom').on('afterChange.productZoom', function () {
			initActiveZoom();
		});
	};

	/////////////////////////////////////////

	// Input number
	$('.input-number').each(function() {
		var $this = $(this),
		$input = $this.find('input[type="number"]'),
		up = $this.find('.qty-up'),
		down = $this.find('.qty-down');

		down.on('click', function () {
			var value = parseInt($input.val()) - 1;
			value = value < 1 ? 1 : value;
			$input.val(value);
			$input.change();
			updatePriceSlider($this , value)
		})

		up.on('click', function () {
			var value = parseInt($input.val()) + 1;
			$input.val(value);
			$input.change();
			updatePriceSlider($this , value)
		})
	});

	var priceInputMax = document.getElementById('price-max'),
			priceInputMin = document.getElementById('price-min');

	priceInputMax.addEventListener('change', function(){
		updatePriceSlider($(this).parent() , this.value)
	});

	priceInputMin.addEventListener('change', function(){
		updatePriceSlider($(this).parent() , this.value)
	});

	function updatePriceSlider(elem , value) {
		if ( elem.hasClass('price-min') ) {
			console.log('min')
			priceSlider.noUiSlider.set([value, null]);
		} else if ( elem.hasClass('price-max')) {
			console.log('max')
			priceSlider.noUiSlider.set([null, value]);
		}
	}

	// Price Slider
	var priceSlider = document.getElementById('price-slider');
	if (priceSlider) {
		noUiSlider.create(priceSlider, {
			start: [1, 999],
			connect: true,
			step: 1,
			range: {
				'min': 1,
				'max': 999
			}
		});

		priceSlider.noUiSlider.on('update', function( values, handle ) {
			var value = values[handle];
			handle ? priceInputMax.value = value : priceInputMin.value = value
		});
	}

})(jQuery);
