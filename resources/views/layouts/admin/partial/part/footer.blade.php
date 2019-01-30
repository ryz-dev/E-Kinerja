<script src="{{ asset('assets/js/bundle.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.twbsPagination.min.js') }}"></script>
<script src="{{ asset('assets/vendor/sweetalert/sweetalert2.all.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/gijgo@1.9.10/js/gijgo.min.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.full.js"></script>
<script type="text/javascript">
  $("#preload").delay(500).fadeOut("slow");
  $('.select2').select2();
</script>
@stack('script')
