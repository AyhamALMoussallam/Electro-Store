@props(['url'])
<tr>
<td class="header" align="center">
<table align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td align="center" style="padding: 28px 16px;">
<a href="{{ $url }}" style="text-decoration: none; display: inline-block;">
<h1 style="margin: 0; padding: 0; text-align: center; font-size: 32px; font-weight: 700; line-height: 1.2; color: #D10024; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
{{ $slot ?: config('mail.from.name', 'إلكترو') }}
</h1>
</a>
</td>
</tr>
</table>
</td>
</tr>
