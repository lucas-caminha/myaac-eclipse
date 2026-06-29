(function () {
	function initCharacterSale() {
		var root = document.querySelector('.eclipse-character-auctions');
		if (!root) return;
		if (root.getAttribute('data-auction-ready') === '1') return;
		root.setAttribute('data-auction-ready', '1');

		var list = root.querySelector('.eclipse-auction-list');
		var cards = Array.prototype.slice.call(root.querySelectorAll('[data-auction-card]'));
		var search = root.querySelector('[data-auction-search]');
		var vocation = root.querySelector('[data-auction-vocation]');
		var minLevel = root.querySelector('[data-auction-min-level]');
		var minCharm = root.querySelector('[data-auction-min-charm]');
		var maxPrice = root.querySelector('[data-auction-max-price]');
		var sort = root.querySelector('[data-auction-sort]');
		var apply = root.querySelector('[data-auction-apply]');

		function numberFrom(card, key) {
			return parseInt(card.getAttribute(key), 10) || 0;
		}

		function refreshAuctions() {
			var term = (search && search.value ? search.value : '').toLowerCase().trim();
			var vocationValue = vocation ? vocation.value : '';
			var minLevelValue = minLevel && minLevel.value ? parseInt(minLevel.value, 10) : 0;
			var minCharmValue = minCharm && minCharm.value ? parseInt(minCharm.value, 10) : 0;
			var maxPriceValue = maxPrice && maxPrice.value ? parseInt(maxPrice.value, 10) : 0;
			var sortValue = sort ? sort.value : 'recent';

			cards.sort(function (a, b) {
				if (sortValue === 'price') return numberFrom(a, 'data-price') - numberFrom(b, 'data-price');
				if (sortValue === 'level') return numberFrom(b, 'data-level') - numberFrom(a, 'data-level');
				if (sortValue === 'main') return numberFrom(b, 'data-main') - numberFrom(a, 'data-main');
				if (sortValue === 'charm') return numberFrom(b, 'data-charm') - numberFrom(a, 'data-charm');
				if (sortValue === 'magic') return numberFrom(b, 'data-magic') - numberFrom(a, 'data-magic');
				if (sortValue === 'distance') return numberFrom(b, 'data-distance') - numberFrom(a, 'data-distance');
				if (sortValue === 'shield') return numberFrom(b, 'data-shield') - numberFrom(a, 'data-shield');
				return numberFrom(b, 'data-posted') - numberFrom(a, 'data-posted');
			});

			cards.forEach(function (card) {
				var matchesName = !term || (card.getAttribute('data-name') || '').indexOf(term) !== -1;
				var matchesVocation = !vocationValue || card.getAttribute('data-vocation') === vocationValue;
				var matchesLevel = numberFrom(card, 'data-level') >= minLevelValue;
				var matchesCharm = numberFrom(card, 'data-charm') >= minCharmValue;
				var matchesPrice = !maxPriceValue || numberFrom(card, 'data-price') <= maxPriceValue;
				card.classList.toggle('is-auction-hidden', !(matchesName && matchesVocation && matchesLevel && matchesCharm && matchesPrice));
				list.appendChild(card);
			});
		}

		if (apply) apply.addEventListener('click', refreshAuctions);
		if (search) search.addEventListener('input', refreshAuctions);
		if (vocation) vocation.addEventListener('change', refreshAuctions);
		if (minLevel) minLevel.addEventListener('input', refreshAuctions);
		if (minCharm) minCharm.addEventListener('input', refreshAuctions);
		if (maxPrice) maxPrice.addEventListener('input', refreshAuctions);
		if (sort) sort.addEventListener('change', refreshAuctions);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initCharacterSale);
	} else {
		initCharacterSale();
	}

	window.addEventListener('load', initCharacterSale);
})();
