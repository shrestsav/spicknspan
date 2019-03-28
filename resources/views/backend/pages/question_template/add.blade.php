  @extends('backend.layouts.app',['title'=> 'Question Template'])

  @section('content')

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-md-12">
        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    {{ $error }}
                @endforeach
            </div>
        @endif
        @if (\Session::has('message'))
          <div class="alert alert-success custom_success_msg">
              {{ \Session::get('message') }}
          </div>
        @endif
        <div class="container col-sm-12">
          <div class="box box-primary">
            <div class="box-header with-border">

            <form name="add_name" id="add_name">  


            <div class="alert alert-danger print-error-msg" style="display:none">
            <ul></ul>
            </div>

            <div class="alert alert-success print-success-msg" style="display:none">
            <ul></ul>
            </div>

            <!-- <div class="form-group">
                <label class="col-md-2 control-label" for="title">Category</label>
                <select name="category_id" id="category_id" class="form-control col-md-4" style="width: 100%;" tabindex="-1" aria-hidden="true">
                    <option value="0">-- Select Category --</option>
                    <option value="1">Bathroom Cleaning</option>
                    <option value="2">Room Cleaning</option>
                    <option value="3">Laundry Service</option>
                </select>
            </div> -->
            <br>
            <div class="form-group">
                <label class="col-md-2 control-label" for="title">Title</label>
                <div class="col-md-4">
                    <input type="text" class="form-control" id="title" name="question_template_title" placeholder="Enter Title Here">
                </div>
            </div>

            <br><br>

            <div class="question_title">
              <h2>Add Questions Here</h2>
            </div>

            <br>

            <div class="table-responsive">  
                <table class="table table-bordered" id="dynamic_field">  
                    <tr>  
                        <td><input type="text" name="name[]" placeholder="Enter your Question Here" class="form-control name_list" /></td>  
                        <td><button type="button" name="add" id="add" class="btn btn-success">Add More</button></td>  
                    </tr>  
                </table>  
                <input type="button" name="submit" id="submit" class="btn btn-info" value="Submit" />  
            </div>


         </form>

            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  @endsection

  @push('scripts')
<script type="text/javascript">
    $(document).ready(function(){      
      var postURL = "<?php echo url('/questionTemplate/add'); ?>";
      var i=1;

      $('#add').click(function(){  
           i++;  
           $('#dynamic_field').append('<tr id="row'+i+'" class="dynamic-added"><td><input type="text" name="name[]" placeholder="Enter your Question Here" class="form-control name_list" /></td><td><button type="button" name="remove" id="'+i+'" class="btn btn-danger btn_remove">X</button></td></tr>');  
      });

      $(document).on('click', '.btn_remove', function(){  
           var button_id = $(this).attr("id");   
           $('#row'+button_id+'').remove();  
      });

      $('#submit').click(function(){            
           $.ajax({  
                url:postURL,  
                method:"POST",  
                data:$('#add_name').serialize(),
                type:'json',
                success:function(data)  
                {
                    if(data.error){
                        printErrorMsg(data.error);
                    }else{
                        i=1;
                        $('.dynamic-added').remove();
                        $('#add_name')[0].reset();
                        $(".print-success-msg").find("ul").html('');
                        $(".print-success-msg").css('display','block');
                        $(".print-error-msg").css('display','none');
                        $(".print-success-msg").find("ul").append('<li>Record Inserted Successfully.</li>');
                    }
                }  
           });  
      });  


      function printErrorMsg (msg) {
         $(".print-error-msg").find("ul").html('');
         $(".print-error-msg").css('display','block');
         $(".print-success-msg").css('display','none');
         $.each( msg, function( key, value ) {
            $(".print-error-msg").find("ul").append('<li>'+value+'</li>');
         });
      }
    });  
</script>   
  @endpush