<div class="divide-y divide-gray-200 dark:divide-zinc-800/80">
    @foreach ($averageRows as $row)
        @include('livewire.section-averages-partials.row', ['row' => $row])
    @endforeach
</div>
