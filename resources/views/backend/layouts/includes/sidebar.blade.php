
    <section class="sidebar">
      <div class="user-panel">
        <div class="pull-left image">
          @if(file_exists(public_path('files/users/'.Auth::user()->id.'/dp_user_'.Auth::user()->id.'.png')))
            <img src="{{ asset('files/users/'.Auth::user()->id.'/dp_user_'.Auth::user()->id.'.png') }}" class="img-circle" alt="User Image">
          @elseif(Auth::user()->avatar)
              <img src="{{ Auth::user()->avatar }}" class="img-circle" alt="User Image">
          @else
            <img src="{{ asset('backend/img/user_default.png') }}" class="img-circle" alt="User Image">
          @endif
        </div>
        <div class="pull-left info">
          <p>{{ ucfirst(Auth::user()->name)}}</p>
          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
        </div>
      </div>

      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">OPERATIONS</li>
        <li class="@if ($title === 'Check In / Out') active @endif"><a href="{{route('attendance.index')}}"><i class="fa fa-sign-in"></i> <span>Check IN/OUT</span></a>
        </li> 
        <li class="@if ($title === 'Attendance') active @endif"><a href="{{route('attendance.list')}}"><i class="fa fa-book"></i> <span>View Attendance</span></a>
        </li>
        <li class="@if ($title === 'QR Scanner') active @endif"><a href="{{route('scanner')}}"><i class="fa fa-qrcode"></i> <span>QR Login</span></a>
        </li>
        <li class="@if ($title === 'Site Attendance') active @endif"><a href="{{route('site.attendance')}}"><i class="fa fa-dashboard"></i> <span>Site Attendance</span></a>
        </li>
        <li class="@if ($title === 'Incident Report') active @endif"><a href="{{route('incident.create')}}"><i class="fa fa-fire"></i> <span>Incident Report</span></a>
        </li>
        <li class="@if ($title === 'Leave Application') active @endif"><a href="{{route('leaveRequest.index')}}"><i class="fa fa-suitcase"></i> <span>Leave Application</span></a>
        </li>

      {{-- Allow these menus for admin only --}}

      @role(['superAdmin','contractor'])
        <li class="header">SETTINGS</li>
        <li class="treeview @if ($title === 'Employees' || $title === 'Contractors' || $title === 'Clients' || $title === 'Company') active @endif">
          <a href="#">
            <i class="fa fa-user-plus"></i> <span>Manage</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <!-- <li class="@if ($title === 'Company') active @endif"><a href="{{route('user_company.index')}}"><i class="fa fa-building-o"></i> <span>Company</span></a>
            </li> -->
            <li class="@if ($title === 'Employees') active @endif"><a href="{{route('user_employee.index')}}"><i class="fa fa-users"></i> <span>Employees</span></a>
            </li>
          @role('superAdmin')
            <li class="@if ($title === 'Contractors') active @endif"><a href="{{route('user_contractor.index')}}"><i class="fa fa-pencil-square-o"></i> <span>Contractors</span></a>
            </li>
          @endrole
            <li class="@if ($title === 'Clients') active @endif"><a href="{{route('user_client.index')}}"><i class="fa fa-user"></i> <span>Clients</span></a>
            </li>
            <li class="@if ($title === 'Sites') active @endif"><a href="{{route('site.index')}}"><i class="fa fa-dashboard"></i> <span>Sites</span></a>
            </li>
          </ul>
        </li>

        <li class="treeview">
          <a href="#">
            <i class="fa fa-folder-open"></i> <span>Utilities</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="@if ($title === 'Wages') active @endif"><a href="{{route('wages.index')}}"><i class="fa fa-circle-o"></i> <span>Wages</span></a>
            </li>
            <li class="@if ($title === 'Roster') active @endif"><a href="{{route('roster.index')}}"><i class="fa fa-circle-o"></i> <span>Roster</span></a>
            </li>
            <li class="@if ($title === 'Roster Variation') active @endif"><a href="{{route('roster_variation.index')}}"><i class="fa fa-circle-o"></i> <span>Roster Variation</span></a>
            </li>
            <li class="@if ($title === 'Question Template') active @endif"><a href="{{route('question.index')}}"><i class="fa fa-circle-o"></i> <span>Question Template</span></a>
            </li>
          </ul>
        </li>
      @endrole
        
       {{--  <li class="@if ($title === 'Mail') active @endif"><a href="{{route('mail.index')}}"><i class="fa fa-dashboard"></i> <span>Mail</span></a>
        </li> --}}
        
      </ul>
    </section>
