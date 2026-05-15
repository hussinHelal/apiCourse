<x-mail::message>
# Introduction

The body of your message.

<x-mail::button :url="{{ route('verify', $user->verification_token) }}">
verify Account
</x-mail::button>
{{-- @component('mail::button', ['url' => route('verify', $user->verification_token)])
Verify My Account
@endcomponent --}}

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
