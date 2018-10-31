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
          row += "<td><div class='img-user' id='user1' style='background-image: url({{ asset('assets/images/img-user.png') }});'></div></td>";
          row += "<td>"+res.response.data[i].nip+"</td>";
          row += "<td>"+res.response.data[i].nama+"</td>";
          row += "<td>"+res.response.data[i].jabatan.jabatan+"</td>";
          row += "<td>"+res.response.data[i].jns_kel+"</td>";
          row += "<td><div class='btn-group mr-2' role='group' aria-label='Edit'><button type='button' class='btn btn-success'><i class='fas fa-edit'></i></button><button type='button' class='btn btn-danger'><i class='fas fa-trash'></i></button></div></td>";
          row += "</tr>";
        }
        selector.html(row);
      }
    });
  });
</script>
