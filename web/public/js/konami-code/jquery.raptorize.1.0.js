/*
 * jQuery Raptorize Plugin 1.0
 * www.ZURB.com/playground
 * Copyright 2010, ZURB
 * Free to use under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
*/


(function($) {

    $.fn.raptorize = function(options) {

        //Yo' defaults
        var defaults = {  
            enterOn: 'click', //timer, konami-code, click
            delayTime: 4000 //time before raptor attacks on timer mode
            };  
        
        //Extend those options
        var options = $.extend(defaults, options); 
	
        return this.each(function() {

			var _this = $(this);
			var audioSupported = true;
			//Stupid Browser Checking which should be in jQuery Support
			/*if ($.browser.mozilla && $.browser.version.substr(0, 5) >= "1.9.2" || $.browser.webkit) { 
				audioSupported = true;
			}*/
			
			//Raptor Vars
			//var raptorImageMarkup = '<img id="elRaptor" style="display: none" src="/public/img/raptor.png" />'
			var raptorImageMarkup = '<img id="ghosts_united" style="display: none" src="/public/img/konamiCodeV3.png" />';

			var raptorAudioMarkup = '<audio id="ghosts_united_anthem" preload="auto"><source src="/public/sound/ghost_sound2.mp3" /><source src="/public/sound/ghost_sound2.ogg" /></audio>';

			//var raptorImageMarkup = '<img id="elRaptor" style="display: none" src="/public/img/did.png" />'
			//var raptorAudioMarkup = '<audio id="elRaptorShriek" preload="auto"><source src="/public/sound/lustucru.mp3" /><source src="/public/sound/lustucru.mp3" /></audio>';
				
			var locked = false;
			
			//Append Raptor and Style
			$('body').append(raptorImageMarkup);
 			if(audioSupported) { $('body').append(raptorAudioMarkup); }
			var ghost = $('#ghosts_united').css({
				"position":"fixed",
				"bottom": "-700px",
				"right" : "0",
				"width" : "20%",
				"display" : "block"
			})
			
			// Animating Code
			function init() {
				locked = true;
			
				//Sound Hilarity
				if(audioSupported) { 
					function playSound() {
						document.getElementById('ghosts_united_anthem').play();
					}
					playSound();
				}
								
				// Movement Hilarity	
				ghost.animate({
					"bottom" : "0"
				}, function() { 			
					$(this).animate({
						"bottom" : "100px"
					}, 100, function() {
						var offset = (($(this).position().left)+400);
						$(this).delay(300).animate({
							"right" : offset
						}, 3200, function() {
							ghost = $('#ghosts_united').css({
								"bottom": "-700px",
								"right" : "0"
							})
							locked = false;
						})
					});
				});
			}
			
			
			//Determine Entrance
			if(options.enterOn == 'timer') {
				setTimeout(init, options.delayTime);
			} else if(options.enterOn == 'click') {
				_this.bind('click', function(e) {
					e.preventDefault();
					if(!locked) {
						init();
					}
				})
			} else if(options.enterOn == 'konami-code'){
			    var kkeys = [], konami = "38,38,40,40,37,39,37,39,66,65";
			    $(window).bind("keydown.ghostz", function(e){
			        kkeys.push( e.keyCode );
			        if ( kkeys.toString().indexOf( konami ) >= 0 ) {
			        	init();
			        	$(window).unbind('keydown.ghostz');
			        }
			    }, true);
	
			}
			
        });//each call
    }//orbit plugin call
})(jQuery);

