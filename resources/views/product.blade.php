<!DOCTYPE html>
<html lang="en">
<head>
@include('partials.electro-head', ['title' => 'Electro - Product'])
<link type="text/css" rel="stylesheet" href="/css/slick.css"/>
<link type="text/css" rel="stylesheet" href="/css/slick-theme.css"/>
<link type="text/css" rel="stylesheet" href="/css/nouislider.min.css"/>
<style>
	/* Main image: keep theme arrows visible (store default hides until hover) */
	#product-main-img .slick-prev,
	#product-main-img .slick-next {
		opacity: 1;
		visibility: visible;
		transform: translateX(0);
	}

	/* Thumbnail arrows sit above/below images, not on top of them */
	.product-thumbs-column {
		display: flex;
		flex-direction: column;
		align-items: center;
		gap: 5px;
	}

	.product-thumbs-arrow-slot {
		display: flex;
		justify-content: center;
		width: 100%;
		flex-shrink: 0;
	}

	.product-thumbs-arrow-slot .slick-prev,
	.product-thumbs-arrow-slot .slick-next {
		position: relative;
		top: auto;
		bottom: auto;
		left: auto;
		right: auto;
		-webkit-transform: none;
		-ms-transform: none;
		transform: none;
		width: 40px;
		height: 40px;
		border: 1px solid #D10024;
		background-color: #D10024;
		border-radius: 50%;
		opacity: 1;
		visibility: visible;
		z-index: 1;
	}

	.product-thumbs-arrow-slot .slick-prev:before,
	.product-thumbs-arrow-slot .slick-next:before {
		font-family: FontAwesome;
		color: #FFF;
	}

	.product-thumbs-arrow-slot .slick-prev:before {
		content: "\f106";
	}

	.product-thumbs-arrow-slot .slick-next:before {
		content: "\f107";
	}

	.product-thumbs-arrow-slot .slick-prev:hover,
	.product-thumbs-arrow-slot .slick-prev:focus,
	.product-thumbs-arrow-slot .slick-next:hover,
	.product-thumbs-arrow-slot .slick-next:focus {
		background-color: #D10024;
		border-color: #D10024;
	}

	.product-thumbs-arrow-slot .slick-prev:hover:before,
	.product-thumbs-arrow-slot .slick-prev:focus:before,
	.product-thumbs-arrow-slot .slick-next:hover:before,
	.product-thumbs-arrow-slot .slick-next:focus:before {
		color: #FFF;
	}

	#product-imgs {
		width: 100%;
	}
</style>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
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
					<!-- Product main img -->
					
						<div class="col-md-5 col-md-push-2">
							<div id="product-main-img">
								<div class="product-preview">
									<img class="product-main-image" src="" alt="">
								</div>

								<div class="product-preview">
									<img class="product-main-image" src="" alt="">
								</div>

								<div class="product-preview">
									<img class="product-main-image" src="" alt="">
								</div>

								<div class="product-preview">
									<img class="product-main-image" src="" alt="">
								</div>
							</div>
						</div>
					
					<!-- /Product main img -->

					<!-- Product thumb imgs -->
					<div class="col-md-2  col-md-pull-5">
						<div class="product-thumbs-column">
							<div id="product-thumbs-prev" class="product-thumbs-arrow-slot"></div>
						<div id="product-imgs">
							<div class="product-preview">
								<img class="product-thumb-image" src="" alt="">
							</div>

							<div class="product-preview">
								<img class="product-thumb-image" src="" alt="">
							</div>

							<div class="product-preview">
								<img class="product-thumb-image" src="" alt="">
							</div>

							<div class="product-preview">
								<img class="product-thumb-image" src="" alt="">
							</div>
						</div>
							<div id="product-thumbs-next" class="product-thumbs-arrow-slot"></div>
						</div>
					</div>
					<!-- /Product thumb imgs -->

					<!-- Product details -->
					<div class="col-md-5">
						<div class="product-details">
							<h2 id="product-name"></h2>
							
							<div>
								<h3 id="product-price"></h3>
								<span id="product-stock"></span>
							</div>
							<br>
							<p id="product-description"></p>

							

							<div class="add-to-cart">
								<div class="qty-label">
									Qty
									<div class="input-number">
										<input
											type="number"
											id="quantity"
											value="1"
											min="1"
										>
										<span class="qty-up">+</span>
										<span class="qty-down">-</span>
									</div>
								</div>

								<button
									class="add-to-cart-btn"
									id="add-to-cart-btn"
								>
									<i class="fa fa-shopping-cart"></i>
									add to cart
								</button>
							</div>

							

							<ul class="product-links">
								<li>Category:</li>
								<li><a href="#" id="product-category"></a></li>
							</ul>

							<ul class="product-links">
								<li>Share:</li>
								<li>
									<a href="#" id="copy-link-btn">
										<i class="fa fa-link"></i> Copy Link
									</a>
								</li>
							</ul>

						</div>
					</div>
					<!-- /Product details -->

					<!-- Product tab -->
					<div class="col-md-12">
						<div id="product-tab">
							<!-- product tab nav -->
							<ul class="tab-nav">
								<li class="active"><a data-toggle="tab" href="#tab3">Reviews</a></li>
							</ul>
							<!-- /product tab nav -->

							

								<!-- tab3  -->
								<div id="tab3" class="tab-pane fade in">
									<div class="row">
										<!-- Rating -->
										<div class="col-md-3">
											<div id="rating">
												<div class="rating-avg">
												<span>0.0</span>
												<div class="rating-stars"></div>
											</div>
												<ul class="rating">
													<li>
														<div class="rating-stars">
															<i class="fa fa-star"></i>
															<i class="fa fa-star"></i>
															<i class="fa fa-star"></i>
															<i class="fa fa-star"></i>
															<i class="fa fa-star"></i>
														</div>
														<div class="rating-progress">
															<div style="width: 80%;"></div>
														</div>
														<span class="sum">3</span>
													</li>
													<li>
														<div class="rating-stars">
															<i class="fa fa-star"></i>
															<i class="fa fa-star"></i>
															<i class="fa fa-star"></i>
															<i class="fa fa-star"></i>
															<i class="fa fa-star-o"></i>
														</div>
														<div class="rating-progress">
															<div style="width: 60%;"></div>
														</div>
														<span class="sum">2</span>
													</li>
													<li>
														<div class="rating-stars">
															<i class="fa fa-star"></i>
															<i class="fa fa-star"></i>
															<i class="fa fa-star"></i>
															<i class="fa fa-star-o"></i>
															<i class="fa fa-star-o"></i>
														</div>
														<div class="rating-progress">
															<div></div>
														</div>
														<span class="sum">0</span>
													</li>
													<li>
														<div class="rating-stars">
															<i class="fa fa-star"></i>
															<i class="fa fa-star"></i>
															<i class="fa fa-star-o"></i>
															<i class="fa fa-star-o"></i>
															<i class="fa fa-star-o"></i>
														</div>
														<div class="rating-progress">
															<div></div>
														</div>
														<span class="sum">0</span>
													</li>
													<li>
														<div class="rating-stars">
															<i class="fa fa-star"></i>
															<i class="fa fa-star-o"></i>
															<i class="fa fa-star-o"></i>
															<i class="fa fa-star-o"></i>
															<i class="fa fa-star-o"></i>
														</div>
														<div class="rating-progress">
															<div></div>
														</div>
														<span class="sum">0</span>
													</li>
												</ul>
											</div>
										</div>
										<!-- /Rating -->

										<!-- Reviews -->
										<div class="col-md-6">
											<div id="reviews">
												<ul class="reviews" id="reviews-list"></ul>

												<ul class="reviews-pagination" id="reviews-pagination"></ul>
											</div>
										</div>
										<!-- /Reviews -->

										<!-- Review Form -->
										<div class="col-md-3">
											<div id="review-form">
												<form class="review-form">
													<textarea class="input" id="review-comment" placeholder="Your Review"></textarea>
													
													<div class="input-rating">
														<span>Your Rating: </span>
														<div class="stars">
															<input id="star5" name="rating" value="5" type="radio"><label for="star5"></label>
															<input id="star4" name="rating" value="4" type="radio"><label for="star4"></label>
															<input id="star3" name="rating" value="3" type="radio"><label for="star3"></label>
															<input id="star2" name="rating" value="2" type="radio"><label for="star2"></label>
															<input id="star1" name="rating" value="1" type="radio"><label for="star1"></label>
														</div>
													</div>
													<button class="primary-btn">Submit</button>
												</form>
											</div>
										</div>
										<!-- /Review Form -->
									</div>
								</div>
								<!-- /tab3  -->
							</div>
							<!-- /product tab content  -->
						</div>
					</div>
					<!-- /product tab -->
				</div>
				<!-- /row -->
			</div>
			<!-- /container -->
		</div>
		<!-- /SECTION -->

		<!-- Section -->
		<div class="section">
			<!-- container -->
			<div class="container">
				<!-- row -->
				<div class="row">

					<div class="col-md-12">
						<div class="section-title text-center">
							<h3 class="title">Related Products</h3>
						</div>
					</div>

					<!-- product -->
					<div class="row" id="related-products"></div>

				</div>
				<!-- /row -->
			</div>
			<!-- /container -->
		</div>
		<!-- /Section -->

				@include('partials.electro-footer')

		@include('partials.electro-scripts', ['withSlick' => true, 'withNouislider' => true, 'withZoom' => true])




		<script>


			axios.defaults.headers.common['Authorization'] =
   			 'Bearer ' + localStorage.getItem('auth_token');

		const productId =
			new URLSearchParams(window.location.search)
			.get('id');

		let currentProduct = null;


		// ======================
		// LOAD PRODUCT
		// ======================
		axios.get(`/api/products/${productId}`)
		.then(res => {

		const product = res.data.data;

		currentProduct = product;
		const qtyInput = document.getElementById('quantity');

		qtyInput.max = product.stock;

		if (product.stock <= 0) {
			qtyInput.disabled = true;

			document.querySelector('.add-to-cart-btn').disabled = true;
		}

		document.getElementById('product-name').textContent =
			product.name;

		if (typeof window.buildProductBreadcrumb === 'function') {
			window.renderBreadcrumb(
				window.buildProductBreadcrumb(product)
			);
		}

		document.getElementById('product-category').textContent =
    		product.category?.name ?? 'No Category';

		document.getElementById('product-price').textContent =
			'$' + product.price;

		document.getElementById('product-description').innerHTML =
    		product.description.replace(/\n/g, '<br>');

		document.getElementById('product-stock').textContent =
			product.stock > 0
				? `In Stock (${product.stock})`
				: 'Out Of Stock';

		const productImagePath = '/storage/' + product.image;

		document.querySelectorAll('.product-main-image')
			.forEach(image => {
				image.src = productImagePath;
			});

		document.querySelectorAll('.product-thumb-image')
			.forEach(image => {
				image.src = productImagePath;
			});

		const firstProductImage =
			document.querySelector('.product-main-image');

		const relocateThumbArrows = () => {
			const $thumbs = $('#product-imgs');

			$thumbs.find('.slick-prev')
				.appendTo('#product-thumbs-prev');
			$thumbs.find('.slick-next')
				.appendTo('#product-thumbs-next');
		};

		const initProductGallery = () => {
			if (typeof window.initProductImageSliders !== 'function') {
				return;
			}

			window.initProductImageSliders();
			relocateThumbArrows();

			requestAnimationFrame(() => {
				$('#product-main-img').slick('setPosition');
				$('#product-imgs').slick('setPosition');
				relocateThumbArrows();
			});
		};

		if (firstProductImage.complete) {
			initProductGallery();
		} else {
			firstProductImage.addEventListener(
				'load',
				initProductGallery,
				{ once: true }
			);
		}



		document.getElementById('copy-link-btn').addEventListener('click', function (e) {
			e.preventDefault();

			const url = window.location.href;

			navigator.clipboard.writeText(url)
				.then(() => {
					alert('Link copied!');
				})
				.catch(() => {
					alert('Failed to copy link');
				});
		});	 

		loadRelatedProducts();

		loadReviews(productId); 

		updateRatingUI(reviews);
	})
		.catch(error => {

			console.log(error);

			alert('Product not found');

		});


		// ======================
		// RELATED PRODUCTS
		// ======================
		function loadRelatedProducts() {

			axios.get('/api/products')
			.then(res => {

				const products = res.data.data;

				const related = products
					.filter(p =>

						p.id != currentProduct.id &&
						p.category_id ==
						currentProduct.category_id

					)
					.slice(0, 4);

				renderRelatedProducts(related);

			});
		}


		// ======================
		// RENDER RELATED
		// ======================
		function renderRelatedProducts(products) {

			const container =
				document.getElementById(
					'related-products'
				);

			container.innerHTML = '';

			products.forEach(product => {

				container.innerHTML += `

				<div class="col-md-3 col-xs-6">

					<div class="product"
						style="cursor:pointer"
						onclick="window.location.href='/product?id=${product.id}'">

						<div class="product-img">

							<img
								src="/storage/${product.image}"
								alt=""
							>

						</div>

						<div class="product-body">

							<p class="product-category">
								${product.category?.name ?? ''}
							</p>

							<h3 class="product-name">

								<a href="/product?id=${product.id}">
									${product.name}
								</a>

							</h3>

							<h4 class="product-price">
								$${product.price}
							</h4>

						</div>

					</div>

				</div>

				`;
			});
		}



		const reviewsPerPage = 4;
		let allReviews = [];
		let currentPage = 1;

function loadReviews(productId) {

    axios.get(`/api/products/${productId}/reviews`)
    .then(res => {

        allReviews = res.data;

        renderReviewsPage(1);

        updateRatingUI(allReviews);

    })
    .catch(err => {
        console.log(err);
    });

}

function renderReviewsPage(page) {

    currentPage = page;

    const container = document.getElementById('reviews-list');

    container.innerHTML = '';

    const start = (page - 1) * reviewsPerPage;
    const end = start + reviewsPerPage;

    const reviews = allReviews.slice(start, end);

    reviews.forEach(review => {

        container.innerHTML += `
            <li>
                <div class="review-heading">
                    <h5 class="name">${review.user.name}</h5>
                    <p class="date">
                        ${new Date(review.created_at).toLocaleString()}
                    </p>
                    <div class="review-rating">
                        ${renderStars(review.rating)}
                    </div>
                </div>

                <div class="review-body">
                    <p>${review.comment}</p>
                </div>
            </li>
        `;
    });

    renderPagination();
}




function renderPagination() {

    const totalPages = Math.ceil(
        allReviews.length / reviewsPerPage
    );

    const pagination =
        document.getElementById('reviews-pagination');

    pagination.innerHTML = '';

    for (let i = 1; i <= totalPages; i++) {

        pagination.innerHTML += `
            <li class="${i === currentPage ? 'active' : ''}">
                <a href="#" onclick="renderReviewsPage(${i}); return false;">
                    ${i}
                </a>
            </li>
        `;
    }
}
	



		function renderStars(rating) {
    let stars = '';

    for (let i = 1; i <= 5; i++) {
        stars += i <= rating
            ? '<i class="fa fa-star"></i>'
            : '<i class="fa fa-star-o"></i>';
    }

    return stars;
}


function updateRatingUI(reviews) {

    if (!reviews.length) {
        setAvgRating(0);
        setDistribution([]);
        return;
    }

    let sum = 0;
    let counts = [0, 0, 0, 0, 0];

    reviews.forEach(r => {
        sum += r.rating;
        counts[r.rating - 1]++;
    });

    const avg = sum / reviews.length;

    setAvgRating(avg);
    setDistribution(counts, reviews.length);
}



function setAvgRating(avg) {

    document.querySelector('.rating-avg span')
        .textContent = avg.toFixed(1);

    const stars = document.querySelector('.rating-avg .rating-stars');

    stars.innerHTML = renderStars(Math.round(avg));
}



function setDistribution(counts, total) {

    const items = document.querySelectorAll('#rating ul.rating li');

    for (let i = 5; i >= 1; i--) {

        const count = counts[i - 1];
        const percent = total ? (count / total) * 100 : 0;

        const row = items[5 - i];

        const bar = row.querySelector('.rating-progress div');
        const sum = row.querySelector('.sum');

        bar.style.width = percent + '%';
        sum.textContent = count;
    }
}


		document.querySelector('.review-form').addEventListener('submit', function(e) {
			e.preventDefault();

			const comment = document.getElementById('review-comment').value;
			const rating = document.querySelector('input[name="rating"]:checked')?.value;

			axios.post('/api/reviews', {
				product_id: productId,
				comment: comment,
				rating: rating
			})
			.then(() => {
				loadReviews(productId);
			})
			.catch(err => {
				alert(err.response?.data?.message || 'Error');
			});
		});


		


		document.querySelector('.add-to-cart-btn')
		.addEventListener('click', function () {

			const qty = parseInt(
				document.getElementById('quantity').value
			);

			if (qty > currentProduct.stock) {

				alert(
					`Only ${currentProduct.stock} item(s) available in stock`
				);

				return;
			}

			if (qty < 1) {

				alert('Quantity must be at least 1');

				return;
			}

			axios.post('/api/cart-items', {
				product_id: currentProduct.id,
				quantity: qty
			})
			.then(res => {

				alert('Product added to cart');

				if (typeof window.reloadSiteLayout === 'function') {
					window.reloadSiteLayout();
				}

			})
			.catch(err => {

				alert(
					err.response?.data?.message ||
					'Error adding product'
				);

			});

		});


		</script>

	</body>
</html>
