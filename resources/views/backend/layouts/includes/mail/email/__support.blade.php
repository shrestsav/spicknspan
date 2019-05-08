@component('mail::message')
# From:  {{$mailData['name']}},
# Email:  {{$mailData['username']}},
# Contact:  {{$mailData['contact']}},
# Message:<br>
{{$mailData['message']}}<br>
@endcomponent