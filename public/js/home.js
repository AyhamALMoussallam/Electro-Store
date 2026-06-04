(function ($) {
	'use strict';

	const API = '/api';
	const SHOP_IMAGES = ['/img/shop01.png', '/img/shop03.png', '/img/shop02.png'];

	let allProducts = [];
	let allCategories = [];

	function escapeHtml(text) {
		const div = document.createElement('div');
		div.textContent = text == null ? '' : String(text);
		return div.innerHTML;
	}

	function authHeaders() {
		const headers = {
			Accept: 'application/json',
			'Content-Type': 'application/json',
		};
		const token = localStorage.getItem('auth_token');
		if (token) {
			headers.Authorization = 'Bearer ' + token;
		}
		return headers;
	}

	function renderProductSlickCard(product) {
		const image = product.image
			? '/storage/' + product.image
			: '/img/product01.png';
		const category = product.category?.name ?? '-';

		return (
			'<div class="product">' +
				'<div class="product-img">' +
					'<a href="/product?id=' + product.id + '">' +
						'<img src="' + image + '" alt="">' +
					'</a>' +
				'</div>' +
				'<div class="product-body">' +
					'<p class="product-category">' + escapeHtml(category) + '</p>' +
					'<h3 class="product-name">' +
						'<a href="/product?id=' + product.id + '">' +
							escapeHtml(product.name) +
						'</a>' +
					'</h3>' +
					'<h4 class="product-price">$' + product.price + '</h4>' +
				'</div>' +
				'<div class="add-to-cart">' +
					'<button type="button" class="add-to-cart-btn home-add-to-cart" ' +
						'data-product-id="' + product.id + '">' +
						'<i class="fa fa-shopping-cart"></i> add to cart' +
					'</button>' +
				'</div>' +
			'</div>'
		);
	}

	function renderProductWidget(product) {
		const image = product.image
			? '/storage/' + product.image
			: '/img/product01.png';
		const category = product.category?.name ?? '-';

		return (
			'<div class="product-widget">' +
				'<div class="product-img">' +
					'<img src="' + image + '" alt="">' +
				'</div>' +
				'<div class="product-body">' +
					'<p class="product-category">' + escapeHtml(category) + '</p>' +
					'<h3 class="product-name">' +
						'<a href="/product?id=' + product.id + '">' +
							escapeHtml(product.name) +
						'</a>' +
					'</h3>' +
					'<h4 class="product-price">$' + product.price + '</h4>' +
				'</div>' +
			'</div>'
		);
	}

	function widgetSlidesHtml(products) {
		const slides = [];

		for (let i = 0; i < products.length; i += 3) {
			const chunk = products.slice(i, i + 3);
			slides.push(
				'<div>' + chunk.map(renderProductWidget).join('') + '</div>'
			);
		}

		return slides.join('');
	}

	function initProductSlick($el, navSelector) {
		if (!$el.length) {
			return;
		}

		if ($el.hasClass('slick-initialized')) {
			$el.slick('unslick');
		}

		$el.slick({
			slidesToShow: 4,
			slidesToScroll: 1,
			autoplay: true,
			infinite: true,
			speed: 300,
			dots: false,
			arrows: true,
			appendArrows: navSelector || false,
			responsive: [
				{
					breakpoint: 991,
					settings: { slidesToShow: 2, slidesToScroll: 1 },
				},
				{
					breakpoint: 480,
					settings: { slidesToShow: 1, slidesToScroll: 1 },
				},
			],
		});
	}

	function initWidgetSlick($el, navSelector) {
		if (!$el.length) {
			return;
		}

		if ($el.hasClass('slick-initialized')) {
			$el.slick('unslick');
		}

		$el.slick({
			infinite: true,
			autoplay: true,
			speed: 300,
			dots: false,
			arrows: true,
			appendArrows: navSelector || false,
		});
	}

	function renderCategoryShops(categories) {
		const row = document.getElementById('home-shops-row');
		if (!row) {
			return;
		}

		const items = categories.slice(0, 3);

		if (!items.length) {
			row.innerHTML =
				'<div class="col-md-12"><p>No categories yet.</p></div>';
			return;
		}

		row.innerHTML = items.map(function (category, index) {
			const img = SHOP_IMAGES[index % SHOP_IMAGES.length];
			const title = escapeHtml(category.name) + '<br>Collection';

			return (
				'<div class="col-md-4 col-xs-6">' +
					'<div class="shop">' +
						'<div class="shop-img">' +
							'<img src="' + img + '" alt="">' +
						'</div>' +
						'<div class="shop-body">' +
							'<h3>' + title + '</h3>' +
							'<a href="/store?category=' + category.id + '" ' +
								'class="cta-btn">Shop now ' +
								'<i class="fa fa-arrow-circle-right"></i></a>' +
						'</div>' +
					'</div>' +
				'</div>'
			);
		}).join('');
	}

	function renderTabNav(navId, categories, onSelect) {
		const nav = document.getElementById(navId);
		if (!nav) {
			return;
		}

		let html = '<li class="active">' +
			'<a href="#" data-category-id="">All</a></li>';

		categories.slice(0, 4).forEach(function (category) {
			html += '<li><a href="#" data-category-id="' +
				category.id + '">' + escapeHtml(category.name) + '</a></li>';
		});

		nav.innerHTML = html;

		nav.querySelectorAll('a').forEach(function (link) {
			link.addEventListener('click', function (e) {
				e.preventDefault();
				nav.querySelectorAll('li').forEach(function (li) {
					li.classList.remove('active');
				});
				link.parentElement.classList.add('active');

				const raw = link.getAttribute('data-category-id');
				const categoryId = raw ? Number(raw) : null;
				onSelect(categoryId);
			});
		});
	}

	function productsForCategory(categoryId) {
		let list = allProducts.slice();

		if (categoryId) {
			list = list.filter(function (p) {
				return p.category_id === categoryId;
			});
		}

		return list
			.sort(function (a, b) {
				return new Date(b.created_at) - new Date(a.created_at);
			})
			.slice(0, 10);
	}

	function renderNewProductsSlick(categoryId) {
		const products = productsForCategory(categoryId);
		const $slick = $('#home-new-products-slick');

		$slick.html(
			products.length
				? products.map(renderProductSlickCard).join('')
				: '<div class="col-md-12"><p>No products found.</p></div>'
		);

		initProductSlick($slick, '#slick-nav-1');
	}

	function renderTopSellingSlick(products) {
		const $slick = $('#home-top-selling-slick');

		$slick.html(
			products.length
				? products.map(renderProductSlickCard).join('')
				: '<div class="col-md-12"><p>No top selling products yet.</p></div>'
		);

		initProductSlick($slick, '#slick-nav-2');
	}

	function topSellingForCategory(categoryId) {
		let list = allProducts.slice();

		if (categoryId) {
			list = list.filter(function (p) {
				return p.category_id === categoryId;
			});
		}

		return list
			.sort(function (a, b) {
				return (b.sales || 0) - (a.sales || 0);
			})
			.slice(0, 10);
	}

	function renderWidgetColumn(columnIndex, category) {
		const slickId = 'home-widget-slick-' + columnIndex;
		const navId = '#slick-nav-' + (columnIndex + 3);
		const titleEl = document.getElementById(
			'home-widget-title-' + columnIndex
		);

		if (titleEl && category) {
			titleEl.textContent = category.name;
		}

		const products = topSellingForCategory(
			category ? category.id : null
		).slice(0, 6);

		const $slick = $('#' + slickId);
		$slick.html(widgetSlidesHtml(products));
		initWidgetSlick($slick, navId);
	}

	async function addToCart(productId) {
		const token = localStorage.getItem('auth_token');
		if (!token) {
			window.location.href = '/login';
			return;
		}

		const res = await fetch(API + '/cart-items', {
			method: 'POST',
			headers: authHeaders(),
			body: JSON.stringify({
				product_id: productId,
				quantity: 1,
			}),
		});

		const body = await res.json().catch(function () { return {}; });

		if (!res.ok) {
			alert(body.message || 'Could not add to cart');
			return;
		}

		alert('Product added to cart');

		if (typeof window.reloadSiteLayout === 'function') {
			window.reloadSiteLayout();
		}
	}

	function bindAddToCart() {
		$(document).on('click', '.home-add-to-cart', function (e) {
			e.preventDefault();
			const id = Number(this.getAttribute('data-product-id'));
			if (id) {
				addToCart(id);
			}
		});
	}

	async function boot() {
		try {
			const [catRes, prodRes] = await Promise.all([
				fetch(API + '/categories'),
				fetch(API + '/products'),
			]);

			if (!catRes.ok || !prodRes.ok) {
				throw new Error('Failed to load home data');
			}

			allCategories = (await catRes.json()).data || [];
			allProducts = (await prodRes.json()).data || [];

			renderCategoryShops(allCategories);

			renderTabNav('home-new-tab-nav', allCategories, function (categoryId) {
				renderNewProductsSlick(categoryId);
			});

			renderTabNav('home-top-tab-nav', allCategories, function (categoryId) {
				renderTopSellingSlick(topSellingForCategory(categoryId));
			});

			renderNewProductsSlick(null);
			renderTopSellingSlick(topSellingForCategory(null));

			const widgetCategories = allCategories.slice(0, 3);
			for (let i = 0; i < 3; i++) {
				renderWidgetColumn(i + 1, widgetCategories[i] || null);
			}

			const hotDealLink = document.getElementById('home-hot-deal-btn');
			if (hotDealLink) {
				hotDealLink.href = '/store';
			}
		} catch (e) {
			console.error(e);
		}
	}

	bindAddToCart();

	$(document).ready(function () {
		boot();
	});
})(jQuery);
