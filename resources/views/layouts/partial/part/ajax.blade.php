<script type="text/javascript">
  $(document).ready(function(){
    var listArr = [];
    var row = '';
    var selector = $('.list_pegawai');
    $.ajax({
      url: "{{ route('list_pegawai') }}",
      data: '',
      success: function(res){
        for(i = 0; i < res.response.length; i++) {
          row += "<tr>";
          row += "<td></td>";
          row += "<td>"+res.response[i].nip+"</td>";
          row += "<td>"+res.response[i].nama+"</td>";
          row += "<td>"+res.response[i].jabatan.jabatan+"</td>";
          row += "<td>"+res.response[i].jns_kel+"</td></tr>";
          row += "</tr>";
        }
        selector.html(row);
      }
    });
  });
</script>
