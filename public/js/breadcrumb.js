(function () {
	'use strict';

	const t = window.ElectroI18n ? window.ElectroI18n.t.bind(window.ElectroI18n) : function (k, r) {
		let text = k;
		if (r) {
			Object.keys(r).forEach(function (key) {
				text = text.replace('{' + key + '}', r[key]);
			});
		}
		return text;
	};

	function escapeHtml(text) {
		const div = document.createElement('div');
		div.textContent = text == null ? '' : String(text);
		return div.innerHTML;
	}

	window.renderBreadcrumb = function (items) {
		const ul = document.getElementById('breadcrumb-tree');

		if (!ul || !Array.isArray(items)) {
			return;
		}

		ul.innerHTML = items.map(function (item) {
			if (item.active) {
				return '<li class="active">' + escapeHtml(item.label) + '</li>';
			}

			const href = item.url || '#';
			return '<li><a href="' + escapeHtml(href) + '">' +
				escapeHtml(item.label) + '</a></li>';
		}).join('');
	};

	window.buildStoreBreadcrumb = function (options) {
		const categories = options.categories || [];
		const filteredCount = options.filteredCount || 0;
		const selectedCategoryIds = options.selectedCategoryIds || [];
		const urlCategoryId = options.urlCategoryId;
		const searchQuery = (options.searchQuery || '').trim();

		const items = [
			{ label: t('home'), url: '/home/' },
			{ label: t('store'), url: '/store/' },
		];

		let categoryId = null;

		if (selectedCategoryIds.length === 1) {
			categoryId = selectedCategoryIds[0];
		} else if (urlCategoryId && !selectedCategoryIds.length) {
			categoryId = Number(urlCategoryId);
		}

		const category = categoryId
			? categories.find(function (c) { return c.id === categoryId; })
			: null;

		const countLabel = filteredCount.toLocaleString('ar') + ' ' + t('results');

		if (searchQuery) {
			items.push({
				label: t('searchLabel') + ': "' + searchQuery + '" (' + countLabel + ')',
				active: true,
			});
		} else if (category) {
			items.push({
				label: category.name + ' (' + countLabel + ')',
				active: true,
			});
		} else {
			items.push({
				label: t('allProducts') + ' (' + countLabel + ')',
				active: true,
			});
		}

		return items;
	};

	window.buildProductBreadcrumb = function (product) {
		const items = [
			{ label: t('home'), url: '/home/' },
			{ label: t('store'), url: '/store/' },
		];

		if (product.category) {
			items.push({
				label: product.category.name,
				url: '/store/?category=' + product.category_id,
			});
		}

		items.push({
			label: product.name,
			active: true,
		});

		return items;
	};
})();
