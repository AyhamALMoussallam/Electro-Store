@include('partials.electro-dialog')
<script src="/js/electro-i18n.js"></script>
<script src="/js/electro-currency.js"></script>
<script src="/js/electro-preferences.js"></script>
<script src="/js/electro-dialog.js"></script>
<script src="/js/jquery.min.js"></script>
<script src="/js/bootstrap.min.js"></script>
@if(!empty($withSlick))
<script src="/js/slick.min.js"></script>
@endif
@if(!empty($withNouislider))
<script src="/js/nouislider.min.js"></script>
@endif
@if(!empty($withZoom))
<script src="/js/jquery.zoom.min.js"></script>
@endif
<script src="/js/main.js"></script>
<script src="/js/layout.js"></script>
<script src="/js/breadcrumb.js"></script>
@stack('page-scripts')
