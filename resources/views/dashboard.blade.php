@if(auth()->user()->isAdmin())
    @php redirect()->route('admin.dashboard')->send(); @endphp
@else
    @php redirect()->route('client.dashboard')->send(); @endphp
@endif