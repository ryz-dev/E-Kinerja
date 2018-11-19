    </section>
  </body>
  <script src="{{ asset('assets/js/bundle.min.js') }}"></script>
  <script src="{{ asset('assets/js/jquery.twbsPagination.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/sweetalert/sweetalert2.all.min.js') }}"></script>
    <script src="{{asset('assets/js/script.js')}}"></script>
    <script type="text/javascript">
    $("#preload").delay(500).fadeOut("slow");
  </script>
  @stack('script')
</html>
