
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel -->
      <div class="user-panel">
        <div class="pull-left image">
          <img src="{{ asset('backend/img/user_default.png') }}" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
          <p>{{ Auth::user()->name}}</p>
          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
        </div>
      </div>

      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">OPERATIONS</li>
        <li class="@if ($title === 'Check In / Out') active @endif"><a href="{{route('attendance.index')}}"><i class="fa fa-dashboard"></i> <span>Check IN/OUT</span></a>
        </li> 
        <li class="@if ($title === 'Attendance') active @endif"><a href="{{route('attendance.list')}}"><i class="fa fa-dashboard"></i> <span>View Attendance</span></a>
        </li>
        <li class="@if ($title === 'QR Scanner') active @endif"><a href="{{route('scanner')}}"><i class="fa fa-dashboard"></i> <span>Scanner</span></a>
        </li>

      {{-- Allow these menus for admin only --}}
      
      @can('isAdmin')

        <li class="@if ($title === 'Employees') active @endif"><a href="{{route('user_employee.index')}}"><i class="fa fa-dashboard"></i> <span>Staffs</span></a>
        </li>
        <li class="@if ($title === 'Contractors') active @endif"><a href="{{route('user_contractor.index')}}"><i class="fa fa-dashboard"></i> <span>Contractors</span></a>
        </li>
        <li class="@if ($title === 'Clients') active @endif"><a href="{{route('user_client.index')}}"><i class="fa fa-dashboard"></i> <span>Clients</span></a>
        </li>
        <li class="@if ($title === 'Wages') active @endif"><a href="{{route('wages.index')}}"><i class="fa fa-dashboard"></i> <span>Wages</span></a>
        </li>
        <li class="@if ($title === 'Roster') active @endif"><a href="{{route('roster.index')}}"><i class="fa fa-dashboard"></i> <span>Roster</span></a>
        </li>
        <li class="@if ($title === 'Roster Variation') active @endif"><a href="{{route('roster_variation.index')}}"><i class="fa fa-dashboard"></i> <span>Roster Variation</span></a>
        </li>
        <li class="@if ($title === 'Sites') active @endif"><a href="{{route('site.index')}}"><i class="fa fa-dashboard"></i> <span>Sites</span></a>
        </li>
        <li class="@if ($title === 'Site Attendance') active @endif"><a href="{{route('site.attendance')}}"><i class="fa fa-dashboard"></i> <span>Site Attendance</span></a>
        </li>
      @endcan
        <li class="@if ($title === 'Mail') active @endif"><a href="{{route('mail.index')}}"><i class="fa fa-dashboard"></i> <span>Mail</span></a>
        </li>


      </ul>
    </section>
    <!-- /.sidebar -->
