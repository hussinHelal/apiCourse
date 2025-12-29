@component('mail::message')
# Hello {{ $user->name }}!


verify your new email address by clicking the button below:

@component('mail::button', ['url' => route('verify', $user->verification_token)])
Verify My Account
@endcomponent

Thanks,<br>
{{ config('app.name') }} Team
@endcomponent
