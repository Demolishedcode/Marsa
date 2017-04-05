$(document).ready(function() {
	// Fade In Page Up Button
	var $window = $(window),
		$page = $('html, body'),
		$pageUp = $('.page-up'),
		scrollTop = $window.scrollTop(),
		userHand = null,
		screenWidth = $window.width();

	// When Clicked Animate to Top
	$pageUp.on('click', function() {
		$page.animate({
			scrollTop: 0
		}, 600, 'easeOutCubic');
	});

	// Show email adress
	$('.contact-buttons a:last-of-type img').on('click', function() {
		$('.contact-view p').fadeIn();
	});


	// User Experience "Left or Right Handed"
	var $notification = $('.ui-notification'),
		$notiButton = $('.ui-buttons div'),
		$menuButton = $('.menu-button'),
		$menuClose = $('.menu-close'),
		$navMenu = $('.nav-menu'),
		$nav = $('nav');


	function navSetup() {
		$navMenu.css('display', 'none');

		if (userHand == 'right') {
			$navMenu.css('margin-left', screenWidth);
		} else if (userHand == 'left') {
			$navMenu.css('margin-left', '-200px');
			// Set pageUp to right side
			$pageUp.css('right', 'calc(100% - 50px)');
		}
	};

	function navClose() {
		$pageOver.fadeOut();
		if (userHand == 'right') {
			$navMenu.animate({
				marginLeft: screenWidth
			});
		} else if (userHand == 'left') {
			$navMenu.animate({
				marginLeft: "-200px"
			});
		}

		$navMenu.css('display', 'block');
	};

	// Give Navigation new place
	$menuButton.on('click', function() {
		$navMenu.css('display', 'block');
		$pageOver.fadeIn();

		if (userHand == "right") {
			$navMenu.animate({
				marginLeft: screenWidth - 200
			});
		} else if (userHand == 'left') {
			$navMenu.animate({
				marginLeft: '0px'
			});
		}
	});

	$menuClose.on('click', function() {
		navClose();
	});

	// Show correct feature create on hover
	var $feature = $('.feature-container'),
		$fcreate = $('.feature-create');

	$feature.focus().hover(function() {
		$(this).find($fcreate).css('display', 'block');
	}, function() {
		$(this).find($fcreate).css('display', 'none');
	});

	// Slideshow
	var slideNumber = 1,
		slideWidth = -$('.slider').outerWidth(),
		$slideContainer = $('.slider-inner');

	// Set the indicators
	function changeIndicator(number) {
		$('.indicator').css({
			backgroundColor: 'rgba(0,0,0,0)',
			borderColor: 'white'
		});
		$(".indicator:nth-child(" + number + ")").animate({
			backgroundColor: '#e52d27',
			borderColor: '#e52d27'
		});
	}

	// Control the slideshow
	function slideShow(slide) {
		if (slide != null) {
			slideNumber = slide;
		}

		switch (slideNumber) {
			case 1:
				$slideContainer.animate({
					marginLeft: '0px'
				}, 600);
				changeIndicator(slideNumber);
				slideNumber = 2;
				break;
			case 2:
				$slideContainer.animate({
					marginLeft: slideWidth + 'px'
				}, 600);
				changeIndicator(slideNumber);
				slideNumber = 3;
				break;
			case 3:
				$slideContainer.animate({
					marginLeft: 2 * slideWidth + 'px'
				}, 600);
				changeIndicator(slideNumber);
				slideNumber = 1;
				break;
		}
	}

	slideShow();

	// Make the slideshow slide every 2 seconds
	var myInterval = setInterval(function() {
			slideShow();
		}, 5000),
		$indicator = $('.indicator');

	$indicator.click(function() {
		var slide = $(this).data('slide');
		clearInterval(myInterval);
		myInterval = setInterval(function() {
			slideShow();
		}, 5000);
		slideNumber = slide;
		slideShow(slideNumber);
	});

	// When User Scrolls
	$window.scroll(function() {
		scrollTop = $window.scrollTop();
		// Set navigation background
		if (scrollTop >= 50 && $nav.height() == 120) {
			$nav.stop().animate({
				height: "90px",
				backgroundColor: "rgba(0,0,0,0.8)"
			});
		} else if (scrollTop < 50 && $nav.height() == 90) {
			$nav.stop().animate({
				height: "120px",
				backgroundColor: "rgba(0,0,0,0)"
			});
		}

		// For checking if pageUp sould be showed
		scrollTop > 50 ? $pageUp.fadeIn() : $pageUp.fadeOut();

		// User spy show where user is on page
		$('.nav-menu li, .content-menu li').css('color', 'white');

		if (scrollTop + windowHeight >= offsetSupport) {
			$('.nav-menu li:nth-child(4), .content-menu li:last-child').css('color', '#ff5700');
		} else if (scrollTop + windowHeight >= offsetCreators) {
			$('.nav-menu li:nth-child(3), .content-menu li:nth-child(2)').css('color', '#ff5700');
		} else if (scrollTop + windowHeight >= offsetFeatures) {
			$('.nav-menu li:nth-child(2), .content-menu li:first-child').css('color', '#ff5700');
		} else {
			$('.nav-menu li, .content-menu li').css('color', 'white');
		}
	});

	// When menu button is clicked move to correct position
	var offsetFeatures = $('.features').offset().top + 200,
		offsetCreators = $('.creators').offset().top + 200,
		offsetSupport = $('footer').offset().top + 200;

	$('.nav-menu li, .content-menu li, .about-button, .down').click(function() {
		var menu = $(this).data('menu');

		switch (menu) {
			case 1:
				$page.animate({
					scrollTop: offsetFeatures - 300
				}, 600, 'easeOutCubic');
				//window.scrollTo(0, offsetFeatures - 300);
				break;
			case 2:
				$page.animate({
					scrollTop: offsetCreators - 300
				}, 600, 'easeOutCubic');
				//window.scrollTo(0, offsetCreators - 300);
				break;
			case 3:
				$page.animate({
					scrollTop: offsetSupport - 300
				}, 600, 'easeOutCubic');
				//window.scrollTo(0, offsetSupport - 300);
				break;
		}

		navClose();
	});

	// Show notification if screen size is smaller then 790px
	var notiCheck = false,
		oneSetup = false,
		$pageOver = $('.page-overlay');

	// Get The hand the user is using
	$notiButton.on('click', function() {
		// Get which hand user is using
		var $this = $(this);
		userHand = $this.data('hand');

		// Close the pop up
		$notification.slideUp();
		$pageOver.fadeOut();
		$pageOver.css('z-index', '9');
		navSetup();
	});

	if (screenWidth < 790) {
		$pageOver.fadeIn();
		$notification.fadeIn();
		notiCheck = true;
	}

	var windowHeight = $(window).height();

	// When user resize the window
	$window.on('resize', function() {
		windowHeight = $(window).height();

		//navClose();

		// Change variables on resize
		// Keep menu offsets up-to-date
		offsetFeatures = $('.features').offset().top + 200;
		offsetCreators = $('.creators').offset().top + 200;
		offsetSupport = $('footer').offset().top + 200;
		// Reset slider variables for responsivness
		slideWidth = -$('.slider').outerWidth();
		slideNumber = 1;
		$slideContainer.css('margin-left', '0px');
		screenWidth = $window.innerWidth();

		if (screenWidth <= 772) {
			if (!notiCheck) {
				$pageOver.fadeIn();
				$notification.fadeIn();
			}

			if (!oneSetup) {
				navSetup();
			}

			oneSetup = notiCheck = true;
		} else if (screenWidth > 772) {
			$navMenu.css('display', 'block');
			$navMenu.css('margin-left', '0');
			$page.css('margin-left', '0');
			$pageUp.css('right', '10px');
			$pageOver.fadeOut();
			$notification.fadeOut();

			oneSetup = false;
		}

		if (windowHeight < $('header').height()) {
			$('.page-content').css('top', '0px');
			$('header').css('position', 'relative');
		} else {
			$('.page-content').css('top', $('header').height());
			$('header').css('position', 'fixed');
		}
	});

	if (windowHeight < $('header').height()) {
		$('.page-content').css('top', '0px');
		$('header').css('position', 'relative');
	} else {
		$('.page-content').css('top', $('header').height());
		$('header').css('position', 'fixed');
	}
});
