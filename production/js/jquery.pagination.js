/**
 * This jQuery plugin displays pagination links inside the selected elements.
 *
 * @author Gabriel Birke (birke *at* d-scribe *dot* de)
 * @version 1.2
 * @param {int} maxentries Number of entries to paginate
 * @param {Object} opts Several options (see README for documentation)
 * @return {Object} jQuery Object
 */
jQuery.fn.pagination = function(maxentries, opts){
	opts = jQuery.extend({
		items_per_page:10,
		num_display_entries:10,
		current_page:0,
		num_edge_entries:0,
		link_to:"#",
		prev_text:"Prev",
		next_text:"Next",
		ellipse_text:"...",
		prev_show_always:true,
		next_show_always:true,
		callback:function(){return false;}
	},opts||{});

	return this.each(function() {
		/**
		 * Calculate the maximum number of pages
		 */
		function numPages() {
			return Math.ceil(maxentries/opts.items_per_page);
		}
		
		/**
		 * Calculate start and end point of pagination links depending on 
		 * current_page and num_display_entries.
		 * @return {Array}
		 */
		function getInterval()  {
			var ne_half 	= Math.ceil(opts.num_display_entries/2); //количество отображаемых ссылок делить на 2
			var np 			= numPages();//количество страниц, все посты делить на количество отображаемых
			var upper_limit = np-opts.num_display_entries;//количество страниц минус количество отображаемых ссылок
			var start 		= current_page>ne_half?Math.max(Math.min(parseInt(current_page)-parseInt(ne_half), upper_limit), 0):0;
			//console.log(current_page+" "+ne_half);
			//console.log(current_page+">"+ne_half+"?Math.max(Math.min("+(current_page-ne_half)+", "+upper_limit+"), 0):0");
			var end 		= current_page>ne_half?Math.min(parseInt(current_page)+parseInt(ne_half)+1, np):Math.min(opts.num_display_entries, np);
			//добавлена +1 и парсИнты
			//console.log(current_page+" "+ne_half);
			//console.log(current_page+">"+ne_half+"?Math.min("+(parseInt(current_page)+parseInt(ne_half)+1)+","+ np+"):Math.min("+opts.num_display_entries+","+ np+")");
			//console.log("Start "+start+" End "+end);
			return [start,end];
		}
		
		/**
		 * This is the event handling function for the pagination links. 
		 * @param {int} page_id The new page number
		 */
		function pageSelected(page_id, evt){
			current_page = page_id;
			//drawLinks();
			var continuePropagation = opts.callback(page_id, panel);
			if (!continuePropagation) {
				if (evt.stopPropagation) {
					evt.stopPropagation();
				}
				else {
					evt.cancelBubble = true;
				}
			}
			return continuePropagation;
		}
		
		/**
		 * This function inserts the pagination links into the container element
		 */
		function drawLinks() {
			panel.empty();
			//alert(1);
			var interval = getInterval();
			var np = numPages();
		
		// This helper function returns a handler function that calls pageSelected with the right page_id
			var getClickHandler = function(page_id) {
				return function(evt){ return pageSelected(page_id,evt); }
			}
			
		// Helper function for generating a single link (or a span tag if it's the current page)
			var appendItem = function(page_id, appendopts){
				page_id = page_id<0?0:(page_id<np?page_id:np-1); // Normalize page id to sane value
				appendopts = jQuery.extend({text:page_id+1, classes:""}, appendopts||{});
				if(page_id == current_page){
					var lnk = jQuery('<li class="selected">'+(appendopts.text)+'</li>');
				}
				else
				{
					var lnk = jQuery('<li class="div"><a>'+(appendopts.text)+'</a></li>');
						lnk.find("a").bind("click", getClickHandler(page_id))
						          .attr('href', opts.link_to.replace(/__id__/,page_id));												
				}
				if(appendopts.classes){lnk.addClass(appendopts.classes);}
				panel.append(lnk);
			}
			
		// Buttons
			// First
			var first;	
			if (current_page == 0)
				first = $('<li style="opacity: 0.4"><img src="img/arrr3.png" style="margin-left: -9px; margin-right: 0px;"/><img src="img/arrr3.png" style=" margin-right: -2px;"/></li>');
			else 
				first = $('<li><a><img src="img/arrr3.png" style="margin-left: -9px; margin-right: 0px;"/><img src="img/arrr3.png" style=" margin-right: -2px;"/></a></li>');
					first.find("a").bind("click", getClickHandler(0))
							  .attr('href', opts.link_to.replace(/__id__/,0));																
			panel.append(first);

			// Prev
			var prev;	
			if (current_page == 0)
				prev = $('<li style="opacity: 0.4"><img src="img/arrr3.png" /></li>');
			else 
				prev = $('<li><a><img src="img/arrr3.png" /></a></li>');
					prev.find("a").bind("click", getClickHandler(current_page-1))
							  .attr('href', opts.link_to.replace(/__id__/,current_page-1));																
			panel.append(prev);
		//:~				
	
		     
		// Generate starting points
			if (interval[0] > 0 && opts.num_edge_entries > 0) {
				var end = Math.min(opts.num_edge_entries, interval[0]);
				for(var i=0; i<end; i++) appendItem(i);				
				if(opts.num_edge_entries < interval[0]) jQuery('<li class="div">...</li>').appendTo(panel);					
			}

		// Generate interval links
			for(var i=interval[0]; i<interval[1]; i++) appendItem(i);
			
		// Generate ending points
			if (interval[1] < np && opts.num_edge_entries > 0) {
				if(np-opts.num_edge_entries > interval[1]) jQuery('<li class="div">...</li>').appendTo(panel);
				
				var begin = Math.max(np-opts.num_edge_entries, interval[1]);
				for(var i=begin; i<np; i++) appendItem(i);
			}
		
		// Buttons
			// Last			
			var last;	
			if (current_page >= np-1)
				last = $('<li style="opacity: 0.4"><img src="img/arrr.png" style="margin-left: -9px; margin-right: 0px;"/><img src="img/arrr.png" /></li>');
			else 
				last = $('<li><a><img src="img/arrr.png" style="margin-left: -9px; margin-right: 0px;"/><img src="img/arrr.png" /></a></li>');
					first.find("a").bind("click", getClickHandler(np-1))
							  .attr('href', opts.link_to.replace(/__id__/,np-1));																			
			// Next
			var next;	
			if (current_page >= np-1)
				 next= $('<li style="opacity: 0.4"><img src="img/arrr.png" /></li>');
			else 
				next = $('<li><a><img src="img/arrr.png" /></a></li>');
					next.find("a").bind("click", getClickHandler(current_page+1))
							  .attr('href', opts.link_to.replace(/__id__/,current_page+1));																
			panel.append(next);
			panel.append(last);
		//:~			
		}
		
		// Extract current_page from options
		var current_page = opts.current_page;
		// Create a sane value for maxentries and items_per_page
		maxentries = (!maxentries || maxentries < 0)?1:maxentries;
		opts.items_per_page = (!opts.items_per_page || opts.items_per_page < 0)?1:opts.items_per_page;
		// Store DOM element for easy access from all inner functions
		var panel = jQuery(this);
		// Attach control functions to the DOM element 
		this.selectPage = function(page_id){ pageSelected(page_id);}
		this.prevPage = function(){ 
			if (current_page > 0) {
				pageSelected(current_page - 1);
				return true;
			}
			else {
				return false;
			}
		}
		this.nextPage = function(){ 
			if(current_page < numPages()-1) {
				pageSelected(current_page+1);
				return true;
			}
			else {
				return false;
			}
		}
		// When all initialisation is done, draw the links
		drawLinks();
        // call callback function
        opts.callback(current_page, this);
	});
}


