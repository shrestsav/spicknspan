@component('mail::message')
{{$mailData['message']}}<br>

@component('mail::button', ['url' => config('app.url')])
Dashboard
@endcomponent

Regards,<br>
Spick And Span Team
@endcomponent