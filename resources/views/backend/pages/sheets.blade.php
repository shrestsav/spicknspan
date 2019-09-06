@extends('backend.layouts.app',['title'=> 'Roster Sheets'])

@push('styles')

  {{-- Excel Sheet --}}
  <link rel="stylesheet" href="{{ asset('backend') }}/vendor/jexcel/jexcel.css" type="text/css" />
  <link rel="stylesheet" href="{{ asset('backend') }}/vendor/jexcel/jsuites.css" type="text/css" />
@endpush

@section('content')

@php
  $total_days =  count($all_days);
  $week = [1,2,3,4,5,6];
  $search_arr = [
    'Employee Name' => [
      'data'    => 'employees',
      'name'    => 'search_by_employee_id'
    ],
    'Client Name' => [
      'data'    => 'clients',
      'name'    => 'search_by_client_id'
    ],
  ];

  $today = strtotime(Date('Y-m-d'));
  $todayWeek = weekOfMonth($today);

  function weekOfMonth($date) {
    //Get the first day of the month.
    $firstOfMonth = strtotime(date("Y-m-01", $date));
    //Apply above formula.
    return intval(date("W", $date)) - intval(date("W", $firstOfMonth)) + 1;
  } 

@endphp

<section class="content">
  <div class="row">
    <div class="col-md-12" id="roster">
      {{-- {{$clients}} --}}
      <index clients_json="{{json_encode($clients)}}" employees_json="{{json_encode($employees)}}"></index>
    </div>        
  </div>
</section>
@endsection

@push('scripts')
{{-- Excel Sheets --}}
<script src="{{ asset('js/roster.js') }}"></script>
<script src="{{ asset('backend') }}/vendor/jexcel/jexcel.js"></script>
<script src="{{ asset('backend') }}/vendor/jexcel/jsuites.js"></script>



@endpush