<div id="mock-data-admin" class="cms-content center $BaseCSSClasses" data-layout-type="border" data-pjax-fragment="Content" data-ignore-tab-state="true">
	<div class="cms-content-header north">
		<div class="cms-content-header-info">			
			<h2><% _t('MockData.MANAGEMOCKDATA','Manage Mock Data') %></h2>
		</div>		
	
	</div>
	<div class="mock-data-container">
		<div class="dashboard-panel mock-data-create-form">		
			<div class="dashboard-panel-header">
				<div class="dashboard-panel-icon">
					<img src="$Icon" width="24" height="24" />
				</div>
				<h3><% _t('MockData.CREATEHEADER','Create Mock Data') %></h3>
			</div>
		
			<div class="dashboard-panel-content">
				$CreateForm
			</div>

		</div>
		<div class="dashboard-panel mock-data-delete">		
			<div class="dashboard-panel-header">
				<div class="dashboard-panel-header-actions">
					<button class="ss-ui-button" data-toggle-text="<% _t('MockData.SELECTNONE','Select none') %>"><% _t('MockData.SELECTALL','Select all') %></button>
				</div>

				<div class="dashboard-panel-icon">
					<img src="$Icon" width="24" height="24" />
				</div>
				<h3><% _t('MockData.DELETEHEADER','Clear Mock Data') %></h3>
			</div>
		
			<div class="dashboard-panel-content">
				$DeleteForm
			</div>

		</div>
	</div>

</div>