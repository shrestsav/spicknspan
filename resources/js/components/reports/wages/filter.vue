<template>
  <div class="row"> 
    <div class="col-md-12 text-center">
      <div class="col-md-3">
        <v-select v-model="filter.employee_id" :options="lists.employees" :reduce="name => name.id" label="name" placeholder="Select Employee" />
      </div>
      <div class="col-md-3">
        <v-select v-model="filter.client_id" :options="lists.clients" :reduce="name => name.id" label="name" placeholder="Select Client" />
      </div>
      <div class="col-md-3">
        <date-picker v-model='filter.range' lang='en' valueType="format" range></date-picker>
      </div>
      <div class="col-md-1">
        <button type="submit" class="btn btn-primary" @click='render'>Search</button>
      </div>
    </div>
    <br><br><br>
    <div class="col-md-12" v-bind:class="{ hide: isActive }">
      <table class="table table-bordered table-hover table-striped">
        <thead>
          <tr>
            <th>S.No</th>
            <th>Date</th>
            <th>Employee Name</th>
            <th>Client Name</th>
            <th>Attended Hours</th>
            <th>Wage</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item,key in results">
            <td>{{++key}}</td>
            <td>{{ item.date }}</td>
            <td>{{ item.employee_name }}</td>
            <td>{{ item.client_name }}</td>
            <td>{{ item.attended_hours }}</td>
            <td>{{ item.totalwage }}</td>
          </tr>
        </tbody>
<!--         <tfoot>
          <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>afdfdsf</td>
            <td>{{ key }}</td>
          </tr>
        </tfoot> -->
      </table>
    </div>
  </div>


      

</template>

<script>
  import DatePicker from 'vue2-datepicker'
  import vSelect from 'vue-select'
  import 'vue-select/dist/vue-select.css';

  export default{
    components: { 
      DatePicker,vSelect
    },
    data(){
      return{
        filter:{
          employee_id : '',
          client_id   : '',
          range       : '',
        },
        results:{},
        lists:{},
        errors:{},
        isActive:true,
        test:'fsdfasdfadf',

      }
    },
    mounted(){
      axios.post('/wagesFilterItems')
      .then((response) => 
        this.lists = response.data,
      )
      .catch((error) => 
        this.errors = error.response.data.errors
      )

    },
    methods:{
      render(){
        axios.post('/wagesReport',this.$data.filter)
        .then((response) => {
          // console.log(response.data);
          this.results = response.data;
          this.isActive = false;
          })
        .catch((error) => this.errors = error.response.data.errors)
      }
    }
  }

  </script>