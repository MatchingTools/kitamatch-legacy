@component('mail::message')
# Application is verified

Dear {{$guardian->last_name}} {{$guardian->first_name}},

your application has been verified today. You take part in the coordination phase.

Greetings,
{{ config('app.name') }}
@endcomponent
