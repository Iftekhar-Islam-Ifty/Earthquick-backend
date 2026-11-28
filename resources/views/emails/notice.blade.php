@if($noticeEvent === 'newsletter_campaign')
    @php
        $lines = explode("\nUnsubscribe from Nous Telos updates:\n", $noticeBody, 2);
        $unsubscribeLink = trim($lines[1] ?? '');
    @endphp
    <p>{!! nl2br(e($lines[0])) !!}</p>
    @if(filter_var($unsubscribeLink, FILTER_VALIDATE_URL))
        <p><a href="{{ $unsubscribeLink }}">Unsubscribe from Nous Telos updates</a></p>
    @endif
@elseif(in_array($noticeEvent, ['newsletter_confirmation', 'newsletter_welcome'], true))
    @php
        $lines = explode("\n", $noticeBody);
        $link = $lines[1] ?? null;
    @endphp
    <p>{{ $lines[0] }}</p>
    @if($link && filter_var($link, FILTER_VALIDATE_URL))
        <p><a href="{{ $link }}">{{ $noticeEvent === 'newsletter_confirmation' ? 'Confirm my email' : 'Unsubscribe from Nous Telos updates' }}</a></p>
    @endif
    @if($noticeEvent === 'newsletter_confirmation')<p>If you did not request this, ignore this email.</p>@endif
@else
    <p>{{ $noticeBody }}</p>
@endif
<p>Rthquick Customer Care</p>
@unless(in_array($noticeEvent, ['newsletter_confirmation', 'newsletter_welcome', 'newsletter_campaign'], true))
    <p>This is a transactional update. Please contact Rthquick through the website if you have a question.</p>
@endunless
