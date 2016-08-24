function prepareToolTips() {
  $('.bubbleInfo').each(function () {
	  $(this).removeClass("bubbleInfo");
	   $(this).addClass("bubbleInfo-ready");
    // options
    var distance = 10;
    var time = 250;
    var hideDelay = 500;

    var hideDelayTimer = null;

	
    // tracker
    var beingShown = false;
    var shown = false;
    
    var trigger = $('.trigger', this);
    var popup = $('.popup', this).css('opacity', 0);




                        
		var template =			'<table class="popup" id="dpop-'+popup.attr("id")+'" ><tbody> \
                        	<tr> \
		    	    			<td class="corner" id="topleft"></td> \
				        		<td class="top"></td> \
				        		<td class="corner" id="topright"></td> \
			    	    	</tr> \
				        	<tr> \
				        		<td class="tt-left"></td> \
				        		<td style="background-color: #fff;" class="content">'+popup.html()+'</td> \
				        		<td class="tt-right"></td> \
				        	</tr> \
				        	<tr> \
				        		<td id="bottomleft" class="corner"></td> \
                                		<td class="bottom"> \
<!--                                        	<img width="30" height="29" \
	                                       	src="http://static.jqueryfordesigners.com/demo/images/coda/bubble-tail2.png" \
                                            alt="popup tail">--> \
                                        </td>                                            \
						        		<td class="corner" id="bottomright"></td>\
				        	</tr> \
				        </tbody> \
                    </table>';
		
		$(template).insertAfter(popup);
		var tmp = $("#dpop-"+popup.attr("id"));		
			tmp.css("width", popup.css("width"));
			tmp.css("height", popup.css("height"));			
		popup.remove();
		popup = tmp;		
		
    // set the mouseover and mouseout on both element
    $([trigger.get(0), popup.get(0)]).mouseover(function(e) {        
      // stops the hide event if we move from the trigger to the popup element
      if (hideDelayTimer) clearTimeout(hideDelayTimer);

      // don't trigger the animation again if we're being shown, or already visible
      if (beingShown || shown) {
        return;
      } else {
        beingShown = true;

        // reset position of popup box
        popup.css({
          top: 30,
          left: 7,
          display: 'block' // brings the popup back in to view
        })

        // (we're using chaining on the popup) now animate it's opacity and position
        popup.css({
          top: '-=' + distance + 'px',
          opacity: 1
        });//, time, 'swing', function() {
          // once the animation is complete, set the tracker variables
          beingShown = false;
          shown = true;
//        });
      }
    }).mouseout(function () {
      // reset the timer if we get fired again - avoids double animations
      if (hideDelayTimer) clearTimeout(hideDelayTimer);
      
      // store the timer so that it can be cleared in the mouseover if required
      hideDelayTimer = setTimeout(function () {
        hideDelayTimer = null;
        popup.css({
          top: '-=' + distance + 'px',
          opacity: 0
        });//, time, 'swing', function () {
          // once the animate is complete, set the tracker variables
          shown = false;
          // hide the popup entirely after the effect (opacity alone doesn't do the job)
          popup.css('display', 'none');
        //});
      }, hideDelay);
    });
  });
};