@extends('backend.layouts.app',['title'=>'Dashboard'])

@section('content')

  <section class="content" style="padding-top: 50px;">
    <div class="row">
      <div class="col-md-12">
        @if ($errors->any())
          <div class="alert alert-danger">
              @foreach ($errors->all() as $error)
                  {{ $error }}<br>
              @endforeach
            </div>
        @endif
        @if (\Session::has('message'))
          <div class="alert alert-success custom_success_msg">
              {{ \Session::get('message') }}
          </div>
        @endif
        <div class="box">
          <div class="box-header" style="text-align: center;">
            <h3 class="box-title" >Welcome You are Logged In</h3>
          </div>
          <div class="box-body  no-padding">
          </div>
        </div>
      </div>
      @role('superAdmin')
        @if(count($supportMails))
          <div class="col-md-8">
            <div class="box box-primary">
              <div class="box-header with-border">
                <h3 class="box-title">SUPPORT MESSAGES</h3>
              </div>
              <div class="box-body no-padding">
                <div class="table-responsive mailbox-messages">
                  <table class="table table-hover table-striped">
                    <tbody>
                    @foreach($supportMails as $supportMail)
                    <tr>
                      <td><input type="checkbox"></td>
                      <td class="mailbox-star"><a href="#"><i class="fa fa-star-o text-yellow"></i></a></td>
                      <td class="mailbox-name"><a href="javascript:;"  data-toggle="modal" data-target="#supportMailDetails_{{$supportMail->id}}">{{$supportMail->name}}</a></td>
                      <td class="mailbox-subject"><b>SUPPORT</b> - {{$supportMail->subject}}
                      </td>
                      <td>
                        @php
                          if($supportMail->assigned_to_name!='' && $supportMail->assigned_to_name!=null){
                            $assigned_to = $supportMail->assigned_to_name;
                            $status = true;
                          }
                          else{
                            $assigned_to = 'Not Assigned';
                            $status = false;
                          }
                        @endphp
                        @if($status)
                          <b>Assigned To: </b> {{$assigned_to}} 
                        @else
                          {{$assigned_to}} 
                        @endif
                      </td>
                      <td class="mailbox-date">{{\Carbon\Carbon::parse($supportMail->created_at)->diffForHumans()}}</td>
                    </tr>

                    <div class="modal modal-default fade" id="supportMailDetails_{{$supportMail->id}}">
                      <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                          <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">{{$supportMail->subject}}</h4>
                          </div>
                          <div class="modal-body">
                            <ul style="list-style: none;">
                              <li>
                                <b>Status: </b>
                                @if($status)
                                  Assigned to 
                                @endif
                                {{$assigned_to}}
                              </li>
                              <li><b>From: </b> {{$supportMail->name}}</li>
                              <li><b>Email: </b> {{$supportMail->email}}</li>
                              <li><b>Contact: </b> {{$supportMail->contact}}</li>
                              <li><b>Time: </b> {{\Carbon\Carbon::parse($supportMail->created_at)->diffForHumans()}}</li>
                              <li><b>Message: </b> <br>{{$supportMail->message}}</li>
                            </ul>
                          </div>
                          <div class="modal-footer">
                            <div class="pull-left">
                              <form role="form" action="{{route('assignSupportTask')}}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="support_message_id" value="{{$supportMail->id}}">
                                <input type="hidden" name="type" value="assign">
                                <select class="select2" name="assign_user_id" required>
                                  <option disabled selected value>Select User</option>
                                  @foreach($superUsers as $superUser)
                                    <option value="{{$superUser->id}}" >
                                      {{$superUser->name }}
                                    </option>
                                  @endforeach
                                </select>
                                <button type="submit" class="btn btn-primary">Assign</button>
                              </form>
                            </div>
                            <div class="pull-right">
                              <form role="form" action="{{route('assignSupportTask')}}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="type" value="mark_done">
                                <input type="hidden" name="support_message_id" value="{{$supportMail->id}}">
                                <button type="submit" class="btn btn-success">Mark as Done</button>
                              </form>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    @endforeach
                    
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        @endif
        @if($leave_apps)
          <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-green">
              <div class="inner">
                <h3>{{$leave_apps}}</h3>
                <p>Pending Leave Requests</p>
              </div>
              <div class="icon">
                <i class="fa fa-newspaper-o"></i>
              </div>
              <a href="{{url('/leaveApplication')}}" class="small-box-footer">Take me <i class="fa fa-arrow-circle-right"></i></a>
            </div>
          </div>
        @endif
        <div class="col-md-4">
          @if(count($assignedTasks))
            <div class="box box-primary">
              <div class="box-header">
                <i class="ion ion-clipboard"></i>
                <h3 class="box-title">Assigned Tasks</h3>
              </div>
              <div class="box-body">
                <ul class="todo-list">
                  @foreach($assignedTasks as $assignedTask)
                  <li>
                    <span class="handle">
                      <i class="fa fa-ellipsis-v"></i>
                      <i class="fa fa-ellipsis-v"></i>
                    </span>
                    <input type="checkbox" value="">
                    <span class="text">{{$assignedTask->subject}}</span>
                    <small class="label label-warning"><i class="fa fa-clock-o"></i> {{\Carbon\Carbon::parse($assignedTask->updated_at)->diffForHumans()}}</small>
                    <div class="tools">
                      <i class="fa fa-edit"></i>
                      <i class="fa fa-trash-o"></i>
                    </div>
                  </li>
                  @endforeach
                </ul>
              </div>
            </div>
          @endif
        </div>
      @endrole
    </div>
  </section>

@endsection

@push('scripts')
<script type="text/javascript">

    // jQuery UI sortable for the todo list
  $('.todo-list').sortable({
    placeholder         : 'sort-highlight',
    handle              : '.handle',
    forcePlaceholderSize: true,
    zIndex              : 999999
  });

  /* The todo list plugin */
  $('.todo-list').todoList({
    onCheck  : function () {
      window.console.log($(this), 'The element has been checked');
    },
    onUnCheck: function () {
      window.console.log($(this), 'The element has been unchecked');
    }
  });
</script>
@endpush

