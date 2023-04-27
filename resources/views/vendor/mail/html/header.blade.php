@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="https://laravel.com/img/notification-logo.png" class="logo" alt="Laravel Logo">
@else
<img class="logo" src="{{ asset('images/logo.png') }}" alt="Huddersfield Pool Logo" />
@endif
</a>
</td>
</tr>
