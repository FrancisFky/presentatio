{{-- Success Message --}}
@if(session('success'))
    <x-notification type="success" :message="session('success')" />
@endif

{{-- Error Message --}}
@if(session('error'))
    <x-notification type="error" :message="session('error')" />
@endif

{{-- Warning Message --}}
@if(session('warning'))
    <x-notification type="warning" :message="session('warning')" />
@endif

{{-- Info Message --}}
@if(session('info'))
    <x-notification type="info" :message="session('info')" />
@endif

{{-- Status Message (for general status updates) --}}
@if(session('status'))
    <x-notification type="status" :message="session('status')" />
@endif 

{{-- Error Message --}}
@if($errors->any())
    @foreach($errors->all() as $error)
        <x-notification type="error" :message="$error" />
    @endforeach
@endif