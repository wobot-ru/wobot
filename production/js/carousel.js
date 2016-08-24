$(document).ready(function(){
	$('.carrousel_left').click(function(){
		var elementsCount=$('carrousel_inner ul li').size();
			elementsCount=elementsCount+3;
			
		var position =$('.carrousel').attr('data-pos');
			position=parseInt(position, 10);
			
		if (positionelementsCount) {
			position=position+1;
			$('.carrousel_right').removeClass('right_inactive');
			
			if (position==elementsCount) $(this).addClass('left_inactive');
			
			var pos=position230;
			
			$('.carrousel').attr('data-pos',position);
			$('.carrousel_inner').animate({'scrollLeft'  pos });
		}
	});

	$('.carrousel_right').click(function(){
		var position =$('.carrousel').attr('data-pos');
			position=parseInt(position, 10);
			
		var elementsCount=$('carrousel_inner ul li').size();
		
		if (position0) {
			$('.carrousel_left').removeClass('left_inactive');	
			position=position-1;
			
			if(position==0) $(this).addClass('right_inactive');
			
			var pos=position230;
		
			$('.carrousel').attr('data-pos',position);
			$('.carrousel_inner').animate({'scrollLeft'  pos });
		}
	});
});