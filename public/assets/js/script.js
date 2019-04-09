// chart
$(document).ready(function() {
  var ctx1 = document.getElementById("chart-absen").getContext("2d");
  var ctx2 = document.getElementById("chart-kinerja").getContext("2d");
  var ctx3 = document.getElementById("chart-tunjangan").getContext("2d");

  var dataAbsen = {
    labels: ["Absen", ""],
    datasets: [
      {
        label: "My First dataset",
        backgroundColor: ["#6C5CE7", "#d8dadc"],
        borderColor: ["#6C5CE7", "#d8dadc"],
        data: [80, 20]
      }
    ]
  };

  var dataKinerja = {
    labels: ["Kinerja", ""],
    datasets: [
      {
        label: "My First dataset",
        backgroundColor: ["#F25857", "#d8dadc"],
        borderColor: ["#F25857", "#d8dadc"],
        data: [80, 20]
      }
    ]
  };

  var dataTunjangan = {
    labels: ["Tunjangan", ""],
    datasets: [
      {
        label: "My First dataset",
        backgroundColor: ["#0984E3", "#d8dadc"],
        borderColor: ["#0984E3", "#d8dadc"],
        data: [80, 20]
      }
    ]
  };

  // --------
  var chart = new Chart(ctx1, {
    type: "pie",
    data: dataAbsen,
    options: {
      responsive: true,
      maintainAspectRatio: true
    }
  });

  var chart2 = new Chart(ctx2, {
    type: "pie",
    data: dataKinerja,
    options: {
      responsive: true,
      maintainAspectRatio: true
    }
  });

  var chart3 = new Chart(ctx3, {
    type: "pie",
    data: dataTunjangan,
    options: {
      responsive: true,
      maintainAspectRatio: true
    }
  });
});
