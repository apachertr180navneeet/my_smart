<a href="{{ url('/') }}" class="navbar-brand">
    @if(isset($setting['dark_logo']))
        <img src="{{ url('storage/' . $setting['dark_logo']->id . '/' . $setting['dark_logo']->file_name) }}" alt="logo" class="img-fluid">
    @else
        <img src="{{ asset('landing-images/general/logo.png') }}" alt="logo" class="img-fluid">
    @endif
</a>
