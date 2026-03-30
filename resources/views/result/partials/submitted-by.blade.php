@if ($result->is_confirmed && $result->submittedBy)
    <div class="ui-card-footer border-t-0 pt-0 dark:border-t-0">
        <p class="text-xs text-gray-500 dark:text-gray-400">
            Submitted by {{ $result->submittedBy->name }} on {{ $submittedAt->format('j M Y') }} at {{ $submittedAt->format('H:i') }}.
        </p>
    </div>
@endif
