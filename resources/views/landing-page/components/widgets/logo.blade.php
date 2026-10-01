<a href="{{ url('/') }}" class="footer-logo">
    @if(isset($setting['footer_logo']))
        <img src="{{ url('storage/' . $setting['footer_logo']->id . '/' . $setting['footer_logo']->file_name) }}" alt="footer-logo" class="img-fluid">
    @else
        <img src="{{ asset('landing-images/general/footer-logo.png') }}" alt="footer-logo" class="img-fluid">
    @endif
</a>
