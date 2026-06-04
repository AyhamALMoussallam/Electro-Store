<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
@include('partials.electro-head', ['title' => 'إلكترو - المتجر'])
<link type="text/css" rel="stylesheet" href="/css/nouislider.min.css"/>
</head>
<body data-active-nav="store">

@include('partials.electro-header', ['activeNav' => 'store'])

		@include('partials.electro-breadcrumb', ['breadcrumbItems' => []])

		<!-- SECTION -->
		<div class="section">
			<!-- container -->
			<div class="container">
				<!-- row -->
				<div class="row">
					<!-- ASIDE -->
					<div id="aside" class="col-md-3">
						<!-- aside Widget -->
						<div class="aside">
							<h3 class="aside-title" data-i18n="categories"></h3>
							<div class="checkbox-filter" id="categories-filter">

								
								
							</div>
						</div>
						<!-- /aside Widget -->

						<!-- aside Widget -->
						<div class="aside">
							<h3 class="aside-title" data-i18n="price"></h3>

							<div style="margin-bottom:10px;">
								<input
									type="number"
									id="min-price"
									data-i18n-placeholder="min"
									class="input"
								>
							</div>

							<div style="margin-bottom:10px;">
								<input
									type="number"
									id="max-price"
									data-i18n-placeholder="max"
									class="input"
								>
							</div>

							<button
								class="primary-btn"
								onclick="applyFilters()"
								data-i18n="apply"
							>
							</button>
						</div>
						<!-- /aside Widget -->

						<!-- aside Widget -->
						<div class="aside">
							<h3 class="aside-title" data-i18n="brand"></h3>
							<div class="checkbox-filter" id="brands-filter">
								
							</div>
						</div>
						<!-- /aside Widget -->

						<!-- aside Widget -->
						<div class="aside">
							<h3 class="aside-title" data-i18n="topSelling">الأكثر مبيعاً</h3>
							<div id="top-selling-container"></div>
						</div>
						<!-- /aside Widget -->
					</div>
					<!-- /ASIDE -->

					<!-- STORE -->
					<div id="store" class="col-md-9">
						<!-- store top filter -->
						<div class="store-filter clearfix">
							<div class="store-sort">
								

								<label>
									<span data-i18n="show"></span>:
									<select 
										class="input-select"
										id="per-page"
										onchange="changePerPage()"
									>
										<option value="10">10</option>
										<option value="20">20</option>
									</select>
								</label>
							</div>
							
						</div>
						<!-- /store top filter -->

						<!-- store products -->
						<div class="row" id="products-container">
							
						</div>
						<!-- /store products -->

						<!-- store bottom filter -->
						<div class="store-filter store-filter--bottom clearfix">
							<span class="store-qty" id="products-count"></span>
							<ul class="store-pagination" id="pagination"></ul>
						</div>
						<!-- /store bottom filter -->
					</div>
					<!-- /STORE -->
				</div>
				<!-- /row -->
			</div>
			<!-- /container -->
		</div>
		<!-- /SECTION -->

@include('partials.electro-footer')

@include('partials.electro-scripts', ['withNouislider' => true])

		<script>
			function t(key, replacements) {
				if (window.ElectroI18n && typeof window.ElectroI18n.t === 'function') {
					return window.ElectroI18n.t(key, replacements);
				}
				return key;
			}

			let allProducts = [];
			let allCategories = [];
			let filteredProducts = [];
			let currentPage = 1;
			let perPage = 10;


			// =========================
			// LOAD EVERYTHING
			// =========================
			async function boot() {

				await loadCategories();
				await loadBrands();
				await loadProducts();
				await loadTopSelling();
			}

			boot();


			// =========================
			// LOAD PRODUCTS
			// =========================
			async function loadProducts() {

				const res = await fetch("/api/products");

				const data = await res.json();

				allProducts = data.data ?? data;

				filteredProducts = [...allProducts];

				applyUrlFilters();

				renderProducts();
			}


			function applyUrlFilters() {
				const params = new URLSearchParams(window.location.search);
				const categoryId = params.get('category');
				const query = (params.get('q') || '').trim().toLowerCase();

				let filtered = [...allProducts];

				if (categoryId) {
					const id = Number(categoryId);
					filtered = filtered.filter(p => p.category_id === id);
					const checkbox = document.getElementById('cat-' + id);
					if (checkbox) checkbox.checked = true;
				}

				if (query) {
					filtered = filtered.filter(p => {
						const name = (p.name || '').toLowerCase();
						const brand = (p.brand?.name || '').toLowerCase();
						const category = (p.category?.name || '').toLowerCase();
						return name.includes(query) ||
							brand.includes(query) ||
							category.includes(query);
					});
				}

				filteredProducts = filtered;
				currentPage = 1;
			}


			// =========================
			// LOAD CATEGORIES
			// =========================
			async function loadCategories() {

				const res = await fetch("/api/categories");
				const data = await res.json();

				allCategories = data.data;

				renderCategories();
			}



			// =========================
			// LOAD BRANDS
			// =========================
			async function loadBrands() {

				const res = await fetch("/api/brands");

				const data = await res.json();

				allBrands = data.data;

				renderBrands();
			}


			// =========================
			// RENDER PRODUCTS
			// =========================
			function renderProducts() {

				const container =
					document.getElementById(
						"products-container"
					);

				container.innerHTML = "";

				// start index
				const start =
					(currentPage - 1) * perPage;

				// end index
				const end =
					start + perPage;

				// current page products
				const products =
					filteredProducts.slice(start, end);

				if (!products.length) {

					container.innerHTML =
						"<h3>" + t('noProducts') + "</h3>";

					renderPagination();
					renderProductsCount();
					updateStoreBreadcrumb();
					return;
				}

				products.forEach(product => {

					container.innerHTML += `

					<div class="col-md-4 col-xs-6">

						<div class="product">

							<div class="product-img">
								<a href="/product?id=${product.id}">
									<img src="/storage/${product.image}" alt="">
								</a>
							</div>

							<div class="product-body">

								<p class="product-category">
									${product.category?.name ?? '-'}
								</p>

								<h3 class="product-name">
									<a href="/product?id=${product.id}">
										${product.name}
									</a>
								</h3>

								<h4 class="product-price">
									${formatPrice(product.price)}
								</h4>

								<p class="product-brand">
									${t('by')} ${product.brand?.name ?? t('unknown')}
								</p>

							</div>

							<div class="add-to-cart">

								<button
									class="add-to-cart-btn"
								>
									<i class="fa fa-shopping-cart"></i>

									${t('addToCart')}
								</button>

							</div>

						</div>

					</div>
					`;
				});

    renderPagination();
    renderProductsCount();
    updateStoreBreadcrumb();
}


			// =========================
			// RENDER CATEGORIES
			// =========================
			function renderCategories() {

				const container =
					document.getElementById(
						"categories-filter"
					);

				container.innerHTML = "";

				allCategories.forEach(category => {

					container.innerHTML += `

					<div class="input-checkbox">

						<input
							type="checkbox"
							id="cat-${category.id}"
							value="${category.id}"
						>

						<label for="cat-${category.id}">

							<span></span>

							${category.name}

						</label>

					</div>
					`;
				});
			}



			// =========================
			// RENDER BRANDS
			// =========================
			function renderBrands() {

				const container =
					document.getElementById(
						"brands-filter"
					);

				container.innerHTML = "";

				allBrands.forEach(brand => {

					container.innerHTML += `

					<div class="input-checkbox">

						<input
							type="checkbox"
							id="brand-${brand.id}"
							value="${brand.id}"
						>

						<label for="brand-${brand.id}">

							<span></span>

							${brand.name}

						</label>

					</div>
					`;
				});
			}


			// =========================
			// FILTER PARAMS (shared with top selling)
			// =========================
			function getFilterParams() {

				const params = new URLSearchParams();

				const checkedCategories =
					document.querySelectorAll(
						'#categories-filter input:checked'
					);

				[...checkedCategories].forEach(input => {
					params.append('category_ids[]', input.value);
				});

				const checkedBrands =
					document.querySelectorAll(
						'#brands-filter input:checked'
					);

				[...checkedBrands].forEach(input => {
					params.append('brand_ids[]', input.value);
				});

				const minPrice =
					document.getElementById('min-price').value;

				const maxPrice =
					document.getElementById('max-price').value;

				if (minPrice && window.ElectroCurrency) {
					const usdMin = window.ElectroCurrency.displayToUsd(minPrice);
					if (usdMin != null) {
						params.set('min_price', usdMin);
					}
				}

				if (maxPrice && window.ElectroCurrency) {
					const usdMax = window.ElectroCurrency.displayToUsd(maxPrice);
					if (usdMax != null) {
						params.set('max_price', usdMax);
					}
				}

				return params;
			}


			// =========================
			// LOAD TOP SELLING
			// =========================
			async function loadTopSelling() {

				const params = getFilterParams();
				const query = params.toString();
				const url = query
					? `/api/products/top-selling?${query}`
					: '/api/products/top-selling';

				const res = await fetch(url);
				const data = await res.json();

				const products = data.data ?? data;

				renderTopSelling(products);
			}


			// =========================
			// RENDER TOP SELLING
			// =========================
			function renderTopSelling(products) {

				const container =
					document.getElementById(
						'top-selling-container'
					);

				container.innerHTML = '';

				if (!products.length) {

					container.innerHTML =
						'<p>' + t('noTopSelling') + '</p>';

					return;
				}

				products.forEach(product => {

					container.innerHTML += `
						<div class="product-widget">
							<div class="product-img">
								<img src="/storage/${product.image}" alt="">
							</div>
							<div class="product-body">
								<p class="product-category">
									${product.category?.name ?? '-'}
								</p>
								<h3 class="product-name">
									<a href="/product?id=${product.id}">
										${product.name}
									</a>
								</h3>
								<h4 class="product-price">
									${formatPrice(product.price)}
								</h4>
							</div>
						</div>
					`;
				});
			}


			// =========================
			// APPLY FILTERS
			// =========================
			function applyFilters() {

				let filtered = [...allProducts];

				// selected categories
				const checked =
					document.querySelectorAll(
						'#categories-filter input:checked'
					);

				const selectedCategories =
					[...checked].map(c => Number(c.value));

				// filter category
				if (selectedCategories.length) {

					filtered = filtered.filter(product => {

						return selectedCategories.includes(
							product.category_id
						);
					});
				}

				// selected brands
				const checkedBrands =
					document.querySelectorAll(
						'#brands-filter input:checked'
					);

				const selectedBrands =
					[...checkedBrands].map(b => Number(b.value));

				// filter brands
				if (selectedBrands.length) {

					filtered = filtered.filter(product => {

						return selectedBrands.includes(
							product.brand_id
						);
					});
				}

				// min price
				const minPrice =
					Number(
						document.getElementById(
							"min-price"
						).value
					);

				// max price
				const maxPrice =
					Number(
						document.getElementById(
							"max-price"
						).value
					);

				const minUsd = minPrice && window.ElectroCurrency
					? window.ElectroCurrency.displayToUsd(minPrice)
					: null;
				const maxUsd = maxPrice && window.ElectroCurrency
					? window.ElectroCurrency.displayToUsd(maxPrice)
					: null;

				if (minUsd != null) {
					filtered = filtered.filter(product => product.price >= minUsd);
				}

				if (maxUsd != null) {
					filtered = filtered.filter(product => product.price <= maxUsd);
				}

				filteredProducts = filtered;

				currentPage = 1;

				renderProducts();
				loadTopSelling();
			}


			// =========================
// PAGINATION
// =========================
function renderPagination() {

    const pagination =
        document.getElementById(
            "pagination"
        );

    pagination.innerHTML = "";

    const totalPages =
        Math.ceil(
            filteredProducts.length / perPage
        );

    // previous
    if (currentPage > 1) {

        pagination.innerHTML += `
            <li>
                <a href="#"
                   onclick="goToPage(${currentPage - 1})">
                    <i class="fa fa-angle-left"></i>
                </a>
            </li>
        `;
    }

    // pages
    for (let i = 1; i <= totalPages; i++) {

        pagination.innerHTML += `

        <li class="${
            currentPage === i
            ? 'active'
            : ''
        }">

            <a href="#"
               onclick="goToPage(${i})">
                ${i}
            </a>

        </li>
        `;
    }

    // next
    if (currentPage < totalPages) {

        pagination.innerHTML += `
            <li>
                <a href="#"
                   onclick="goToPage(${currentPage + 1})">
                    <i class="fa fa-angle-right"></i>
                </a>
            </li>
        `;
    }
}


// =========================
// CHANGE PAGE
// =========================
function goToPage(page) {

    currentPage = page;

    renderProducts();

    window.scrollTo({
        top: 0,
        behavior: "smooth"
    });
}


// =========================
// CHANGE PER PAGE
// =========================
function changePerPage() {

    perPage = Number(
        document.getElementById(
            "per-page"
        ).value
    );

    currentPage = 1;

    renderProducts();
}


// =========================
// BREADCRUMB
// =========================
function updateStoreBreadcrumb() {
	if (typeof window.buildStoreBreadcrumb !== 'function') {
		return;
	}

	const params = new URLSearchParams(window.location.search);
	const checked = document.querySelectorAll(
		'#categories-filter input:checked'
	);
	const selectedCategoryIds = [...checked].map(
		c => Number(c.value)
	);

	const items = window.buildStoreBreadcrumb({
		categories: allCategories,
		filteredCount: filteredProducts.length,
		selectedCategoryIds: selectedCategoryIds,
		urlCategoryId: params.get('category'),
		searchQuery: params.get('q') || '',
	});

	window.renderBreadcrumb(items);
}


// =========================
// PRODUCTS COUNT
// =========================
function renderProductsCount() {
    const countEl = document.getElementById('products-count');
    if (!countEl) {
        return;
    }

    const total = filteredProducts.length;

    if (total === 0) {
        countEl.innerText = t('showingProductsNone');
        return;
    }

    const start = (currentPage - 1) * perPage + 1;
    const end = Math.min(currentPage * perPage, total);

    countEl.innerText = t('showingProducts', {
        start: start,
        end: end,
        total: total
    });
}

		</script>

	</body>
</html>
