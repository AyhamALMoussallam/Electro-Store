<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
@include('partials.electro-head', ['title' => 'Electro - Home'])
<link type="text/css" rel="stylesheet" href="/css/slick.css"/>
<link type="text/css" rel="stylesheet" href="/css/slick-theme.css"/>
<link type="text/css" rel="stylesheet" href="/css/nouislider.min.css"/>
</head>
<body data-active-nav="home">

@include('partials.electro-header', ['activeNav' => 'home'])

		<!-- Category shops -->
		<div class="section">
			<div class="container">
				<div class="row" id="home-shops-row"></div>
			</div>
		</div>

		<!-- New Products -->
		<div class="section">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<div class="section-title">
							<h3 class="title" data-i18n="newProducts">منتجات جديدة</h3>
							<div class="section-nav">
								<ul class="section-tab-nav tab-nav" id="home-new-tab-nav"></ul>
							</div>
						</div>
					</div>
					<div class="col-md-12">
						<div class="row">
							<div class="products-tabs">
								<div id="tab1" class="tab-pane active">
									<div class="products-slick" id="home-new-products-slick" data-nav="#slick-nav-1"></div>
									<div id="slick-nav-1" class="products-slick-nav"></div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>


		<!-- Top selling carousel -->
		<div class="section">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<div class="section-title">
							<h3 class="title" data-i18n="topSelling">الأكثر مبيعاً</h3>
							<div class="section-nav">
								<ul class="section-tab-nav tab-nav" id="home-top-tab-nav"></ul>
							</div>
						</div>
					</div>
					<div class="col-md-12">
						<div class="row">
							<div class="products-tabs">
								<div id="tab2" class="tab-pane fade in active">
									<div class="products-slick" id="home-top-selling-slick" data-nav="#slick-nav-2"></div>
									<div id="slick-nav-2" class="products-slick-nav"></div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Top selling widgets -->
		<div class="section">
			<div class="container">
				<div class="row">
					<div class="col-md-4 col-xs-6">
						<div class="section-title">
							<h4 class="title" id="home-widget-title-1" data-i18n="topSelling">الأكثر مبيعاً</h4>
							<div class="section-nav">
								<div id="slick-nav-3" class="products-slick-nav"></div>
							</div>
						</div>
						<div class="products-widget-slick home-widget-slick" id="home-widget-slick-1" data-nav="#slick-nav-3"></div>
					</div>

					<div class="col-md-4 col-xs-6">
						<div class="section-title">
							<h4 class="title" id="home-widget-title-2" data-i18n="topSelling">الأكثر مبيعاً</h4>
							<div class="section-nav">
								<div id="slick-nav-4" class="products-slick-nav"></div>
							</div>
						</div>
						<div class="products-widget-slick home-widget-slick" id="home-widget-slick-2" data-nav="#slick-nav-4"></div>
					</div>

					<div class="clearfix visible-sm visible-xs"></div>

					<div class="col-md-4 col-xs-6">
						<div class="section-title">
							<h4 class="title" id="home-widget-title-3" data-i18n="topSelling">الأكثر مبيعاً</h4>
							<div class="section-nav">
								<div id="slick-nav-5" class="products-slick-nav"></div>
							</div>
						</div>
						<div class="products-widget-slick home-widget-slick" id="home-widget-slick-3" data-nav="#slick-nav-5"></div>
					</div>
				</div>
			</div>
		</div>

		@include('partials.electro-footer')

		@include('partials.electro-scripts', ['withSlick' => true, 'withNouislider' => true])
		<script src="/js/home.js"></script>

	</body>
</html>
