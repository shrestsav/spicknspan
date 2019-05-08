@component('mail::message')
# Hello {{$mailData['name']}},

{{$mailData['message']}}<br>

@component('mail::table')
| User Credentials 					   |
| -------------    					   |
| Username:  {{$mailData['username']}} |
| Password:  {{$mailData['password']}} |
@endcomponent
 
@component('mail::button', ['url' => config('app.url')])
Dashboard
@endcomponent

Regards,<br>
Spick And Span Team
@endcomponent