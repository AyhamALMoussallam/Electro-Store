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
@stack('page-scripts')
