// GA Functions 1.4.2 http://murak.net/ MIT License

var gaFunc = gaFunc || {};

jQuery(function($) {
	var defaults = {
		tracker:'',
		outbound:true,
		outboundEvent:true,
		outboundAsync:false,
		outboundSelector:'a[href^="http"]:not([href*="//' + location.host + '"])',
		outboundPrefix:'/outbound/',
		download:true,
		downloadEvent:true,
		downloadAsync:false,
		downloadSelector:'a',
		downloadTypes:'pdf|zip|jpe?g|png|gif|mp\d?|mpe?g|flv|wmv|docx?|pptx?|xlsx?',
		downloadPrefix:'/download/',
		mailto:true,
		input:true,
		inputSelector:'form',
		inputFocus:false,
		inputChange:true,
		inputClick:true,
		inputReset:true,
		stay:false,
		staySec:60,
		scroll:true,
		scrollAsync:true,
		scrollClientRatio:150,
		xdomain:false,
		xdomainDomain:'othersite1.com|othersite2.com',
		locationSearch:false
	};
	
	var setting = $.extend(defaults, gaFunc);
	var atracker = setting.tracker ? setting.tracker + '.' : '';
	var loc = location.pathname + (setting.locationSearch ? location.search : '');
	
	var startDate = new Date();
	var startTime = startDate.getTime();
	var lastTime = startTime;
	
	function totalSec() {
			var nd = new Date();
			return parseInt((nd.getTime() - startTime) / 1000);
	}
	
	function eventSec() {
			var nd = new Date();
			var nt = nd.getTime();
			var t = parseInt((nt - lastTime) / 1000);
			lastTime = nt;
			return t;
	}
	
	function trackEvent(async, category, action, label, value) {
		if(async) {
			_gaq.push([atracker + '_trackEvent', category, action, label, value]);
		} else {
			try{
				var pageTracker = _gat._getTrackerByName(setting.tracker);
				pageTracker._trackEvent(category, action, label, value);
			} catch(err) {}
		}
	}
	
	function trackPageview(async, url) {
		if(async) {
			_gaq.push([atracker + '_trackPageview', url]);
		} else {
			try{
				var pageTracker = _gat._getTrackerByName(setting.tracker);
				pageTracker._trackPageview(url);
			} catch(err) {}
		}
	}
	
	// track outbound
	if(setting.outbound) {
		var q = setting.outboundSelector;
		if(setting.outboundEvent) {
			$(document).on('click',q, function() {
				var v = this.href.match(/\/\/([^\/]+)/)[1];
				trackEvent(setting.outboundAsync, 'Outbound', v, this.href, totalSec());
			});
		} else {
			$(document).on('click',q, function() {
				var u = setting.outboundPrefix + this.href.replace(/^.*\/\//, '');
				trackPageview(setting.outboundAsync, u);
			});
		}
	}
	
	// track downloads
	if(setting.download) {
		var regTypes = new RegExp('\.(' + setting.downloadTypes + ')$', 'i');
		$(setting.downloadSelector).each(function() {
			if(this.href.match(regTypes)) {
				if(setting.downloadEvent) {
					$(this).click(function() {
						var v = this.href.match(regTypes)[1].toUpperCase();
						trackEvent(setting.downloadAsync, 'Download', v, this.href, totalSec());
					});
				} else {
					$(this).click(function() {
						var u = setting.downloadPrefix + this.href.replace(/^.*\/\//, '');
						trackPageview(setting.downloadAsync, u);
					});
				}
			}
		});
	}
	
	// track mailto
	if(setting.mailto) {
		$('a[href^="mailto:"]').click(function() {
			trackEvent(true, 'Email', 'mailto', this.href);
		});
	}
	
	// track input
	if(setting.input) {
		var inputs = ':text,:password,:radio,:checkbox,:file,select,textarea';
		var q = $(setting.inputSelector);
		
		function inputFunc(event, ext, async) {
			var u = loc + '#' + ext.join('#');
			trackEvent(async, 'Input', event, u, eventSec());
		}
		
		if(setting.inputFocus) {
			q.find(inputs).focus(function(){
				inputFunc('focus', [$(this).attr('type') || this.tagName, this.id || this.name], true);
			});
		}
		if(setting.inputChange) {
			q.find(inputs).change(function(){
				inputFunc('change', [$(this).attr('type') || this.tagName, this.id || this.name], true);
			});
		}
		if(setting.inputClick) {
			q.find(':submit,:image,:button').click(function(){
				inputFunc('click', [$(this).attr('type') || this.tagName, this.id || this.name], true);
			});
		}
		if(setting.inputReset) {
			q.find(':reset').click(function(){
				inputFunc('reset', ['reset', this.id || this.name], true);
			});
		}
	}
	
	// track stay
	if(setting.stay && setting.staySec > 0) {
		setTimeout(function() {
			trackEvent(true, 'Reading', 'stay', loc, totalSec());
		}, setting.staySec * 1000);
	}
	
	// track scroll
	if(setting.scroll) {
		var scrollMax = 0;
		
		$(window).scroll(function() {
			var st = $(window).scrollTop();
			var ch = $('html').attr('clientHeight');
			if(!ch) ch = $(window).height();
			var sb = st + ch;
			
			// record scroll max
			if(sb > scrollMax) { scrollMax = sb; }
		});
		
		$(window).unload(function() {
			var sq = $('html');
			var ch = sq.attr('clientHeight');
			if(!ch) ch = $(window).height();
			
			// track scroll
			if(setting.scrollClientRatio > 0 && scrollMax * 100 >= ch * setting.scrollClientRatio) {
				var sh = sq.attr('scrollHeight');
				if(!sh) sh = $(document).height();
				if(sh) {
					var r = parseInt(scrollMax / sh * 100);
					trackEvent(setting.scrollAsync, 'Reading', 'scroll', loc, r);
				}
			}
		});
	}
	
	// cross domain link
	if(setting.xdomain) {
		var d = setting.xdomainDomain.split('|');
		var q = [];
		for(var i = 0; i < d.length; i++) {
			q.push('a[href*="'  + d[i] + '"]');
		}
		var g = setting.xdomainDomain.replace(/\./g, '\\.');
		$(q.join(',')).click(function() {
			if(this.href.match(RegExp('^https?://[^/]*(' + g + ')'))) {
				var pageTracker = _gat._getTrackerByName(setting.tracker);
				this.href = pageTracker._getLinkerUrl(this.href);
			}
		});
	}
});
