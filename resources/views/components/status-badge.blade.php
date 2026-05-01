@props(['status'])

@php
    $class = match($status) {
        'submitted' => 'status-submitted',
        'in_review' => 'status-in_review',
        'resolved' => 'status-resolved',
        'rejected' => 'status-rejected',
        default => 'status-submitted',
    };
@endphp

<span {{ $attributes->merge(['class' => "status-badge {$class}"]) }}>
    {{ str($status)->replace('_', ' ')->title() }}
</span>
