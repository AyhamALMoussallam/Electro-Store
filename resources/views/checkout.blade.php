<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
@include('partials.electro-head', ['title' => 'Electro - Checkout'])
</head>
<body data-active-nav="store">

@include('partials.electro-header', ['activeNav' => 'store'])

		@include('partials.electro-breadcrumb', [
			'breadcrumbHeader' => 'إتمام الشراء',
			'breadcrumbItems' => [
				['label' => 'الرئيسية', 'url' => '/home/'],
				['label' => 'المتجر', 'url' => '/store/'],
				['label' => 'إتمام الشراء', 'active' => true],
			],
		])

		<div class="section">
			<div class="container">
				<div class="row">

					<div class="col-md-7">
						<div class="shipping-details">
							<div class="section-title">
								<h3 class="title">عنوان التوصيل</h3>
							</div>
							
								<div class="form-group">
									<select class="input" name="city" id="city-select" required>
										<option value="">اختر المدينة</option>

										@foreach($cities as $city)
											<option value="{{ $city->id }}">
												{{ $city->name }}
											</option>
										@endforeach

									</select>
								</div>
								<div class="form-group">
									<select class="input" name="area" id="area-select" required>
										<option value="">اختر المنطقة</option>
									</select>
								</div>

						</div>

							<div class="section-title">
								<h3 class="title">ملاحظات الطلب</h3>
							</div>
						<div class="order-notes">
							<textarea
								class="input"
								id="order-note"
								placeholder="ملاحظات الطلب"
							></textarea>
						</div>
					</div>

					<div class="col-md-5 order-details">

						<div class="section-title text-center">
							<h3 class="title">طلبك</h3>
						</div>

						<div class="order-summary">

							<div class="order-col">
								<div><strong>المنتج</strong></div>
								<div><strong>الإجمالي</strong></div>
							</div>

							<div
								class="order-products"
								id="checkout-products"
							></div>

							<div class="order-col">
								<div>التوصيل</div>
									<div>
										<strong id="shipping-fee">مجاني</strong>
									</div>
								</div>

							<div class="order-col">
								<div><strong>الإجمالي</strong></div>
								<div>
									<strong class="order-total" id="checkout-total">0 SP</strong>
								</div>
							</div>

						</div>

						<div class="input-checkbox">
							<input type="checkbox" id="terms">
							<label for="terms">
								<span></span>
								قرأت وأوافق على
								<a href="#">الشروط والأحكام</a>
							</label>
						</div>

							<button class="primary-btn order-submit" onclick="placeOrder()">
								تأكيد الطلب
							</button>

					</div>
				</div>
			</div>
		</div>

				@include('partials.electro-footer')

		@include('partials.electro-scripts', [])

		<script>
		const t = window.ElectroI18n ? window.ElectroI18n.t.bind(window.ElectroI18n) : function (k) { return k; };

		let shippingFee = 0;
		let cartSubtotal = 0;

		async function loadCheckoutCart() {
			let token = localStorage.getItem("auth_token");

			let res = await fetch("/api/cart-items", {
				headers: {
					"Authorization": "Bearer " + token,
					"Accept": "application/json"
				}
			});

			let data = await res.json();
			let items = data.data;
			let html = "";
			cartSubtotal = 0;

			items.forEach(item => {
				let subtotal = item.quantity * item.product.price;
				cartSubtotal += subtotal;

				html += `
					<div class="order-col">
						<div>${item.quantity}x ${item.product.name}</div>
						<div>${formatPrice(subtotal)}</div>
					</div>
				`;
			});

			document.getElementById("checkout-products").innerHTML = html;
			updateCheckoutTotal();
		}

		function updateCheckoutTotal() {
			let finalTotal = cartSubtotal + shippingFee;
			document.getElementById("checkout-total").innerText = formatPrice(finalTotal);
		}

		loadCheckoutCart();

		document.getElementById("city-select").addEventListener("change", async function () {
			let cityId = this.value;
			let areaSelect = document.getElementById("area-select");

			areaSelect.innerHTML = '<option>' + t('loading') + '</option>';

			if (!cityId) {
				areaSelect.innerHTML = '<option value="">' + t('selectArea') + '</option>';
				return;
			}

			let res = await fetch(`/api/cities/${cityId}/areas`);
			let data = await res.json();

			areaSelect.innerHTML = '<option value="">' + t('selectArea') + '</option>';

			data.data.forEach(area => {
				areaSelect.innerHTML += `<option value="${area.id}">${area.name}</option>`;
			});
		});

document.getElementById("area-select").addEventListener("change", async function () {
    let areaId = this.value;

    if (!areaId) {
        shippingFee = 0;
        document.getElementById("shipping-fee").innerText = t('free');
        updateCheckoutTotal();
        return;
    }

    let res = await fetch(`/api/areas/${areaId}`);
    let data = await res.json();
    let area = data.data;

    shippingFee = parseFloat(area.fee) || 0;

    document.getElementById("shipping-fee").innerText =
        shippingFee > 0 ? formatPrice(shippingFee) : t('free');

    updateCheckoutTotal();
});

async function placeOrder() {
	let token = localStorage.getItem("auth_token");
	let areaId = document.getElementById("area-select").value;
	let note = document.getElementById("order-note").value;
	let terms = document.getElementById("terms").checked;

	if (!areaId) {
		alert(t('selectAreaRequired'));
		return;
	}

	if (!terms) {
		alert(t('acceptTermsRequired'));
		return;
	}

	try {
		let res = await fetch("/api/orders", {
			method: "POST",
			headers: {
				"Content-Type": "application/json",
				"Authorization": "Bearer " + token,
				"Accept": "application/json"
			},
			body: JSON.stringify({ area_id: areaId, note: note })
		});

		let data = await res.json();

		if (!res.ok) {
			alert(data.message || t('orderFailed'));
			return;
		}

		alert(t('orderSuccess'));

		document.getElementById("checkout-products").innerHTML = "";
		document.getElementById("checkout-total").innerText = formatPrice(0);
		document.getElementById("shipping-fee").innerText = t('free');
		document.getElementById("order-note").value = "";
		shippingFee = 0;
		cartSubtotal = 0;

	} catch (err) {
		console.error(err);
		alert(t('somethingWrong'));
	}
}
		</script>	
	</body>
</html>
