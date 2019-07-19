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
    <div class="col-md-12">
      <div class="box box-primary">
        <div class="box-header with-border">
          <div class="box-header">
            {{-- <h3 class="box-title"></h3> --}}

            {{-- Filter Form --}}
            <div class="search_form">
              <form autocomplete="off" role="form" action="{{route('roster.sheets')}}" method="POST" enctype="multipart/form-data">
                @csrf
                @foreach($search_arr as $part => $arr)
                  <select class="select2 {{$arr['name']}}" name="{{$arr['name']}}">
                    <option disabled selected value> {{$part}}</option>
                    @foreach(${$arr['data']} as $data)
                      <option value="{{$data->id}}" @if(Request::input($arr['name'])==$data->id) selected @endif>
                        {{$data->name }}
                      </option>
                    @endforeach
                  </select>
                @endforeach
                <div class="input-group date search_by_date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input name="year_month" type="text" id="year_month" class="form-control txtTime" style="width:85px;" value="{{$year.'-'.$month}}" autocomplete="off" required>
                </div> 
                <button type="submit" class="btn btn-primary">SEARCH</button>
              </form>
            </div>
            @role('superAdmin','contractor')
            <button type="button" class="btn btn-danger edit_rosters pull-right">EDIT</button>
            @endrole
            <div class="pull-right">
              <select class="select2 week_selector" onchange="changeWeek(this)">
                <option disabled selected value>Select Week</option>
                @foreach($week as $a)
                  <option value="{{$a}}">Week {{$a}}</option>
                @endforeach
              </select>
            </div>
          </div> 
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
<script type="text/javascript">
  $('#year_month').datepicker({
        autoclose: true,
        minViewMode: 1,
        format: 'yyyy-mm'
    });
</script>
<script>
  const year = '{{$year}}';
  const month = '{{$month}}';

    var arr = [1,2,3,4,5,6];
    var full_date = moment().format('YYYY-MM-D');
    var year_month = moment().format('YYYY-MM');
    var sel_year_month = '{{$year."-".$month}}';
    var today = moment().format('D');
    var cur_month = moment().format('MM');
    var total_days = Number('{{$total_days}}');
    var leave_types = JSON.parse('{!! json_encode(config("setting.leave_types")) !!}');
    var curr_week = week_of_month(year, cur_month, today);


  //Not used kaam lagna sakxa
  function getMonths(month,year){
    var ar = [];
    var days = [];
    var start = moment(year+"-"+month,"YYYY-MM");
    for(var end = moment(start).add(1,'month');  start.isBefore(end); start.add(1,'day')){
        ar.push(start.format('D-ddd'));
        days[start.format('D')] = start.format('ddd,MMM-D');
    }
    return days;
  }
  var days = getMonths(month,year);
  console.log(days);

  const myArr = [
  { 
    title:'Employee',
    type:'dropdown',
    autocomplete:true, 
    readOnly:true,
    source:[
      {'id':'1', 'name':'Fruits'}, 
      {'id':'2', 'name':'Legumes'}, 
      {'id':'3', 'name':'General Food'} 
    ]
  },
  { 
    title:'Client',
    type:'dropdown',
    autocomplete:true,
    // readOnly:true, 
    source:['Apples','Bananas','Carrots','Oranges','Cheese'], 
  }
  ];
  
  
 
var data2 = [
    [3, 'Cheese'],
    [1, 'Apples'],
    [2, 'Carrots'],
    [1, 'Oranges'],
];
// var header = ['Employee', 'Client'];
for(var i=1; i<days.length; i++){
  // header.push(days[i]);
  const myObj = {};
  console.log(days[i]);
  myObj.title = days[i];
  myObj.type = 'text';
  myObj.width = 120;
  myObj.data = 'fasdf';
  myArr.push(myObj);

}
// console.log(header);
console.log(myArr);
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
// var changed = function(instance, cell, x, y, value) {
//     var cellName = jexcel.getColumnNameFromId([x,y]);
//     alert('New change on cell ' + cellName + ' to: ' + value + '');
// }
// table = jexcel(document.getElementById('spreadsheet'), {
$('#spreadsheet').jexcel({
    data:data2,
    // onchange: changed,
    // colHeaders: header,
    colWidths: [ 300, 300, 100, 100 ],
    columns: myArr,
    allowInsertRow:false,
});

for(var i=2; i<=32; i++){
  var elm = $('td[data-x="'+i+'"]');
  elm.addClass('week_'+week_of_month(year, month, i))
  // elm.hide();
}
// for(var i=10; i<=16; i++){
//   $('td[data-x="'+i+'"]').show();
// }

// var date = new Date("2019-06-01");

// console.log(getWeekOfMonth(date));
// //get week number of date 
// function getWeekOfMonth(date) {
//     let adjustedDate = date.getDate()+date.getDay();
//     let prefixes = ['0', '1', '2', '3', '4', '5'];
//     return (parseInt(prefixes[0 | adjustedDate / 7])+1);
// }


function ISO8601_week_no(dt){
  var tdt = new Date(dt.valueOf());
  var dayn = (dt.getDay() + 6) % 7;
  tdt.setDate(tdt.getDate() - dayn + 3);
  var firstThursday = tdt.valueOf();
  tdt.setMonth(0, 1);
  if (tdt.getDay() !== 4) {
    tdt.setMonth(0, 1 + ((4 - tdt.getDay()) + 7) % 7);
  }
 return 1 + Math.ceil((firstThursday - tdt) / 604800000);
}

function week_of_month(year, month, day){
  month = month-1;
  d = new Date(year, month, day); 
  f = new Date(year, month, 1);
  return ISO8601_week_no(d)-ISO8601_week_no(f)+1;
}

</script>
<script type="text/javascript">
    // Select Current Week
    if(year_month==sel_year_month){
      var curr = document.getElementsByClassName('week_'+curr_week);
      for (var i=0;i<curr.length;i+=1){
        curr[i].style.display = 'table-cell';
      }
      var week_selector = document.getElementsByClassName('week_selector');
      week_selector[0].value = curr_week;
      // week_selector[0].fireEvent("onchange");
      arr.forEach(function(b){
        if(curr_week!=b){
          var other = document.getElementsByClassName('week_'+b);
          for (var i=0;i<other.length;i+=1){
            other[i].style.display = 'none';
          }
        }
      });
    }
    else{
        $('.week_1').show();
        $('.week_selector').val(1).trigger('change');
        $('.week_2').hide();
        $('.week_3').hide();
        $('.week_4').hide();
        $('.week_5').hide();
        $('.week_6').hide();
    }

  function changeWeek(me){
    var week = me.value;
    var selWeek = document.getElementsByClassName('week_'+week);
    // $('.week_'+week).show();
    console.log(selWeek);
    for (var i=0;i<selWeek.length;i+=1){
      selWeek[i].style.display = 'table-cell';
    }
    arr.forEach(function(b){
      if(week!=b){
        var other = document.getElementsByClassName('week_'+b);
        for (var i=0;i<other.length;i+=1){
          other[i].style.display = 'none';
        }
      }
    });
  }
    // Week Switch
    // $('.week_selector').on('change', function(e){
    //   var a = $(this).val();
    //   $('.week_'+a).show();
    //   arr.forEach(function(b){
    //     if(a!=b){
    //       $('.week_'+b).hide();
    //     }
    //   });
    // });
    // $('td').on('change',function(e){
    //   alert($(this).text());
    // })
</script>


@endpush