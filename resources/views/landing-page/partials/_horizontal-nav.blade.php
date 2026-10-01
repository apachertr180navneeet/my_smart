<div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mx-auto mb-2 mb-xl-0">
        @if(isset($sectionData['header_setting']) && $sectionData['header_setting'] == 1 && isset($sectionData['menu_items']))
            @foreach($sectionData['menu_items'] as $menu)
                <li class="nav-item">
                    <a class="nav-link" href="{{ $menu['url'] ?? '#' }}">{{ $menu['label'] ?? '' }}</a>
                </li>
            @endforeach
        @endif
    </ul>
</div>
