<div class="divide-y divide-gray-200 dark:divide-zinc-800/80" data-result-card-frames>
    @foreach ($result->frames as $index => $frame)
        @include('result.partials.frame-row')
    @endforeach
</div>
