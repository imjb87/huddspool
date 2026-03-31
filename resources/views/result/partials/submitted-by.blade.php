@if ($result->is_confirmed && $result->submittedBy)
    <div class="ui-card-footer">
        <p class="text-xs text-gray-500 dark:text-gray-400">
            Submitted by {{ $result->submittedBy->name }} on {{ $submittedAt->format('j M Y') }} at {{ $submittedAt->format('H:i') }}.
        </p>
    </div>
@endif
