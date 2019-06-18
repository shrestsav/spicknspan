@extends('backend.layouts.app',['title'=>'Reports'])

@section('content')

  <section class="content" style="padding-top: 50px;">
    <div class="row">
      <div id="app">
        <div class="col-md-12">
          <div class="nav-tabs-custom">
            <reportlist></reportlist>
            <div class="tab-content">
              <div class="tab-pane" id="tab">
                <router-view></router-view>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <script type="text/javascript" src="{{ asset('js/app.js') }}"></script>

@endsection

@push('scripts')
<script type="text/javascript">

</script>
@endpush

