(function ($) {
	'use strict';

	const API = '/api';
	const t = window.ElectroI18n ? window.ElectroI18n.t.bind(window.ElectroI18n) : function (k) { return k; };

	function getToken() {
		return localStorage.getItem('auth_token');
	}

	function authHeaders() {
		const headers = { Accept: 'application/json' };
		const token = getToken();
		if (token) {
			headers.Authorization = 'Bearer ' + token;
		}
		return headers;
	}

	async function fetchCategories() {
		const res = await fetch(API + '/categories');
		if (!res.ok) {
			return [];
		}
		const json = await res.json();
		return json.data || [];
	}

	function fillCategorySelect(categories) {
		const select = document.getElementById('header-category-select');
		if (!select) {
			return;
		}

		const params = new URLSearchParams(window.location.search);
		const selected = params.get('category') || '';

		let html = '<option value="">' + t('allCategories') + '</option>';
		categories.forEach(function (category) {
			const active = String(category.id) === selected ? ' selected' : '';
			html += '<option value="' + category.id + '"' + active + '>' + category.name + '</option>';
		});
		select.innerHTML = html;
	}

	function renderMainNav(categories) {
		const nav = document.getElementById('main-nav-list');
		if (!nav) {
			return;
		}

		const activeNav = document.body.dataset.activeNav || '';
		let html = '';

		html += '<li class="' + (activeNav === 'home' ? 'active' : '') + '"><a href="/home">' + t('home') + '</a></li>';
		html += '<li class="' + (activeNav === 'store' ? 'active' : '') + '"><a href="/store">' + t('store') + '</a></li>';

		categories.forEach(function (category) {
			const key = 'category-' + category.id;
			html += '<li class="' + (activeNav === key ? 'active' : '') + '">';
			html += '<a href="/store?category=' + category.id + '">' + category.name + '</a></li>';
		});

		nav.innerHTML = html;
	}

	function renderFooterCategories(categories) {
		const list = document.getElementById('footer-categories');
		if (!list) {
			return;
		}

		if (!categories.length) {
			list.innerHTML = '<li><a href="/store">' + t('browseStore') + '</a></li>';
			return;
		}

		list.innerHTML = categories.map(function (category) {
			return '<li><a href="/store?category=' + category.id + '">' + category.name + '</a></li>';
		}).join('');
	}

	async function updateAccountLinks() {
		const headerLink = document.getElementById('header-account-link');
		const dashboardItem = document.getElementById('header-dashboard-item');
		const footerAccount = document.getElementById('footer-account-link');
		const footerOrders = document.getElementById('footer-orders-link');
		const token = getToken();

		function setGuestLinks() {
			if (headerLink) {
				headerLink.href = '/login';
				headerLink.innerHTML = '<i class="fa fa-user-o"></i> ' + t('signIn');
			}
			if (dashboardItem) {
				dashboardItem.style.display = 'none';
			}
			if (footerAccount) {
				footerAccount.href = '/login';
				footerAccount.textContent = t('signIn');
			}
			if (footerOrders) {
				footerOrders.style.display = 'none';
			}
		}

		if (!token) {
			setGuestLinks();
			return;
		}

		try {
			const res = await fetch(API + '/user', { headers: authHeaders() });
			if (!res.ok) {
				throw new Error('Unauthorized');
			}

			const user = (await res.json()).user;
			const isAdmin = Number(user.role) === 1;
			const accountUrl = '/profile';

			if (headerLink) {
				headerLink.href = accountUrl;
				headerLink.innerHTML = '<i class="fa fa-user-o"></i> ' + t('myAccount');
			}
			if (dashboardItem) {
				dashboardItem.style.display = isAdmin ? 'inline-block' : 'none';
			}
			if (footerAccount) {
				footerAccount.href = accountUrl;
				footerAccount.textContent = t('myAccount');
			}
			if (footerOrders) {
				const ordersLink = footerOrders.querySelector('a');
				if (isAdmin) {
					footerOrders.style.display = 'none';
				} else {
					footerOrders.style.display = 'list-item';
					if (ordersLink) {
						ordersLink.href = '/orders';
					}
				}
			}
		} catch (e) {
			localStorage.removeItem('auth_token');
			setGuestLinks();
		}
	}

	function renderCartItem(item) {
		const product = item.product || {};
		const lineTotal = (item.quantity * (product.price || 0)).toFixed(2);
		const image = product.image ? '/storage/' + product.image : '/img/product01.png';

		return (
			'<div class="product-widget" data-cart-item-id="' + item.id + '">' +
				'<div class="product-img">' +
					'<img src="' + image + '" alt="">' +
				'</div>' +
				'<div class="product-body">' +
					'<h3 class="product-name"><a href="/product?id=' + product.id + '">' + (product.name || t('product')) + '</a></h3>' +
					'<h4 class="product-price"><span class="qty">' + item.quantity + 'x</span>' + formatPrice(product.price) + '</h4>' +
				'</div>' +
				'<button type="button" class="delete" data-remove-cart="' + item.id + '"><i class="fa fa-close"></i></button>' +
			'</div>'
		);
	}

	function shouldSkipHeaderCart() {
		const body = document.body;

		if (!document.getElementById('header-cart-list')) {
			return true;
		}

		if (body.classList.contains('minimal-header-page')) {
			return true;
		}

		if (body.classList.contains('admin-profile-page')) {
			return true;
		}

		if (body.classList.contains('page-profile') && body.classList.contains('account-ui-pending')) {
			return true;
		}

		if (body.dataset.hideHeaderCart === 'pending') {
			return true;
		}

		return false;
	}

	async function loadHeaderCart() {
		const list = document.getElementById('header-cart-list');
		const qtyEl = document.getElementById('header-cart-qty');
		const summaryEl = document.getElementById('header-cart-summary');
		const subtotalEl = document.getElementById('header-cart-subtotal');
		const token = getToken();

		if (!list || shouldSkipHeaderCart()) {
			return;
		}

		if (!token) {
			list.innerHTML = '<p style="padding:15px;margin:0;color:#8D99AE;">' + t('signInForCart') + '</p>';
			if (qtyEl) qtyEl.textContent = '0';
			if (summaryEl) summaryEl.textContent = '0 ' + t('itemsSelected');
			if (subtotalEl) subtotalEl.textContent = t('subtotal') + ': ' + formatPrice(0);
			return;
		}

		try {
			const res = await fetch(API + '/cart-items', { headers: authHeaders() });
			if (!res.ok) {
				throw new Error('Cart unavailable');
			}

			const items = (await res.json()).data || [];
			let totalQty = 0;
			let subtotal = 0;

			if (!items.length) {
				list.innerHTML = '<p style="padding:15px;margin:0;color:#8D99AE;">' + t('cartEmpty') + '</p>';
			} else {
				list.innerHTML = items.map(renderCartItem).join('');
				items.forEach(function (item) {
					const price = item.product ? item.product.price : 0;
					totalQty += item.quantity;
					subtotal += item.quantity * price;
				});
			}

			if (qtyEl) qtyEl.textContent = totalQty;
			if (summaryEl) summaryEl.textContent = totalQty + ' ' + t('itemsSelected');
			if (subtotalEl) subtotalEl.textContent = t('subtotal') + ': ' + formatPrice(subtotal);
		} catch (e) {
			list.innerHTML = '<p style="padding:15px;margin:0;color:#8D99AE;">' + t('cartUnavailable') + '</p>';
		}
	}

	async function removeCartItem(id) {
		const token = getToken();
		if (!token) {
			window.location.href = '/login';
			return;
		}

		const res = await fetch(API + '/cart-items/' + id, {
			method: 'DELETE',
			headers: authHeaders(),
		});

		if (!res.ok) {
			const body = await res.json().catch(function () { return {}; });
			alert(body.message || t('cartRemoveError'));
			return;
		}

		await loadHeaderCart();
	}

	function bindCartActions() {
		// Must bind on cart-list, not document: cart-dropdown stops propagation
		$('#header-cart-list').on('click', '[data-remove-cart]', function (e) {
			e.preventDefault();
			e.stopPropagation();

			const id = this.getAttribute('data-remove-cart');
			if (id) {
				removeCartItem(id);
			}
		});
	}

	function fillSearchFromUrl() {
		const input = document.getElementById('header-search-input');
		const params = new URLSearchParams(window.location.search);
		if (input && params.get('q')) {
			input.value = params.get('q');
		}
	}

	async function initSiteLayout() {
		const categories = await fetchCategories();
		fillCategorySelect(categories);
		renderMainNav(categories);
		renderFooterCategories(categories);
		fillSearchFromUrl();
		await updateAccountLinks();

		if (!shouldSkipHeaderCart()) {
			await loadHeaderCart();
		}
	}

	bindCartActions();

	$(document).ready(function () {
		initSiteLayout();
	});

	window.reloadSiteLayout = initSiteLayout;
})(jQuery);
