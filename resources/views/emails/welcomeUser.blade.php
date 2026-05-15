@component('mail::message')
# Hello {{ $user->name }}!

{{-- Welcome to {{ config('app.name') }}! We're excited to have you on board. --}}

To get started, please verify your email address by clicking the button below:

@component('mail::button', ['url' => route('verify', $user->verification_token)])
Verify My Account
@endcomponent



Thanks,<br>
{{-- {{ config('app.name') }} Team --}}
@endcomponent
