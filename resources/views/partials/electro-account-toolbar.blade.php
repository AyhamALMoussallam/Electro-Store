<div class="account-toolbar">
	<div class="container">
		<div class="account-toolbar-inner">
			<div class="account-toolbar-left">
				<a id="nav-dashboard" href="/dashboard" class="{{ !empty($showNavDashboard) ? 'nav-visible' : '' }}" style="{{ !empty($showNavDashboard) ? 'display:inline' : 'display:none' }}">
					<i class="fa account-toolbar-chevron" aria-hidden="true"></i> <span data-i18n="dashboard"></span>
				</a>
				<a id="nav-orders" href="/orders" class="{{ !empty($showNavOrders) ? 'nav-visible' : '' }}" style="{{ !empty($showNavOrders) ? 'display:inline' : 'display:none' }}">
					<i class="fa account-toolbar-chevron" aria-hidden="true"></i> <span data-i18n="myOrders"></span>
				</a>
				<a id="nav-profile" href="/profile" class="{{ !empty($showNavProfile) ? 'nav-visible' : '' }}" style="{{ !empty($showNavProfile) ? 'display:inline' : 'display:none' }}">
					<i class="fa account-toolbar-chevron" aria-hidden="true"></i> <span data-i18n="profile"></span>
				</a>
			</div>
			@if(empty($hideToolbarTitle))
				<div class="account-toolbar-title" id="toolbar-title" @if(!empty($toolbarTitleKey)) data-i18n="{{ $toolbarTitleKey }}" @endif>{{ $toolbarTitle ?? '' }}</div>
			@endif
			<div class="account-toolbar-actions">
				<a href="/store" class="primary-btn" data-i18n="storeBtn"></a>
				@if(!empty($showLogout))
				<button type="button" class="account-btn-outline" onclick="logout()" data-i18n="logout"></button>
				@endif
			</div>
		</div>
	</div>
</div>
