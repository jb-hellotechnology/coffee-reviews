@component('mail::message')
# Welcome to Coffee Shop Reviews, {{ $name }}!

Thanks for joining — you're now part of a community dedicated to finding
the best coffee, not just the best café.

Here's what you can do:

@component('mail::panel')
Browse reviewed coffee shops, add venues you love, and leave detailed
reviews scoring everything from espresso extraction to bean sourcing.
@endcomponent

@component('mail::button', ['url' => $indexUrl])
Browse coffee shops
@endcomponent

Know a great coffee shop that isn't listed yet?

@component('mail::button', ['url' => $createUrl, 'color' => 'success'])
Add a venue
@endcomponent

Thanks,
Coffee Shop Reviews team
@endcomponent
