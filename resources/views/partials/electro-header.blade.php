@php
	$activeNav = $activeNav ?? '';
@endphp

<header>
	<div id="top-header">
		<div class="container">
			<ul class="header-links pull-left">
				<li><a href="tel:+021955184"><i class="fa fa-phone"></i> +021-95-51-84</a></li>
				<li><a href="mailto:email@email.com"><i class="fa fa-envelope-o"></i> email@email.com</a></li>
				<li><a href="#"><i class="fa fa-map-marker"></i> 1734 Stonecoal Road</a></li>
			</ul>
			<ul class="header-links pull-right">
				@empty($minimalHeader)
					<li><a href="#"><i class="fa fa-dollar"></i> USD</a></li>
				@endempty
				<li><a href="/login" id="header-account-link"><i class="fa fa-user-o"></i> My Account</a></li>
			</ul>
		</div>
	</div>

	<div id="header">
		<div class="container">
			<div class="row">
				@if(!empty($minimalHeader))
					<div class="col-md-12">
						<div class="header-logo header-logo--minimal">
							<a href="/home" class="logo">
								<img src="/img/logo.png" alt="Electro">
							</a>
						</div>
					</div>
				@else
					<div class="col-md-3">
						<div class="header-logo">
							<a href="/home" class="logo">
								<img src="/img/logo.png" alt="Electro">
							</a>
						</div>
					</div>
					<div class="col-md-6">
						@if(!empty($showHeaderUserName))
							<div class="header-profile-center">
								<div class="header-profile-label">Profile</div>
								<div class="header-profile-name" id="header-user-name">Loading...</div>
							</div>
						@else
							<div class="header-search">
								<form id="header-search-form" action="/store" method="get">
									<select class="input-select" id="header-category-select" name="category">
										<option value="">All Categories</option>
									</select>
									<input class="input" id="header-search-input" name="q" placeholder="Search here">
									<button type="submit" class="search-btn">Search</button>
								</form>
							</div>
						@endif
					</div>
					<div class="col-md-3 clearfix">
						<div class="header-ctn">
							@empty($hideCart)
							<div class="dropdown">
								<a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
									<i class="fa fa-shopping-cart"></i>
									<span>Your Cart</span>
									<div class="qty" id="header-cart-qty">0</div>
								</a>
								<div class="cart-dropdown">
									<div class="cart-list" id="header-cart-list">
										<p style="padding:15px;margin:0;color:#8D99AE;">Your cart is empty</p>
									</div>
									<div class="cart-summary">
										<small id="header-cart-summary">0 Item(s) selected</small>
										<h5 id="header-cart-subtotal">SUBTOTAL: $0.00</h5>
									</div>
									<div class="cart-btns">
										<a href="/store">View Store</a>
										<a href="/checkout">Checkout <i class="fa fa-arrow-circle-right"></i></a>
									</div>
								</div>
							</div>
							@endempty
							<div class="menu-toggle">
								<a href="#"><i class="fa fa-bars"></i><span>Menu</span></a>
							</div>
						</div>
					</div>
				@endif
			</div>
		</div>
	</div>
</header>

@if(empty($minimalHeader) && empty($hideMainNav))
<nav id="navigation">
	<div class="container">
		<div id="responsive-nav">
			<ul class="main-nav nav navbar-nav" id="main-nav-list">
				<li class="{{ $activeNav === 'home' ? 'active' : '' }}"><a href="/home">Home</a></li>
				<li class="{{ $activeNav === 'store' ? 'active' : '' }}"><a href="/store">Store</a></li>
			</ul>
		</div>
	</div>
</nav>
@endempty
