<script type="text/javascript">
  $(document).ready(function(){
    var row = '';
    var selector = $('.list_pegawai');
    $.ajax({
      url: "{{ route('list_pegawai') }}",
      data: '',
      success: function(res){
        for(i = 0; i < res.response.data.length; i++) {
          row += "<tr>";
          row += "<td></td>";
          row += "<td>"+res.response.data[i].nip+"</td>";
          row += "<td>"+res.response.data[i].nama+"</td>";
          row += "<td>"+res.response.data[i].jabatan.jabatan+"</td>";
          row += "<td>"+res.response.data[i].jns_kel+"</td></tr>";
          row += "</tr>";
        }
        selector.html(row);
      }
    });
  });
</script>
