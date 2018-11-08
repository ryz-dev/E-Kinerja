<!DOCTYPE html>
<html lang="en">
<head>
    @include('layouts.admin.partial.part.header')
    <style media="screen">
      .loading {
        margin: auto;
        z-index: 9999;
        width: 100%;
        height: 100%;
        background-color:#212d3ad6;
        position: absolute;
      }
      .loading img {
        width: 50px;
        height: 50px;
        top: 0px;
        bottom: 0px;
        left: 0px;
        right: 0px;
        position: absolute;
        margin: auto;
      }
    </style>
</head>
<body>
<section class="monitoring-absen">
    <div class="burgerBtn">
        <div class="bar1"></div>
        <div class="bar2"></div>
        <div class="bar3"></div>
    </div>
    <div class="overlay">
        <div class="close-side"><i class="fas fa-times"></i></div>
    </div>
    @include('layouts.admin.partial.part.sidebar')
    @yield('content')
</section>
</body>
<div class="loading">
  <img src="{{ asset('assets/images/loading.gif') }}" alt="loading">
</div>
@include('layouts.admin.partial.part.footer')
</html>
