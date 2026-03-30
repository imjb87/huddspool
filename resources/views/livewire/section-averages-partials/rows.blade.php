<div class="ui-card-rows">
    @foreach ($averageRows as $row)
        @include('livewire.section-averages-partials.row', ['row' => $row])
    @endforeach
</div>
