@component('mail::message')
# {{ $venueName }} has been verified

Hi {{ $name }},

Great news — **{{ $venueName }}**, which you added to Best Coffee, has been
reviewed and verified by our team. It's now fully listed on the platform.

@component('mail::button', ['url' => $venueUrl])
View {{ $venueName }}
@endcomponent

Why not be the first to leave a review?

Thanks,
The Best Coffee team
@endcomponent
