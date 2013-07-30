(function($) {
$.entwine('ss.tree', function($){
	$('.cms .cms-tree').entwine({
		getTreeConfig: function() {
			var config = this._super();			
			var itemsFunction = config.contextmenu.items;
			config.contextmenu.items = function(node) {				
				items = itemsFunction(node);
				items['addmockchildren'] = {
					'label': 'Add mock children',
					'action': function(obj) {
						alert("It works");
					}
				}
				return items;
			}
			return config;
		}
	});
});
})(jQuery);