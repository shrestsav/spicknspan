<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>{{env('APP_NAME', 'System')}}</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="{{ asset('backend/css/bootstrap.min.css') }}">
  {{-- Select 2 --}}
  <link rel="stylesheet" href="{{ asset('backend/css/select2.min.css') }}">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('backend/css/font-awesome.min.css') }}">
  <!-- DataTables -->
  <link rel="stylesheet" href="{{ asset('backend/css/dataTables.bootstrap.min.css') }}">
  <!-- Bootstrap DatePicker -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('backend/css/AdminLTE.min.css') }}">
  {{-- Theme Color CSS --}}
  <link rel="stylesheet" href="{{ asset('backend/css/skin-green.min.css') }}">
  <link rel="stylesheet" href="{{ asset('backend/css/jquery-filestyle.min.css') }}">
  <link rel="stylesheet" href="{{ asset('backend/css/daterangepicker.css') }}">
  <link rel="stylesheet" href="{{ asset('backend/css/animate.css') }}">
  <link rel="stylesheet" href="{{ asset('backend/iCheck/all.css') }}">

  {{-- CUSTOM CSS BY SHRESTSAV --}}
  <link rel="stylesheet" href="{{ asset('backend/css/style.css') }}">
  <style type="text/css">
    .skin-green .main-header .logo {
        background-color: #292929;
    }
    .skin-green .main-header .navbar {
        background-color: #292929 !important;
    }
    .skin-green .wrapper, .skin-green .main-sidebar, .skin-green .left-side {
        background-color: #292929;
    }
    .skin-green .sidebar a:hover {
        background-color: #0060A2 !important;
    }
    .skin-green .sidebar-menu>li.active>a {
        border-left-color: #0060A2;
    }
    .skin-green .main-header li.user-header {
        background-color: #19506f;
    }
    .skin-green .sidebar-menu>li>.treeview-menu {
        background: #313131;
    }
    .logo-mini_green{

    }
    .logo-mini_blue{

    }
    [data-notify="progressbar"] {
      margin-bottom: 0px;
      position: absolute;
      bottom: 0px;
      left: 0px;
      width: 100%;
      height: 5px;
    }
  </style>
  <style type="text/css" media="print">
    .printer{
      display:none;
    }
  </style>
  

  @stack('styles')

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-green sidebar-mini {{Session::get('theme_sidebar')}}">
  <div class="wrapper printer">

    <header class="main-header">
      @include('backend.layouts.includes.header')
    </header>

    <!-- Left side column. contains the logo and sidebar -->
    <aside class="main-sidebar">
      @include('backend.layouts.includes.sidebar');
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">

      <!-- Content Header (Page header) -->
      <section class="content-header">
        <h1>
          {{$title}}
          <small>Control panel</small>
        </h1>
        <ol class="breadcrumb">
          <li><a href="{{route('dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
          <li class="active">{{$title}}</li>
        </ol>
      </section>
      @yield('content')
    </div>
    <!-- /.content-wrapper -->

    <footer class="main-footer">
      <div class="pull-right hidden-xs">
        <b>Version</b> 1.0
      </div>
      <strong>Copyright &copy; 2019</strong> All rights
      reserved.
    </footer>

    <!-- Add the sidebar's background. This div must be placed
         immediately after the control sidebar -->
    <div class="control-sidebar-bg"></div>
  </div>
  <!-- ./wrapper -->

    @stack('modals')
    
<!-- jQuery 3 -->
<script src="{{ asset('backend/js/jquery.min.js') }}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="{{ asset('backend/js/jquery-ui.min.js') }}"></script>
<script src="{{ asset('backend/js/select2.full.min.js') }}"></script>
<script src="{{ asset('backend/js/sweetalert.min.js') }}"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  var SITE_URL =  '{{ url("/") . "/" }}';

  $.widget.bridge('uibutton', $.ui.button);

  // SET AJAX CSRF TOKEN
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  // Display Current Time
  var datetime = null,
          date = null;

  var update = function () {
      date = moment(new Date()).tz('{{Auth::user()->timezone}}');
      datetime.html(date.format('h:mm:ss A'));
  };

  $(document).ready(function(){
      datetime = $('.display-now-time')
      update();
      setInterval(update, 1000);
  });
  // Display Current Time Ends

  $(function () {
      $('.select2').select2();
  });


  $('.sidebar-toggle').on('click',function(){
    if($('body').hasClass("sidebar-collapse")){
      sidebar_state='';
    }
    else{
      sidebar_state='sidebar-collapse';
    }
    
    $.ajax({
      url: "{{ url('/set_sidebar') }}",
      method: 'post',
      data: {
         id: '{{ Auth::user()->id }}',
         theme_sidebar: sidebar_state
      },
      success: function(response){
      }
    });
  });

</script>

{{-- Context Menu JS --}}
<script type="text/javascript">
  @if(env('CONTEXT_MENU',false))
    window.addEventListener("contextmenu", e => {
      e.preventDefault();
    });
  @endif

  $('.contextmenurow').on('contextmenu',function(e){
      let id=$(this).attr('dataid');
      var position = $(this).position();
      var cardHeight = $(".card").height();
      var dialogHeight = $("#contextmenu_"+id).outerHeight();
      var total = position.top + dialogHeight + 100;
      $('.dropdown-contextmenu').hide();
      $('#contextmenu_'+id).show();
      $('#contextmenu_'+id).offset({left:e.pageX,top:e.pageY});
      if(total>=cardHeight){
        $('#contextmenu_'+id).offset({top:e.pageY-dialogHeight});
      }
  });

  $(document).on('click',function(){
      $('.dropdown-contextmenu').hide();
  });
</script>
<!-- Bootstrap 3.3.7 -->
<script src="{{ asset('backend/js/bootstrap.min.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('backend/js/adminlte.min.js') }}"></script>
<script src="{{ asset('backend/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('backend/js/dataTables.bootstrap.min.js') }}"></script>
<script src="{{ asset('backend/js/sweetalert.min.js') }}"></script>
<script src="{{ asset('backend/js/validator.min.js') }}"></script>
<script src="{{ asset('backend/js/jquery-filestyle.min.js') }}"></script>
<script src="{{ asset('backend/js/bootstrap-notify.min.js') }}"></script>
<script src="{{ asset('backend/iCheck/icheck.min.js') }}"></script>

<script src="{{ asset('backend/js/custom.js') }}"></script>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.23/moment-timezone.min.js"></script>
<script src="{{ asset('backend/js/moment-timezone-with-data-2012-2022.min.js') }}"></script>
<script src="{{ asset('backend/js/daterangepicker.js') }}"></script>
<script type="text/javascript">
  var timeZones = moment.tz.names();

  @if($errors->any())
    @foreach ($errors->all() as $error)
      showNotify('danger','{{$error}}')
    @endforeach
  @endif

  @if(\Session::has('error'))
    showNotify('danger','{{\Session::get("error")}}')
  @endif

  @if(\Session::has('message'))
    showNotify('success','{{\Session::get("message")}}')
  @endif

//Initialize Date pickers
  $('.datepicker_with_time').daterangepicker({
    singleDatePicker: true,
    startDate: moment().startOf('hour'),
    timePicker:true,
    timePicker24Hour:true,
    locale: {
      format: 'YYYY/M/DD hh:mm A'
    }
  });
</script>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/js/bootstrap-datepicker.min.js"></script>

@stack('scripts')

</body>
</html>
