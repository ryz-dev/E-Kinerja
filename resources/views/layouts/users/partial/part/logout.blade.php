<div class="nav-top">
  <div class="img-profile" id="user-profile" style="background-image: url({{ asset('assets/images/img-user.png') }});"></div>
  <div class="profile">
    <div class="row">
      <div class="col-md-12">
        <div class="profile-img">
          <div class="img-profile" style="background-image: url({{ asset('assets/images/img-user.png') }});">
          </div>
        </div>
        <br>
        <div class="profile-name">
        <label>{{ \Auth::user()->nama }}</label>
        </div>
      </div>
    </div>
    <a 
      href="{{ route('logout') }}" 
      class="btn btn-block" 
      id="btn-logout"
      onclick="event.preventDefault();
              document.getElementById('logout-form').submit();">Logout</a>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
      @csrf
    </form>
  </div>
</div>
