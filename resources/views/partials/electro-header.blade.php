@php

	$activeNav = $activeNav ?? '';

@endphp



<header>

	<div id="top-header">

		<div class="container">

			<ul class="header-links pull-left">

				<li><a href="tel:+963959498994"><i class="fa fa-phone"></i> +963 959 498 994</a></li>

				<li><a href="mailto:electro@gmail.com"><i class="fa fa-envelope-o"></i> electro@gmail.com</a></li>

				<li><a href="#"><i class="fa fa-map-marker"></i> Damascus, Syria, AL-Mazzah</a></li>

			</ul>

			<ul class="header-links pull-right">

				@include('partials.electro-preferences')

				@empty($minimalHeader)

					<li id="header-dashboard-item" style="display: none;">

						<a href="/dashboard" id="header-dashboard-link"><i class="fa fa-tachometer"></i> <span data-i18n="dashboard"></span></a>

					</li>

				@endempty

				<li><a href="/login" id="header-account-link"><i class="fa fa-user-o"></i> <span data-i18n="myAccount"></span></a></li>

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

								<div class="header-profile-label" data-i18n="profile"></div>

								<div class="header-profile-name" id="header-user-name"></div>

							</div>

						@else

							<div class="header-search">

								<form id="header-search-form" action="/store" method="get">

									<select class="input-select" id="header-category-select" name="category">

										<option value="" data-i18n="allCategories">جميع التصنيفات</option>

									</select>

									<input class="input" id="header-search-input" name="q" data-i18n-placeholder="searchHere" placeholder="ابحث هنا">

									<button type="submit" class="search-btn" data-i18n="search">بحث</button>

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

									<span data-i18n="yourCart"></span>

									<div class="qty" id="header-cart-qty">0</div>

								</a>

								<div class="cart-dropdown">

									<div class="cart-list" id="header-cart-list">

										<p style="padding:15px;margin:0;color:#8D99AE;" data-i18n="cartEmpty"></p>

									</div>

									<div class="cart-summary">

										<small id="header-cart-summary">0</small>

										<h5 id="header-cart-subtotal"></h5>

									</div>

									<div class="cart-btns">

										<a href="/store" data-i18n="viewStore"></a>

										<a href="/checkout"><span data-i18n="checkout"></span> <i class="fa fa-arrow-circle-right checkout-cart-icon"></i></a>

									</div>

								</div>

							</div>

							@endempty

							<div class="menu-toggle">

								<a href="#"><i class="fa fa-bars"></i><span data-i18n="menu"></span></a>

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

				<li class="{{ $activeNav === 'home' ? 'active' : '' }}"><a href="/home" data-i18n="home"></a></li>

				<li class="{{ $activeNav === 'store' ? 'active' : '' }}"><a href="/store" data-i18n="store"></a></li>

			</ul>

		</div>

	</div>

</nav>

@endif

