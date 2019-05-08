@if($mailData['email_type']==='registration')
	
	@include('backend.layouts.includes.mail.email.__registration')

@elseif($mailData['email_type']==='support')

	@include('backend.layouts.includes.mail.email.__support')

@endif