(function($) {
$.entwine('ss.tree', function($){
	$('.cms .cms-tree').entwine({
		getTreeConfig: function() {
			var self = this;
			var config = this._super();			
			var itemsFunction = config.contextmenu.items;
			config.contextmenu.items = function(node) {				
				items = itemsFunction(node);
				items['addmockchildren'] = {
					'label': 'Add mock children',
					'action': function(obj) {
						var url = self.data('urlDuplicate').replace('/duplicate','/addmockchildren/node');
						$('.cms-container').entwine('.ss').loadPanel(ss.i18n.sprintf(
							url, obj.data('id')
						));


					}
				}
				return items;
			}
			return config;
		}
	});
});
})(jQuery);