/**
 * jQuery Multiple Elements Cycle Plugin.
 *
 * Provides a simple preview scrolling panel. Shows the given range of li items from the middle and
 * allows scrolling left and right within the list. Does not handle automatic scrolling / time based
 * or wrapping items around. 
 *
 * Note $.multipleElementsCycle needs to be called on a div containing a ul rather then the actual ul
 *
 * Copyright 2011, Will Rossiter <will.rossiter@gmail.com>
 * Released under the BSD License.
 *
 * Version: 1.0
 */
(function($) {
	$.fn.multipleElementsCycle = function(opts){
		
		/**
		 * Setup default configuration options. To override any of these
		 * options pass in the object to the multipleElementsCycle call
		 * 
		 * $("#container").multipleElementsCycle({
		 *		show: 5,
		 *		container: ".test"
		 * });
		 */
		var defaults = {
			container: '#cycle',	// Selector for element (ul) container (selector)
			prev: '#cycle-prev',	// Selector to scroll previous (selector)
			next: '#cycle-next', 	// Selector to scroll next (selector)
			speed: 500,				// Speed to scroll elements (int)
			containerSize: false,	// Override default size (int, px)
			show: 4,				// Items to show from the list (int)
			start: false,			// Override the start with a defined value (int)
			jumpTo: false,			// Selectors to use as jump list
			vertical: false,		// Whether Scroll is for vertical
			scrollCount: 1,			// How many elements to scroll when clicking next / prev,
			element: "li",			// Element which is cycled. Update this and parent (selector)
			parent: "ul"			// Parent element which contains the elements (selector)
		};
		
		var opts = $.extend(defaults, opts);
				
		return this.each(function() {
			var self = $(this);
			
			var elements = self.find(opts.element);
			var maxIndex = elements.length - 1;
			
			// Calculate the start index. It will either work it out automatically based
			// on the length of the list or use the provided opts.start value
			var lowerIndex = (opts.start === false) ? Math.floor((maxIndex - opts.show + 1) / 2) : opts.start;
			var upperIndex = lowerIndex + opts.show;
			var size = (opts.vertical === false) ? elements.outerWidth(true) : elements.outerHeight(true);
			var margin = ((lowerIndex) * size) * -1;

			// Hide the arrows if we are at the bounds
			if(upperIndex >= elements.length) $(opts.next).hide();
			if(lowerIndex <= 0) $(opts.prev).hide(); 
			
			if(opts.vertical === false) {
				$(this).find(opts.container).css({
					width: (opts.containerSize) ? opts.containerSize : size * opts.show,
					overflow: 'hidden'
				});
				
				$(this).find(opts.parent).css({
					width: (elements.length) * size,
					padding: '0'
				});
				
				$(opts.parent, self).animate({
					marginLeft: margin
				}, opts.speed);
			}
			else {
				$(this).find(opts.container).css({
					height: (opts.containerSize) ? opts.containerSize : size * opts.show,
					overflow: 'hidden'
				});
				
				$(this).find(opts.parent).css({
					height: (elements.length) * size,
					padding: '0'
				});
				
				$(opts.parent, self).animate({
					marginTop: margin
				}, opts.speed);
			}
	
			var cycle = {
				next: function() {
					if(upperIndex <= maxIndex) {
						$(opts.prev).show();

						var count = ((upperIndex+opts.scrollCount) > maxIndex) ? elements.length-upperIndex : opts.scrollCount;
			
						margin = margin - (size * count);
						upperIndex = upperIndex + count;
						lowerIndex = lowerIndex + count;
						
						if(opts.vertical === false) {
							$(opts.parent, self).animate({
								marginLeft: margin
							},opts.speed);
						}
						else {
							$(opts.parent, self).animate({
								marginTop: margin
							},opts.speed);
						}
						
						if(upperIndex > maxIndex) $(opts.next).hide();
					}
				},
				prev: function() {
					if(lowerIndex >= 0) {
						$(opts.next).show();	
						
						var count = ((lowerIndex-opts.scrollCount) < 0) ? lowerIndex : opts.scrollCount;
						
						upperIndex = upperIndex - count;
						lowerIndex = lowerIndex - count;
						margin = margin + (size * count);
						
						if(opts.vertical === false) {
							$(opts.parent, self).animate({
								marginLeft: margin
							}, opts.speed);
						}
						else {
							$(opts.parent, self).animate({
								marginTop: margin
							}, opts.speed);
						}
						if((lowerIndex-1) < 0) $(opts.prev).hide();
					}
				},
				toPoint: function(pos) {
					var oldUpper = upperIndex;
					
					if(pos == 0) {
						// jump to end
						upperIndex = maxIndex + 1;
						lowerIndex = upperIndex - opts.show;
					}
					else if(pos < 0) {
						// offset from end
						upperIndex = maxIndex + parseInt(pos);
						lowerIndex = lowerIndex + parseInt(pos);
					}
					else {
						// offset from start
						lowerIndex = pos - 1;
						upperIndex = lowerIndex + opts.show;
					}
					// if the upper index is 
					margin = margin + (size * (oldUpper-upperIndex));
					
					if(opts.vertical === false) {
						$(opts.parent, self).animate({
							marginLeft: margin
						},opts.speed);
					}
					else { 
						$(opts.parent, self).animate({
							marginTop: margin
						}, opts.speed);
					}
					
					if(upperIndex >= maxIndex) $(opts.next).hide();
					else $(opts.next).show();
					
					if(lowerIndex == 0) $(opts.prev).hide();
					else $(opts.prev).show();
				}
			};
			
			$(opts.next).live('click', function(e) { 
				cycle.next();
				
				e.preventDefault();
			});
			
			$(opts.prev).live('click', function(e) { 
				cycle.prev(); 
				
				e.preventDefault();
			});

			if(opts.jumpTo) {
				$(opts.jumpTo).live('click', function(e) { 
					cycle.toPoint($(this).data('position')); 
					
					e.preventDefault();
				});
			}
		});	
	}
})(jQuery);