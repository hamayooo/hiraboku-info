$(function () {

	// Toggle Menu
	// -----------------------------------------------------------------------
	var currentScroll;
	$('.gnav-toggle').click(function () {
		$(this).toggleClass('is-active');
		$('.gnav').toggleClass("is-active");
		if ($('.gnav-toggle').hasClass('is-active')) {
			currentScroll = $(window).scrollTop();
			$('.wrap').css({
				position: 'fixed',
				width: '100%',
				top: -1 * currentScroll
			});
			$('.gnav-toggle .text small').text("CLOSE");
		} else {
			$('.wrap').attr({
				style: ''
			});
			$('.gnav-toggle .text small').text("MENU");
			$(window).scrollTop(currentScroll);
		}
	});



	// fix header
	// -----------------------------------------------------------------------
	// $(window).on("load resize", function () {
	// 	var headerTop = $(".header").offset().top;
	// 	var headerHeight = $(".header").outerHeight();
	// 	$(window).on("scroll", function () {
	// 		var scrollTop = $(window).scrollTop();
	// 		if (scrollTop > headerTop) {
	// 			$('.header').addClass("fixed");
	// 		} else {
	// 			$('.header').removeClass("fixed");
	// 		}
	// 	});
	// });



	$('a[href^="#"]').click(function () {
		var speed = 500;
		var href = $(this).attr("href");
		var target = $(href == "#" || href == "" ? 'html' : href);
		var position = target.offset().top;
		if ($(window).width() > 768) {
			position = position - $('header').outerHeight();
		}
		$("html, body").animate({
			scrollTop: position
		}, speed, "swing");
		return false;
	});


	$('.mv__slider').not('.slick-initialized').slick({
		autoplay: true,
		autoplaySpeed: 4500,
		speed: 1000,
		arrows: false,
		dots: false,
		fade: true,
		pauseOnHover: false,
		pauseOnFocus: false,
		// responsive: [
		//  {
		// 	breakpoint: 768, //767px以下のサイズに適用
		// 	settings: {
		// 		dots:false
		// 	}
		//  }
	 // ]
	});


	$('.store-slider').not('.slick-initialized').slick({
		autoplay: true,
		autoplaySpeed: 4500,
		speed: 1000,
		arrows: false,
		dots: true,
		fade: true,
		pauseOnHover: false,
		pauseOnFocus: false,
		appendDots: $('.dots'),
		customPaging: function(slick,index) {
				var bgImgFile = slick.$slides.eq(index).find('.slide .img').data("img");
				return '<div style="background: url(' + bgImgFile + ')center center/cover no-repeat;"></div>';
	  },
		responsive: [
	   {
	    breakpoint: 768, //767px以下のサイズに適用
	    settings: {
				dots: false,
				asNavFor: '.store-slider-thum',
	    }
	   }
	 ]
	});


	$('.store-slider-thum').not('.slick-initialized').slick({
		autoplay: true,
		autoplaySpeed: 4500,
		speed: 1000,
		arrows: false,
		dots: false,
		// fade: true,
		pauseOnHover: false,
		pauseOnFocus: false,
		centerMode: true,
		centerPadding: '26%',
		customPaging:false,
		asNavFor: '.store-slider',
	});


	$('.search__tab ul li').click(function(){
		var index = $(this).data('triger');
		$('.search__tab ul li').removeClass('active');
		$(this).addClass('active');

		// クリックしたタブと同じインデックス番号をもつコンテンツを表示
		$('.tab-content').removeClass('is-active');
		$('.tab-content').eq(index).addClass('is-active');
	});


	$('.gnav ul li .hover .box strong').click(function(){
		$(this).next('ul').slideToggle();
		$(this).toggleClass('is-active');
	});

	$('.box02 ul li .trigger').click(function(){
		$(this).next('ul').slideToggle();
		$(this).toggleClass('is-active');
	});

	$('#reset').click(function(){
		document.getElementById('searchform').reset();
	});



	$(document).ready(function(){
	  $(this).scrollTop(0);
	});


	$(window).on('load', function() {
		var url = $(location).attr('href');
		var urlParam = location.search.substring(1);
		var headerHeight = $('header').outerHeight();
		if(url.indexOf("#") != -1){
			var anchor = url.split("#");
			var target = $('#' + anchor[anchor.length - 1]);
			if(target.length){
				var pos = Math.floor(target.offset().top) - headerHeight;
				$("html, body").animate({scrollTop:pos}, 500);
			}
		} else if(urlParam){
			var target = $('#search-text');
			if(target.length){
				var pos = Math.floor(target.offset().top) - headerHeight;
				$("html, body").animate({scrollTop:pos}, 500);
			}
		}
	});




});
