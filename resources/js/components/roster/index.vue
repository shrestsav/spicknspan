<template>
  <div class="box box-primary">
    <div class="box-header with-border">
      <div class="box-header">
        <div class="search_form">
          <date-picker v-model="year_month" @change="split()" lang="en" type="month" valueType="format" format="YYYY-MM" class="txtTime"></date-picker>
          <button type="button" class="btn btn-primary" @click="load()">LOAD</button>
        </div>

        <div class="pull-right">
          <select class="select2 week_selector" @change="changeWeek()" v-model="week">
            <option disabled selected value>Select Week</option>
            <option v-for="index in 6" :value="index">Week {{ index }}</option>
          </select>
        </div>
      </div> 
    </div>
    <div class="box-body no-padding">
      <div id="spreadsheet" ref="spreadsheet"></div>
    </div>
  </div>
</template>

<script>
  
  // import ent from 'vue-moment'
  import DatePicker from 'vue2-datepicker'
  import jexcel from 'jexcel'
  import jexcelStyle from 'jexcel/dist/jexcel.css'

  export default {
    name: "jexcel",
    components: {DatePicker},
    props: ['clients_json','employees_json'],
    data() {
      return {
        year_month:'',
        month:6,
        year:2019,
        week:2,
        days:{},
        clients: {},
        employees: {},
        columns:[],
        data: [
          [3, 1],
          [2, 8],
          [2, 9],
          [3, 4],
        ]
      };
    },
    computed: {
      split(){
        var str = this.year_month;
        var splitted = str.split("-");
        this.year = splitted[0];
        this.month = splitted[1];
      },
      jExcelOptions() {
        return {
          data: this.data,
          columns: this.columns
        };
      }
    },
    methods: {
      insertRowc() {
        console.log(this);
      },
      getMonths(month,year){
        var ar = [];
        var days = [];
        var start = moment(year+"-"+month,"YYYY-MM");
        for(var end = moment(start).add(1,'month');  start.isBefore(end); start.add(1,'day')){
            ar.push(start.format('D-ddd'));
            days[start.format('D')] = start.format('ddd,MMM-D');
        }
        return days;
      },
      populateTitle(){
        this.columns = [          
        { 
          title: 'Employee',
          type: 'dropdown',
          autocomplete:true, 
          // readOnly:true,
          width:140,
          source:this.clients
        },
        { 
          title:'Client',
          type:'dropdown',
          autocomplete:true,
          // readOnly:true, 
          width:140,
          source:this.employees
        }];

        for(var i=1; i<this.days.length; i++){
          const myObj = {};
          myObj.title = this.days[i];
          myObj.type = 'text';
          myObj.width = 140;
          this.columns.push(myObj);
        }
      },
      populateRefs(){
        for(var i=2; i<=32; i++){
          var elm = $('td[data-x="'+i+'"]');
          elm.addClass('week_'+this.week_of_month(this.year, this.month, i))
        }
      },
      load(){
        // jexcel.destroy(document.getElementById('spreadsheet'), true);
        this.days = this.getMonths(this.month,this.year);
        this.populateTitle();
        const jExcelObj = jexcel(this.$refs["spreadsheet"], this.jExcelOptions);
        Object.assign(this, { jExcelObj });
        this.populateRefs();
        this.changeWeek();
      },
      ISO8601_week_no(dt){
        var tdt = new Date(dt.valueOf());
        var dayn = (dt.getDay() + 6) % 7;
        tdt.setDate(tdt.getDate() - dayn + 3);
        var firstThursday = tdt.valueOf();
        tdt.setMonth(0, 1);
        if (tdt.getDay() !== 4) {
          tdt.setMonth(0, 1 + ((4 - tdt.getDay()) + 7) % 7);
        }
       return 1 + Math.ceil((firstThursday - tdt) / 604800000);
      },
      week_of_month(year, month, day){
        month = month-1;
        var d = new Date(year, month, day); 
        var f = new Date(year, month, 1);
        return this.ISO8601_week_no(d)-this.ISO8601_week_no(f)+1;
      },
      changeWeek(){
        var selWeek = document.getElementsByClassName('week_'+this.week);
        for (var i=0;i<selWeek.length;i+=1){
          selWeek[i].style.display = 'table-cell';
        }
        for(var b = 0; b<=6; b++){
          if(this.week!=b){
            var other = document.getElementsByClassName('week_'+b);
            for (var i=0;i<other.length;i+=1){
              other[i].style.display = 'none';
            }
          }
        }
      },
      getClients(){
        axios.get('/clientsList')
        .then((response) => {
          console.log(response.data);
          this.employees = JSON.parse(response.data);
          })
        .catch((error) => this.errors = error.response.data.errors)
      },
    },
    created: function() {
      this.clients = JSON.parse(this.clients_json); 
      this.employees = JSON.parse(this.employees_json); 
    },
    mounted: function() {
      this.days = this.getMonths(this.month,this.year);
        this.populateTitle();
        const jExcelObj = jexcel(this.$refs["spreadsheet"], this.jExcelOptions);
        Object.assign(this, { jExcelObj });
        this.populateRefs();
        this.changeWeek();
    }
  };

</script>