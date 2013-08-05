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

$.entwine('ss', function($){
	$('#Form_MockChildrenForm.cms-edit-form').entwine({

			onsubmit: function(e, button) {				
				if(this.prop("target") != "_blank") {
					var id = this.find(":input[name=ParentID]").val();					
					if(button) this.closest('.cms-container').submitForm(this, button, function(data, status, xhr){
							console.log("refresh");
							jQuery('.cms-tree').jstree('refresh');
							console.log("done");
						}
					);
					return false;
				}
			},
	})
});

})(jQuery);