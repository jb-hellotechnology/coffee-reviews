@component('mail::message')
# Your review of {{ $venueName }} has been removed

Hi {{ $name }},

Your review of **{{ $venueName }}** has been removed by a moderator as it
didn't meet our community guidelines.

@component('mail::panel')
{{ $reviewBody }}
@endcomponent

If you believe this was a mistake, please get in touch. You're welcome
to submit a new review focused on the coffee experience.

@component('mail::button', ['url' => $indexUrl])
Browse coffee shops
@endcomponent

Thanks,
Coffee Shop Reviews team
@endcomponent
