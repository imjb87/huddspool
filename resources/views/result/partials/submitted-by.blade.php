@if ($result->is_confirmed && $result->submittedBy)
    <p class="text-sm text-gray-500 dark:text-gray-400">
        Submitted by {{ $result->submittedBy->name }} on {{ $submittedAt->format('j M Y') }} at {{ $submittedAt->format('H:i') }}.
    </p>
@endif
