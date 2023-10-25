@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="https://www.unib.ac.id/wp-content/uploads/2019/10/logo-unib-e1571906638799.png" class="logo" alt="">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
