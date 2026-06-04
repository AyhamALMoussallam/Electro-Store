(function () {
	'use strict';

	function escapeHtml(text) {
		const div = document.createElement('div');
		div.textContent = text == null ? '' : String(text);
		return div.innerHTML;
	}

	/**
	 * @param {Array<{label: string, url?: string, active?: boolean}>} items
	 */
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
			{ label: 'Home', url: '/home/' },
			{ label: 'Store', url: '/store/' },
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

		const countLabel = filteredCount.toLocaleString() + ' Results';

		if (searchQuery) {
			items.push({
				label: 'Search: "' + searchQuery + '" (' + countLabel + ')',
				active: true,
			});
		} else if (category) {
			items.push({
				label: category.name + ' (' + countLabel + ')',
				active: true,
			});
		} else {
			items.push({
				label: 'All Products (' + countLabel + ')',
				active: true,
			});
		}

		return items;
	};

	window.buildProductBreadcrumb = function (product) {
		const items = [
			{ label: 'Home', url: '/home/' },
			{ label: 'Store', url: '/store/' },
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
