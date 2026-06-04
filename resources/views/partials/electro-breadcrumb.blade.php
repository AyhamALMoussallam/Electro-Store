<!-- BREADCRUMB -->
<div id="breadcrumb" class="section">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				@if(!empty($breadcrumbHeader))
					<h3 class="breadcrumb-header">{{ $breadcrumbHeader }}</h3>
				@endif
				<ul class="breadcrumb-tree" id="breadcrumb-tree">
					@foreach($breadcrumbItems ?? [] as $item)
						@if(!empty($item['active']))
							<li class="active">{{ $item['label'] }}</li>
						@else
							<li><a href="{{ $item['url'] ?? '#' }}">{{ $item['label'] }}</a></li>
						@endif
					@endforeach
				</ul>
			</div>
		</div>
	</div>
</div>
<!-- /BREADCRUMB -->
