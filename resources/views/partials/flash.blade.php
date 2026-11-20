{{-- Session flash message set by a controller redirect: ->with('status', '...') --}}
@if (session('status'))
    <flux:callout variant="success" icon="check-circle" :heading="session('status')" data-test="flash-status" />
@endif
