<div class="ticket-reply-header">
    <div class="ticket-reply-header__left">
        <a href="{{ route('admin.dashboard') }}" class="logo"><img src="{{ siteLogo() }}" alt="@lang('image')">
        </a>
    </div>
    <div class="ticket-reply-header__right">
        {{-- <div class="left-wrapper">
            <form class="navbar-search">
                <input type="search" name="#0" class="navbar-search-field" id="searchInput" autocomplete="off"
                    placeholder="@lang('Search here...')">
                <i class="las la-search"></i>
                <ul class="search-list"></ul>
            </form>
        </div> --}}
        <div class="right-wrapper">
            @can('admin.ticket.department')
                <a id="navbarDropdown" class="nav-link dropdown-btn" href="{{ route('admin.ticket.department') }}">
                    @lang('Ticket Open')
                </a>
            @endcan
            @can('admin.ticket.index')
                <a class="nav-link dropdown-btn" href="{{ route('admin.ticket.index') }}">
                    <span class="icon"><i class="las la-undo"></i></span> @lang('Back')
                </a>
            @endcan
        </div>
    </div>
</div>
