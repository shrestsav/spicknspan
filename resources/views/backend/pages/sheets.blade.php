@extends('backend.layouts.app',['title'=> 'Roster Sheets'])

@push('styles')
  <!-- Jquery Time Picker -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.10.0/jquery.timepicker.min.css" rel="stylesheet">
  <style type="text/css">
    .timepicker{
      width: 100%;
      text-align: center;
    }
    .week_selector{
      margin:0px 8px;
      padding: 10px 20px;
    }
    td:hover{
      border:1px solid #9a9a9a !important;
    }
    th{
      text-align: center;
    }
    .ibtnDel{
      padding: 0px 2px;
      background: red;
      color: white;
    }
    .ibtnDel:hover{
      cursor: pointer;
    }
    td.week_1,td.week_2,td.week_3,td.week_4,td.week_5{
      padding: 10px 20px !important;
    }
    .week_1 input,.week_2 input,.week_3 input,.week_4 input,.week_5 input{
      border: 0px !important;
      margin: 4px;
    }
    .editable_roster .week_1 input:hover,.editable_roster .week_2 input:hover,.editable_roster .week_3 input:hover,.editable_roster .week_4 input:hover,.editable_roster .week_5 input:hover{
      border: 0.5px dashed  #00a65a !important;
      cursor: pointer;
    }
    input:hover{
      cursor: pointer;
    }
    .search_by_date{
      display: inline-table;
      width: 150px; 
      top: 14px;
    }
    .pagination{
      margin: 0px;
    }
    .form-control[readonly],.readonly_roster{
      background-color: #f7f7f7ad;
      opacity: 1;
    }
  </style>

  {{-- Excel Sheet --}}
  <link rel="stylesheet" href="https://bossanova.uk/jexcel/v3/jexcel.css" type="text/css" />
  <link rel="stylesheet" href="https://bossanova.uk/jsuites/v2/jsuites.css" type="text/css" />
@endpush

@section('content')

@php
  $total_days =  count($all_days);
  $week = [1,2,3,4,5];
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
    <div class="col-md-12">
      <div class="box box-primary">
        <div class="box-header with-border">
        </div>
        <div class="box-body no-padding">
          {{-- Excel Sheet --}}
          <div id="spreadsheet"></div>
        </div>
      </div>
    </div>        
  </div>
</section>

@endsection

@push('scripts')
{{-- Excel Sheets --}}
<script src="https://bossanova.uk/jexcel/v3/jexcel.js"></script>
<script src="https://bossanova.uk/jsuites/v2/jsuites.js"></script>
<script>
  //Not used kaam lagna sakxa
  function getMonths(month,year){
    var ar = [];
    var days = [];
    var start = moment(year+"-"+month,"YYYY-MMM");
    for(var end = moment(start).add(1,'month');  start.isBefore(end); start.add(1,'day')){
        ar.push(start.format('D-ddd'));
        days[start.format('D')] = start.format('ddd,MMM-D');
    }
    return days;
  }
  var days = getMonths('Mar',2011);
  console.log(days);

var data2 = [
    [1, 'Cheese'],
    [1, 'Apples'],
    [2, 'Carrots'],
    [1, 'Oranges'],
];
var header = ['Employee', 'Client'];
for(var i=1; i<days.length; i++){
  header.push(days[i]);
}
console.log(header);
// dropdownFilter = function(instance, cell, c, r, source) {
//     var value = instance.jexcel.getValueFromCoords(c - 1, r);
//     if (value == 1) {
//         return ['Apples','Bananas','Oranges'];
//     } else if (value == 2) {
//         return ['Carrots'];
//     } else {
//         return source;
//     }
// }
var changed = function(instance, cell, x, y, value) {
    var cellName = jexcel.getColumnNameFromId([x,y]);
    alert('New change on cell ' + cellName + ' to: ' + value + '');
}
table = jexcel(document.getElementById('spreadsheet'), {
    data:data2,
    onchange: changed,
    colHeaders: header,
    colWidths: [ 300, 300, 100, 100 ],
    columns: [
      { 
        type:'dropdown', 
        source:[
          {'id':'1', 'name':'Fruits'}, 
          {'id':'2', 'name':'Legumes'}, 
          {'id':'3', 'name':'General Food'} 
        ]
      },
      { 
        type:'dropdown', 
        source:['Apples','Bananas','Carrots','Oranges','Cheese'], 
      },
      { 
        type: 'text' 
      },
      { 
        type: 'text' 
      },
      { 
        type: 'text' 
      },
      { 
        type: 'text' 
      },
    ],

});
</script>


@endpush