<div class="ui-averages-item-group" data-section-averages-list data-slot="item-group">
    @include('livewire.section-averages-partials.header')

    @foreach ($averageRows as $row)
        @include('livewire.section-averages-partials.row', ['row' => $row])
    @endforeach
</div>
