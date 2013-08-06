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

$.entwine('ss.tree', function($){	
	$('.cms .cms-tree').entwine({

		'from .cms-container': {

			onafterstatechange: function(e){								
				if(id = $('.cms-container').entwine('.ss').getMockChildrenID()) {					
					var self = this;
					$.ajax({
						url: self.data('urlTree'),
						type: "GET",
						data: {"ID": id},
						success: function(data) {
							var ids = [];
							var $html = $("<ul>"+data+"</ul>");
							$html.find('li').each(function() {
								ids.push($(this).data('id'));
							});							
							self.updateNodesFromServer(ids);
							$('.cms-container').entwine('.ss').setMockChildrenID(null)
						}
					});
				}
				else {						
					this.updateFromEditForm();
				}					
			}
		}

	});


});


$.entwine('ss', function($) {
	$('.cms .cms-container').entwine({
		MockChildrenID: null
	});

	$('#Form_MockChildrenForm').entwine({
		onmatch: function() {
			var id = this.find(':input[name=ID]').val();
			$('.cms-container').setMockChildrenID(id);			
		}
	});

});

})(jQuery);