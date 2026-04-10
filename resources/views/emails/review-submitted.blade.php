@component('mail::message')
# Thanks for your review, {{ $name }}

Your review of **{{ $venue }}** has been submitted successfully.

@component('mail::panel')
{{ $body }}
@endcomponent

Your review helps other coffee fans find great coffee. If you scored
any of the specific dimensions, those scores are already contributing
to the venue's Coffee Score.

@component('mail::button', ['url' => $venueUrl])
View {{ $venue }}
@endcomponent

Thanks,
Coffee Shop Reviews team
@endcomponent
