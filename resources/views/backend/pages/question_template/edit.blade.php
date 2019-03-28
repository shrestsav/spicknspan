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

    });
  </script>    
  @endpush