<div class="account-toolbar">
	<div class="container">
		<div class="account-toolbar-inner">
			<div class="account-toolbar-left">
				<a id="nav-dashboard" href="/dashboard" style="display:none;">
					<i class="fa fa-angle-left"></i> Dashboard
				</a>
				<a id="nav-orders" href="/orders" style="display:none;">
					<i class="fa fa-angle-left"></i> My Orders
				</a>
				<a id="nav-profile" href="/profile" style="display:none;">
					<i class="fa fa-angle-left"></i> Profile
				</a>
			</div>
			<div class="account-toolbar-title" id="toolbar-title">{{ $toolbarTitle ?? 'Account' }}</div>
			<div class="account-toolbar-actions">
				<a href="/store" class="primary-btn">Store</a>
				@if(!empty($showLogout))
				<button type="button" class="account-btn-outline" onclick="logout()">Logout</button>
				@endif
			</div>
		</div>
	</div>
</div>
