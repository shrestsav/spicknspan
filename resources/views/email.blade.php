@if($mailData['email_type']==='registration')
	
	@include('backend.layouts.includes.mail.email.__registration')

@elseif($mailData['email_type']==='support')

	@include('backend.layouts.includes.mail.email.__support')

@elseif($mailData['email_type']==='rosterNotify')

	@include('backend.layouts.includes.mail.email.__rosterNotify')

@endif