(function($) {
$.entwine('ss', function($) {
	$('#mock-data-admin *').entwine({

		getContainer: function() {
			return this.closest("#mock-data-admin");
		}

	});


	$('#mock-data-admin').entwine({

		onmatch: function() {
	
		}
	});

	$('#mock-data-admin .dashboard-panel').entwine({

		loading: function() {
			this.addClass("loading");
		},

		doneLoading: function() {
			this.removeClass("loading");
		}
	});


	$('#mock-data-admin .dashboard-panel *').entwine({

		getPanel: function() {
			return this.closest(".dashboard-panel");
		}
	});



	$('#mock-data-admin .dashboard-panel form').entwine({

		onmatch: function() {
			this.find("[type=submit]").attr("disabled", true);
			this.validate();
		},

		onsubmit: function(e) {
			e.preventDefault();
			if(this.validate()) {
				var self = this;
				this.getPanel().loading();
				$.ajax({
					url: this.attr('action'),
					data: this.serialize(),
					success: function(data) {
						alert(data);
						self.getPanel().doneLoading();
					}
				});
			}

		},


		validate: function() {
			var valid = true;
			this.find(".field").each(function() {
				if(!$(this).isValid()) {					
					valid = false;
					return false;
				}
			});
			this.find("[type=submit]").attr("disabled", !valid);
			return valid;
		}
	});


	$('#mock-data-admin .dashboard-panel form .field').entwine({
		getFormField: function() {
			if(this.hasClass("dropdown")) {
				return this.find("select:first");
			}
			if(this.hasClass("checkboxset")) {
				return this.find(":checkbox");
			}
			return this.find("input:first");
		},

		isValid: function() {
			if(!this.hasClass("required")) return true;
			if(this.hasClass("numeric")) return parseInt(this.getFormField().val()) > 0
			if(this.hasClass("checkboxset")) return this.getFormField().filter(":checked").length > 0
			return this.getFormField().val().length;
		}

	});



	$('#mock-data-admin .dashboard-panel form :checkbox').entwine({

	   	onchange: function(e) {
	   		this.closest("form").validate();
	   	}


	});


	// $('#mock-data-admin .dashboard-panel form select').entwine({

	//    	onmatch: function(e) {
	//    		this.closest("form").validate();
	//    	}

	// })

	$('#mock-data-admin .dashboard-panel form :text').entwine({

		onkeyup: function(e) {
			this.closest("form").validate();
		}
	});

	$('.cms #mock-data-admin .dashboard-panel form select[name=ClassName]').entwine({

		onmatch: function() {
			console.log("shit");
		},

		onchange: function(e) {
			this.closest("form").validate();
			this.getPanel().loading();
			var self = this;
			$.ajax({
				url: this.data('url'),
				data: {
					ClassName: this.val()
				},
				dataType: "JSON",
				success: function(json) {
					console.log(json);
					if(json.related_classes) {						
						html = "";
						for(i in json.related_classes) {
							html += "<option value='"+i+"'>"+json.related_classes[i]+"</option>";
						}
						self.closest("form").find("#ParentID").show().find("select").html(html).trigger("liszt:updated");
						self.closest("form").find("[name=ParentField]").val(json.parent_field);
					}
					else {
						self.closest("form").find("#ParentID").hide();
					}
					if(json.relations) {
						self.closest("form").find("#IncludeRelations").show();
					}
					else {
						self.closest("form").find("#IncludeRelations").hide();
					}
					if(json.files) {
						self.closest("form").find("#DownloadImages").show();
					}
					else {
						self.closest("form").find("#DownloadImages").hide();
					}
					self.getPanel().doneLoading();
				}
			})
		}
	})
		


});
})(jQuery);