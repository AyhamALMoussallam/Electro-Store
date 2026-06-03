<!DOCTYPE html>
<html lang="en">
<head>
@include('partials.electro-head', ['title' => 'Electro - Checkout'])
</head>
<body data-active-nav="store">

@include('partials.electro-header', ['activeNav' => 'store'])

		<!-- BREADCRUMB -->
		<div id="breadcrumb" class="section">
			<!-- container -->
			<div class="container">
				<!-- row -->
				<div class="row">
					<div class="col-md-12">
						<h3 class="breadcrumb-header">Checkout</h3>
						<ul class="breadcrumb-tree">
							<li><a href="#">Home</a></li>
							<li class="active">Checkout</li>
						</ul>
					</div>
				</div>
				<!-- /row -->
			</div>
			<!-- /container -->
		</div>
		<!-- /BREADCRUMB -->

		<!-- SECTION -->
		<div class="section">
			<!-- container -->
			<div class="container">
				<!-- row -->
				<div class="row">

					<div class="col-md-7">
						<!-- Shipping Details -->
						<div class="shipping-details">
							<div class="section-title">
								<h3 class="title">Shipping address</h3>
							</div>
							
								<div class="form-group">
									<select class="input" name="city" id="city-select" required>
										<option value="">Select City</option>

										@foreach($cities as $city)
											<option value="{{ $city->id }}">
												{{ $city->name }}
											</option>
										@endforeach

									</select>
								</div>
								<div class="form-group">
									<select class="input" name="area" id="area-select" required>
										<option value="">Select Area</option>
									</select>
								</div>

						</div>
						<!-- /Shipping Details -->

							<div class="section-title">
								<h3 class="title">Order Notes</h3>
							</div>
						<!-- Order notes -->
						<div class="order-notes">
							<textarea
								class="input"
								id="order-note"
								placeholder="Order Notes"
							></textarea>
						</div>
						<!-- /Order notes -->
					</div>

					<!-- Order Details -->
					<div class="col-md-5 order-details">

						<div class="section-title text-center">
							<h3 class="title">Your Order</h3>
						</div>

						<div class="order-summary">

							<div class="order-col">
								<div><strong>PRODUCT</strong></div>
								<div><strong>TOTAL</strong></div>
							</div>

							<!-- PRODUCTS -->
							<div
								class="order-products"
								id="checkout-products"
							>

							</div>

							<div class="order-col">
								<div>Shipping</div>

									<div>
										<strong id="shipping-fee">
											FREE
										</strong>
									</div>
								</div>

							<div class="order-col">
								<div><strong>TOTAL</strong></div>

								<div>
									<strong
										class="order-total"
										id="checkout-total"
									>
										$0
									</strong>
								</div>
							</div>

						</div>


						<div class="input-checkbox">

							<input
								type="checkbox"
								id="terms"
							>

							<label for="terms">
								<span></span>

								I've read and accept the
								<a href="#">
									terms & conditions
								</a>
							</label>

						</div>

							<button
								class="primary-btn order-submit"
								onclick="placeOrder()"
							>
								Place Order
							</button>

					</div>
					<!-- /Order Details -->
				</div>
				<!-- /row -->
			</div>
			<!-- /container -->
		</div>
		<!-- /SECTION -->

				@include('partials.electro-footer')

		@include('partials.electro-scripts', [])


		<script>

		let shippingFee = 0;

		async function loadCheckoutCart() {

			let token = localStorage.getItem("auth_token");

			let res = await fetch("/api/cart-items", {
				headers: {
					"Authorization": "Bearer " + token,
					"Accept": "application/json"
				}
			});

			let data = await res.json();

			console.log(data);

			let items = data.data;

			let html = "";

			cartSubtotal = 0;

			items.forEach(item => {

				let subtotal =
					item.quantity *
					item.product.price;

				cartSubtotal += subtotal;

				html += `
					<div class="order-col">
						<div>
							${item.quantity}x
							${item.product.name}
						</div>

						<div>
							$${subtotal}
						</div>
					</div>
				`;
			});

			document.getElementById(
				"checkout-products"
			).innerHTML = html;

			updateCheckoutTotal();
		}

		let cartSubtotal = 0;


		// =========================
		// UPDATE TOTAL
		// =========================
		function updateCheckoutTotal() {

			let finalTotal =
				cartSubtotal + shippingFee;

			document.getElementById(
				"checkout-total"
			).innerText =
				"$" + finalTotal.toFixed(2);
		}


		loadCheckoutCart();


		document.getElementById("city-select").addEventListener("change", async function () {

			let cityId = this.value;
			let areaSelect = document.getElementById("area-select");

			areaSelect.innerHTML = '<option>Loading...</option>';

			if (!cityId) {
				areaSelect.innerHTML = '<option value="">Select Area</option>';
				return;
			}

			let res = await fetch(`/api/cities/${cityId}/areas`);
			let data = await res.json();

			areaSelect.innerHTML = '<option value="">Select Area</option>';

			data.data.forEach(area => {
				areaSelect.innerHTML += `
					<option value="${area.id}">${area.name}</option>
				`;
			});
		});



document.getElementById("area-select").addEventListener("change", async function () {

    let areaId = this.value;

    // no area selected
    if (!areaId) {

        shippingFee = 0;

        document.getElementById("shipping-fee").innerText =
            "FREE";

        updateCheckoutTotal();

        return;
    }

    // get area
    let res = await fetch(`/api/areas/${areaId}`);
    let data = await res.json();

    shippingFee = Number(data.data.fee ?? 0);

    // render fee
    document.getElementById("shipping-fee").innerText =
        `$${shippingFee.toFixed(2)}`;

    // update total
    updateCheckoutTotal();
});



// =========================
// PLACE ORDER
// =========================
async function placeOrder() {

	let token = localStorage.getItem("auth_token");

	let areaId =
		document.getElementById("area-select").value;

	let note =
		document.getElementById("order-note").value;

	let terms =
		document.getElementById("terms").checked;

	// validation
	if (!areaId) {
		alert("Please select area");
		return;
	}

	if (!terms) {
		alert("Please accept terms");
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
			body: JSON.stringify({
				area_id: areaId,
				note: note
			})
		});

		let data = await res.json();

		console.log(data);

		if (!res.ok) {
			alert(data.message || "Order failed");
			return;
		}

		alert("Order placed successfully");

		// clear ui
		document.getElementById(
			"checkout-products"
		).innerHTML = "";

		document.getElementById(
			"checkout-total"
		).innerText = "$0";

		document.getElementById(
			"shipping-fee"
		).innerText = "FREE";

		document.getElementById(
			"order-note"
		).value = "";

		shippingFee = 0;
		cartSubtotal = 0;

	} catch (err) {

		console.error(err);

		alert("Something went wrong");
	}
}


		</script>	
	</body>
</html>
