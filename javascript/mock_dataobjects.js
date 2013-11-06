(function($) {
$.entwine('ss.tree', function($){

	// Add the context link to the site tree
	$('.cms .cms-tree').entwine({
		getTreeConfig: function() {
			var self = this;
			var config = this._super();
			var itemsFunction = config.contextmenu.items;
			config.contextmenu.items = function(node) {
				items = itemsFunction(node);
				items['addmockchildren'] = {
					'label': ss.i18n._t('MockData.AddMockChildren', 'Add mock children'),
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
		},


		// After creating mock children, get the tree of the parent, run through all of its IDs, and refresh the tree
		// Todo: This is inefficient. It should only update the new records, not the whole parent node.
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

	// Store the ID of the parent node used in the "add mock children" form
	$('.cms .cms-container').entwine({
		MockChildrenID: null
	});


	// Set the stored value of the mock children parent
	$('#Form_MockChildrenForm').entwine({
		onmatch: function() {
			var id = this.find(':input[name=ID]').val();
			$('.cms-container').setMockChildrenID(id);
		}
	});


	// These events add functionality to the GridField component
	$('.mockdata-generator-toggle-btn a').entwine({

		onclick: function(e) {
			e.preventDefault();
			this.hide();
			$('.mockdata-generator-options').slideDown();
		}
	});

	$('.mockdata-generator-options button.cancel').entwine({

		onclick: function(e) {
			e.preventDefault();
			$('.mockdata-generator-options').slideUp(function() {
				$('.mockdata-generator-toggle-btn a').show();
			});
		}
	});

	$('.mockdata-generator-options :text').entwine({

		onkeyup: function(e) {
			if(e.which == 13) {
				e.preventDefault();
				this.closest("form").find(".mock-data-generator-btn.create").click();
			}
		}
	});
});
})(jQuery);