<!DOCTYPE html>
<html lang="en">
<head>
  @include('layouts.admin.partial.part.header')
</head>
<body>
<div id="preload">
  <img src="{{ asset('assets/images/loading_ekinerja.svg')}}" width="150">
</div>
<section class="component">
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
@include('layouts.admin.partial.part.footer')
</html>
