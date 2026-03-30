<div class="ui-card-rows" data-result-card-frames>
    @foreach ($result->frames as $index => $frame)
        @include('result.partials.frame-row')
    @endforeach
</div>
