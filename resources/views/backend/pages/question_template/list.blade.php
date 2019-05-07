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
              <div class="add-btn">
                <a class="btn btn-primary" href="{{route('question.add')}}">ADD TEMPLATE</a>
              </div>
              <br>
              <table class="table table-hover">
                <tr>
                  <th>S.N.</th>
                  <!-- <th>Category</th> -->
                  <th>Title</th>
                  <th>Action</th>
                </tr>
                <?php $i = 1;?>
                @foreach($qTemplate as $qT)
                  <tr>
                    <td><?php echo $i;?></td>
                    <!-- <td></td> -->
                    <td>{{$qT->template_title}}</td>
                    <td>
                     {{--  <a href="{{url('/questionTemplate',$qT->id)}}">
                        <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                      </a> --}}
                      <a href="javascript:;" id="delete_qT" data-user_id = '{{$qT->id}}'><i class="fa fa-trash" aria-hidden="true"></i>
                      </a>
                    </td>
                    <!-- <form action="{{ url('/questionTemplate/').'/'.$qT->id}}" method="POST">
                        {{ csrf_field() }}
                        <input type="hidden" name="_method" value="POST">
                        <td><button>Delete</button></td>
                      </form> -->
                  </tr>
                  <?php $i++;?>
                @endforeach
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  @endsection

  @push('scripts')
  <script type="text/javascript">
    $(function () {
        $('#delete_qT').on('click',function(){
          swal({
          title: "Are you sure?",
          text: "Once deleted, you will not be able to recover this data!",
          icon: "warning",
          buttons: true,
          dangerMode: true,
        })
          .then((willDelete) => {
            if (willDelete) {
              var user_id = $(this).data('user_id');
              alert(user_id);
              window.location.href = "{{url('questionTemplate/')}}/"+user_id;
            } 
          });
        });
    });
  </script>    
  @endpush