<div class="account-toolbar">
	<div class="container">
		<div class="account-toolbar-inner">
			<div class="account-toolbar-left">
				<a id="nav-dashboard" href="/dashboard" class="{{ !empty($showNavDashboard) ? 'nav-visible' : '' }}" style="{{ !empty($showNavDashboard) ? 'display:inline' : 'display:none' }}">
					<i class="fa fa-angle-right"></i> لوحة التحكم
				</a>
				<a id="nav-orders" href="/orders" class="{{ !empty($showNavOrders) ? 'nav-visible' : '' }}" style="{{ !empty($showNavOrders) ? 'display:inline' : 'display:none' }}">
					<i class="fa fa-angle-right"></i> طلباتي
				</a>
				<a id="nav-profile" href="/profile" class="{{ !empty($showNavProfile) ? 'nav-visible' : '' }}" style="{{ !empty($showNavProfile) ? 'display:inline' : 'display:none' }}">
					<i class="fa fa-angle-right"></i> الملف الشخصي
				</a>
			</div>
			@if(empty($hideToolbarTitle))
				<div class="account-toolbar-title" id="toolbar-title">{{ $toolbarTitle ?? 'الحساب' }}</div>
			@endif
			<div class="account-toolbar-actions">
				<a href="/store" class="primary-btn">المتجر</a>
				@if(!empty($showLogout))
				<button type="button" class="account-btn-outline" onclick="logout()">تسجيل الخروج</button>
				@endif
			</div>
		</div>
	</div>
</div>
