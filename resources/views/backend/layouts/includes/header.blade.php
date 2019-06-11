    <!-- Logo -->
    <a href="{{url('/')}}" class="logo">
      <div class="logo-lg">
        <img src="{{ asset('backend/img/company-logo.png') }}" width="200px;">
      </div>
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini">
        <img src="{{ asset('backend/img/company-logo-small.png') }}" height="40px">
        {{-- <span class="logo-mini_green">T</span><b>&</b><span class="logo-mini_blue">C</span> --}}
      </span>
      <!-- logo for regular state and mobile devices -->
      {{-- <span class="logo-lg">SPICK <b>N</b> SPAN</span> --}}
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <li class="date-time">
            <a href="#" class="display-time">
              {{-- <i class="fa fa-clock-o"></i> --}}
              <span class="label label-info display-now-time">{{-- Display Current Time Here --}}</span>
            </a>

          </li>
          <!-- User Account: style can be found in dropdown.less -->
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              @if(file_exists(public_path('files/users/'.Auth::user()->id.'/dp_user_'.Auth::user()->id.'.png')))
                <img src="{{ asset('files/users/'.Auth::user()->id.'/dp_user_'.Auth::user()->id.'.png') }}" class="user-image" alt="User Image">
              @elseif(Auth::user()->avatar)
                <img src="{{ Auth::user()->avatar }}" class="user-image" alt="User Image">
              @else
                <img src="{{ asset('backend/img/user_default.png') }}" class="user-image" alt="User Image">
              @endif
              <span class="hidden-xs">{{ ucfirst(Auth::user()->name) }}</span>
            </a>
            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">
                @if(file_exists(public_path('files/users/'.Auth::user()->id.'/dp_user_'.Auth::user()->id.'.png')))
                  <img src="{{ asset('files/users/'.Auth::user()->id.'/dp_user_'.Auth::user()->id.'.png') }}" class="img-circle" alt="User Image">
                @elseif(Auth::user()->avatar)
                  <img src="{{ Auth::user()->avatar }}" class="img-circle" alt="User Image">
                @else
                  <img src="{{ asset('backend/img/user_default.png') }}" class="img-circle" alt="User Image">
                @endif
                <p>
                  {{ ucfirst(Auth::user()->user_type) }}
                  <small></small>
                </p>
              </li>
              <!-- Menu Body -->
              {{-- <li class="user-body">
                <div class="row">
                  <div class="col-xs-4 text-center">
                    <a href="#">Profile</a>
                  </div>
                  <div class="col-xs-4 text-center">
                    <a href="#">Settings</a>
                  </div>
                  <div class="col-xs-4 text-center">
                    <a href="#">Something</a>
                  </div>
                </div>
              </li> --}}
              <!-- Menu Footer-->
              <li class="user-footer" style="text-align: center;">
                <div class="pull-left">
                  <a href="{{route('password.edit')}}" class="btn btn-default btn-flat">Change Password</a>
                </div>
                <div class="pull-right">
                  <a class="btn btn-default btn-flat" href="{{ route('logout') }}"
                     onclick="event.preventDefault();
                                   document.getElementById('logout-form').submit();">
                      {{ __('Sign Out') }}
                  </a>

                  <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                      @csrf
                  </form>
                  {{-- <a href="#" class="btn btn-default btn-flat">Sign out</a> --}}
                </div>
              </li>
            </ul>
          </li>
          <!-- Control Sidebar Toggle Button -->
          <!-- <li>
            <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
          </li> -->
        </ul>
      </div>
    </nav>