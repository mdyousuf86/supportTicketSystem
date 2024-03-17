<div class="sidebar bg--dark">
    <button class="res-sidebar-close-btn"><i class="las la-times"></i></button>
    <div class="sidebar__inner">
        <div class="sidebar__logo">
            <a href="{{ route('admin.dashboard') }}" class="sidebar__main-logo"><img src="{{ siteLogo() }}"
                    alt="@lang('image')"></a>
        </div>

        <div class="sidebar__menu-wrapper" id="sidebar__menuWrapper">
            <ul class="sidebar__menu">
                @can('admin.dashboard')
                    <li class="sidebar-menu-item {{ menuActive('admin.dashboard') }}">
                        <a href="{{ route('admin.dashboard') }}" class="nav-link ">
                            <i class="menu-icon las la-home"></i>
                            <span class="menu-title">@lang('Dashboard')</span>
                        </a>
                    </li>
                @endcan

                @can('admin.statistics')
                    <li class="sidebar-menu-item {{ menuActive('admin.statistics') }}">
                        <a href="{{ route('admin.statistics') }}" class="nav-link ">
                            <i class="menu-icon las la-signal"></i>
                            <span class="menu-title">@lang('Ticket Statistics')</span>
                        </a>
                    </li>
                @endcan


                @can(['admin.staff.index', 'admin.roles.index', 'admin.permissions.index'])
                    <li class="sidebar-menu-item sidebar-dropdown">
                        <a class="{{ menuActive(['admin.staff*', 'admin.roles.*'], 3) }}" href="javascript:void(0)">
                            <i class="menu-icon las la-users"></i>
                            <span class="menu-title">@lang('Manage Staff')</span>
                        </a>
                        <div class="sidebar-submenu {{ menuActive(['admin.staff*', 'admin.roles.*'], 2) }}">
                            <ul>
                                @can('admin.staff.index')
                                    <li class="sidebar-menu-item {{ menuActive('admin.staff*') }}">
                                        <a class="nav-link" href="{{ route('admin.staff.index') }}">
                                            <i class="menu-icon las la-dot-circle"></i>
                                            <span class="menu-title">@lang('All Staff')</span>
                                        </a>
                                    </li>
                                @endcan

                                @can('admin.roles.index')
                                    <li class="sidebar-menu-item {{ menuActive('admin.roles*') }}">
                                        <a class="nav-link" href="{{ route('admin.roles.index') }}">
                                            <i class="menu-icon las la-dot-circle"></i>
                                            <span class="menu-title">@lang('Roles')</span>
                                        </a>
                                    </li>
                                @endcan
                                {{-- <li class="sidebar-menu-item {{ menuActive('admin.permissions*') }}">
                                    <a class="nav-link" href="{{ route('admin.permissions.index') }}">
                                        <i class="menu-icon las la-dot-circle"></i>
                                        <span class="menu-title">@lang('Permissions')</span>
                                    </a>
                                </li> --}}
                            </ul>
                        </div>
                    </li>
                @endcan


                @can('admin.department.index')
                    <li class="sidebar-menu-item {{ menuActive('admin.department.*') }}">
                        <a href="{{ route('admin.department.index') }}" class="nav-link ">
                            <i class="menu-icon  lab la-buffer"></i>
                            <span class="menu-title">@lang('Department')</span>
                        </a>
                    </li>
                @endcan

                @can('admin.status.index')
                    <li class="sidebar-menu-item {{ menuActive('admin.status.index') }}">
                        <a href="{{ route('admin.status.index') }}" class="nav-link ">
                            <i class="menu-icon las la-sort"></i>
                            <span class="menu-title">@lang('Status')</span>
                        </a>
                    </li>
                @endcan

                @can('admin.priority.index')
                    <li class="sidebar-menu-item {{ menuActive('admin.priority.index') }}">
                        <a href="{{ route('admin.priority.index') }}" class="nav-link ">
                            <i class="menu-icon las la-exclamation-triangle"></i>
                            <span class="menu-title">@lang('Priority')</span>
                        </a>
                    </li>
                @endcan

                @can('admin.spam.filter.index')
                    <li class="sidebar-menu-item {{ menuActive('admin.spam.filter.index') }}">
                        <a href="{{ route('admin.spam.filter.index') }}" class="nav-link ">
                            <i class="menu-icon las la-filter"></i>
                            <span class="menu-title">@lang('Spam Filtes')</span>
                        </a>
                    </li>
                @endcan

                @can(['admin.category.all', 'admin.reply.index'])
                    <li class="sidebar-menu-item sidebar-dropdown">
                        <a href="javascript:void(0)" class="{{ menuActive(['admin.category*', 'admin.reply.index'], 3) }}">
                            <i class="menu-icon las la-reply-all"></i>
                            <span class="menu-title">@lang('Predefined Reply')</span>
                        </a>
                        <div class="sidebar-submenu {{ menuActive(['admin.category*', 'admin.reply.index'], 2) }} ">
                            <ul>
                                @can('admin.category.all')
                                    <li class="sidebar-menu-item {{ menuActive('admin.category.all') }} ">
                                        <a href="{{ route('admin.category.all') }}" class="nav-link">
                                            <i class="menu-icon las la-dot-circle"></i>
                                            <span class="menu-title">@lang('Categories')</span>
                                        </a>
                                    </li>
                                @endcan

                                @can('admin.reply.index')
                                    <li class="sidebar-menu-item {{ menuActive('admin.reply.index') }} ">
                                        <a href="{{ route('admin.reply.index') }}" class="nav-link">
                                            <i class="menu-icon las la-dot-circle"></i>
                                            <span class="menu-title">@lang('Replies')</span>
                                        </a>
                                    </li>
                                @endcan
                            </ul>
                        </div>
                    </li>
                @endcan

                @can(['admin.users*'])
                    <li class="sidebar-menu-item sidebar-dropdown">
                        <a href="javascript:void(0)" class="{{ menuActive('admin.users*', 3) }}">
                            <i class="menu-icon las la-users"></i>
                            <span class="menu-title">@lang('Manage Users')</span>

                            @if (
                                $bannedUsersCount > 0 ||
                                    $emailUnverifiedUsersCount > 0 ||
                                    $mobileUnverifiedUsersCount > 0 ||
                                    $kycUnverifiedUsersCount > 0 ||
                                    $kycPendingUsersCount > 0)
                                <span class="menu-badge pill bg--danger ms-auto">
                                    <i class="fa fa-exclamation"></i>
                                </span>
                            @endif
                        </a>
                        <div class="sidebar-submenu {{ menuActive('admin.users*', 2) }} ">
                            <ul>
                                @can('admin.users.active')
                                    <li class="sidebar-menu-item {{ menuActive('admin.users.active') }} ">
                                        <a href="{{ route('admin.users.active') }}" class="nav-link">
                                            <i class="menu-icon las la-dot-circle"></i>
                                            <span class="menu-title">@lang('Active Users')</span>
                                        </a>
                                    </li>
                                @endcan

                                @can('admin.users.banned')
                                    <li class="sidebar-menu-item {{ menuActive('admin.users.banned') }} ">
                                        <a href="{{ route('admin.users.banned') }}" class="nav-link">
                                            <i class="menu-icon las la-dot-circle"></i>
                                            <span class="menu-title">@lang('Banned Users')</span>
                                            @if ($bannedUsersCount)
                                                <span class="menu-badge pill bg--danger ms-auto">{{ $bannedUsersCount }}</span>
                                            @endif
                                        </a>
                                    </li>
                                @endcan

                                @can('admin.users.email.unverified')
                                    <li class="sidebar-menu-item  {{ menuActive('admin.users.email.unverified') }}">
                                        <a href="{{ route('admin.users.email.unverified') }}" class="nav-link">
                                            <i class="menu-icon las la-dot-circle"></i>
                                            <span class="menu-title">@lang('Email Unverified')</span>

                                            @if ($emailUnverifiedUsersCount)
                                                <span
                                                    class="menu-badge pill bg--danger ms-auto">{{ $emailUnverifiedUsersCount }}</span>
                                            @endif
                                        </a>
                                    </li>
                                @endcan

                                @can('admin.users.mobile.unverified')
                                    <li class="sidebar-menu-item {{ menuActive('admin.users.mobile.unverified') }}">
                                        <a href="{{ route('admin.users.mobile.unverified') }}" class="nav-link">
                                            <i class="menu-icon las la-dot-circle"></i>
                                            <span class="menu-title">@lang('Mobile Unverified')</span>
                                            @if ($mobileUnverifiedUsersCount)
                                                <span
                                                    class="menu-badge pill bg--danger ms-auto">{{ $mobileUnverifiedUsersCount }}</span>
                                            @endif
                                        </a>
                                    </li>
                                @endcan

                                @can('admin.users.all')
                                    <li class="sidebar-menu-item {{ menuActive('admin.users.all') }} ">
                                        <a class="nav-link" href="{{ route('admin.users.all') }}">
                                            <i class="menu-icon las la-dot-circle"></i>
                                            <span class="menu-title">@lang('All Users')</span>
                                        </a>
                                    </li>
                                @endcan

                                @can('admin.users.notification.all')
                                    <li class="sidebar-menu-item {{ menuActive('admin.users.notification.all') }}">
                                        <a class="nav-link" href="{{ route('admin.users.notification.all') }}">
                                            <i class="menu-icon las la-dot-circle"></i>
                                            <span class="menu-title">@lang('Notification to All')</span>
                                        </a>
                                    </li>
                                @endcan
                            </ul>
                        </div>
                    </li>
                @endcan

                @can(['admin.ticket*'])
                    <li class="sidebar-menu-item sidebar-dropdown">
                        <a href="javascript:void(0)" class="{{ menuActive('admin.ticket*', 3) }}">
                            <i class="menu-icon la la-ticket"></i>
                            <span class="menu-title">@lang('Support Ticket') </span>
                            @if ($ticketStatuses->where('is_awaiting', 1)->where('tickets_count', '>', 0)->count())
                                <span class="menu-badge pill bg--danger ms-auto">
                                    <i class="fa fa-exclamation"></i>
                                </span>
                            @endif
                        </a>
                        <div class="sidebar-submenu {{ menuActive('admin.ticket*', 2) }} ">
                            <ul>
                                @foreach ($ticketStatuses as $k => $ticketStatus)
                                    <li
                                        class="sidebar-menu-item {{ menuActive('admin.ticket.index.status', param: $ticketStatus->id) }} ">
                                        <a href="{{ route('admin.ticket.index.status', $ticketStatus->id) }}"
                                            class="nav-link">
                                            <i class="menu-icon las la-dot-circle"></i>
                                            <span class="menu-title">{{ __(@$ticketStatus->title) }}</span>
                                            @if ($ticketStatus->is_awaiting && $ticketStatus->tickets_count)
                                                <span
                                                    class="menu-badge pill bg--danger ms-auto">{{ $ticketStatus->tickets_count }}</span>
                                            @endif
                                        </a>
                                    </li>
                                @endforeach

                                <li class="sidebar-menu-item  {{ menuActive('admin.ticket.index') }}">
                                    <a href="{{ route('admin.ticket.index') }}" class="nav-link">
                                        <i class="menu-icon las la-dot-circle"></i>
                                        <span class="menu-title">@lang('All Ticket')</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endcan

                @can('admin.feedback.index')
                    <li class="sidebar-menu-item {{ menuActive('admin.feedback.index') }}">
                        <a href="{{ route('admin.feedback.index') }}" class="nav-link ">
                            <i class="menu-icon las la-comment"></i>
                            <span class="menu-title">@lang('Ticket Feedback')</span>
                        </a>
                    </li>
                @endcan


                @can(['admin.report*'])
                    <li class="sidebar-menu-item sidebar-dropdown">
                        <a href="javascript:void(0)" class="{{ menuActive('admin.report*', 3) }}">
                            <i class="menu-icon la la-list"></i>
                            <span class="menu-title">@lang('Report') </span>
                        </a>
                        <div class="sidebar-submenu {{ menuActive('admin.report*', 2) }} ">
                            <ul>
                                @can('admin.report.login.history')
                                    <li
                                        class="sidebar-menu-item {{ menuActive(['admin.report.login.history', 'admin.report.login.ipHistory']) }}">
                                        <a class="nav-link" href="{{ route('admin.report.login.history') }}">
                                            <i class="menu-icon las la-dot-circle"></i>
                                            <span class="menu-title">@lang('Login History')</span>
                                        </a>
                                    </li>
                                @endcan

                                @can('admin.report.notification.history')
                                    <li class="sidebar-menu-item {{ menuActive('admin.report.notification.history') }}">
                                        <a class="nav-link" href="{{ route('admin.report.notification.history') }}">
                                            <i class="menu-icon las la-dot-circle"></i>
                                            <span class="menu-title">@lang('Notification History')</span>
                                        </a>
                                    </li>
                                @endcan

                            </ul>
                        </div>
                    </li>
                @endcan

                @if (can([
                        'admin.setting.index',
                        'admin.cron.index',
                        'admin.setting.logo.icon',
                        'admin.setting.system.configuration',
                        'admin.extensions.index',
                        'admin.language.manage',
                        'admin.seo',
                        'admin.setting.notification',
                    ]))
                    <li class="sidebar__menu-header">@lang('Settings')</li>
                @endif

                @can('admin.setting.index')
                    <li class="sidebar-menu-item {{ menuActive('admin.setting.index') }}">
                        <a class="nav-link" href="{{ route('admin.setting.index') }}">
                            <i class="menu-icon las la-life-ring"></i>
                            <span class="menu-title">@lang('General Setting')</span>
                        </a>
                    </li>
                @endcan

                @can('admin.cron.index')
                    <li class="sidebar-menu-item {{ menuActive('admin.cron*') }}">
                        <a class="nav-link" href="{{ route('admin.cron.index') }}">
                            <i class="menu-icon las la-clock"></i>
                            <span class="menu-title">@lang('Cron Job Setting')</span>
                        </a>
                    </li>
                @endcan

                @can('admin.setting.system.configuration')
                    <li class="sidebar-menu-item {{ menuActive('admin.setting.system.configuration') }}">
                        <a class="nav-link" href="{{ route('admin.setting.system.configuration') }}">
                            <i class="menu-icon las la-cog"></i>
                            <span class="menu-title">@lang('System Configuration')</span>
                        </a>
                    </li>
                @endcan

                @can('admin.setting.logo.icon')
                    <li class="sidebar-menu-item {{ menuActive('admin.setting.logo.icon') }}">
                        <a class="nav-link" href="{{ route('admin.setting.logo.icon') }}">
                            <i class="menu-icon las la-images"></i>
                            <span class="menu-title">@lang('Logo & Favicon')</span>
                        </a>
                    </li>
                @endcan

                @can('admin.extensions.index')
                    <li class="sidebar-menu-item {{ menuActive('admin.extensions.index') }}">
                        <a class="nav-link" href="{{ route('admin.extensions.index') }}">
                            <i class="menu-icon las la-cogs"></i>
                            <span class="menu-title">@lang('Extensions')</span>
                        </a>
                    </li>
                @endcan

                @can('admin.language.manage')
                    <li class="sidebar-menu-item  {{ menuActive(['admin.language.manage', 'admin.language.key']) }}">
                        <a class="nav-link" data-default-url="{{ route('admin.language.manage') }}"
                            href="{{ route('admin.language.manage') }}">
                            <i class="menu-icon las la-language"></i>
                            <span class="menu-title">@lang('Language') </span>
                        </a>
                    </li>
                @endcan

                @can('admin.seo')
                    <li class="sidebar-menu-item {{ menuActive('admin.seo') }}">
                        <a class="nav-link" href="{{ route('admin.seo') }}">
                            <i class="menu-icon las la-globe"></i>
                            <span class="menu-title">@lang('SEO Manager')</span>
                        </a>
                    </li>
                @endcan

                @can(['admin.setting.notification*'])
                    <li class="sidebar-menu-item sidebar-dropdown">
                        <a href="javascript:void(0)" class="{{ menuActive('admin.setting.notification*', 3) }}">
                            <i class="menu-icon las la-bell"></i>
                            <span class="menu-title">@lang('Notification Setting')</span>
                        </a>
                        <div class="sidebar-submenu {{ menuActive('admin.setting.notification*', 2) }} ">
                            <ul>
                                @can('admin.setting.notification.global')
                                    <li class="sidebar-menu-item {{ menuActive('admin.setting.notification.global') }} ">
                                        <a class="nav-link" href="{{ route('admin.setting.notification.global') }}">
                                            <i class="menu-icon las la-dot-circle"></i>
                                            <span class="menu-title">@lang('Global Template')</span>
                                        </a>
                                    </li>
                                @endcan

                                @can('admin.setting.notification.email')
                                    <li class="sidebar-menu-item {{ menuActive('admin.setting.notification.email') }} ">
                                        <a class="nav-link" href="{{ route('admin.setting.notification.email') }}">
                                            <i class="menu-icon las la-dot-circle"></i>
                                            <span class="menu-title">@lang('Email Setting')</span>
                                        </a>
                                    </li>
                                @endcan

                                @can('admin.setting.notification.sms')
                                    <li class="sidebar-menu-item {{ menuActive('admin.setting.notification.sms') }} ">
                                        <a class="nav-link" href="{{ route('admin.setting.notification.sms') }}">
                                            <i class="menu-icon las la-dot-circle"></i>
                                            <span class="menu-title">@lang('SMS Setting')</span>
                                        </a>
                                    </li>
                                @endcan

                                @can('admin.setting.notification.templates')
                                    <li class="sidebar-menu-item {{ menuActive('admin.setting.notification.templates') }} ">
                                        <a class="nav-link" href="{{ route('admin.setting.notification.templates') }}">
                                            <i class="menu-icon las la-dot-circle"></i>
                                            <span class="menu-title">@lang('Notification Templates')</span>
                                        </a>
                                    </li>
                                @endcan

                            </ul>
                        </div>
                    </li>
                @endcan

                @if (can(['admin.frontend.templates', 'admin.frontend.manage.pages', 'admin.frontend.sections']))
                    <li class="sidebar__menu-header">@lang('Frontend Manager')</li>
                @endif

                @can('admin.frontend.templates')
                    <li class="sidebar-menu-item {{ menuActive('admin.frontend.templates') }}">
                        <a class="nav-link " href="{{ route('admin.frontend.templates') }}">
                            <i class="menu-icon la la-puzzle-piece"></i>
                            <span class="menu-title">@lang('Manage Templates')</span>
                        </a>
                    </li>
                @endcan

                @can('admin.frontend.manage.pages')
                    <li class="sidebar-menu-item {{ menuActive('admin.frontend.manage.*') }}">
                        <a class="nav-link " href="{{ route('admin.frontend.manage.pages') }}">
                            <i class="menu-icon la la-list"></i>
                            <span class="menu-title">@lang('Manage Pages')</span>
                        </a>
                    </li>
                @endcan

                @can(['admin.frontend.sections*'])
                    <li class="sidebar-menu-item sidebar-dropdown">
                        <a class="{{ menuActive('admin.frontend.sections*', 3) }}" href="javascript:void(0)">
                            <i class="menu-icon la la-html5"></i>
                            <span class="menu-title">@lang('Manage Section')</span>
                        </a>
                        <div class="sidebar-submenu {{ menuActive('admin.frontend.sections*', 2) }} ">
                            <ul>
                                @php
                                    $lastSegment = collect(request()->segments())->last();
                                @endphp
                                @foreach (getPageSections(true) as $k => $secs)
                                    @if ($secs['builder'])
                                        <li class="sidebar-menu-item  @if ($lastSegment == $k) active @endif ">
                                            <a class="nav-link" href="{{ route('admin.frontend.sections', $k) }}">
                                                <i class="menu-icon las la-dot-circle"></i>
                                                <span class="menu-title">{{ __($secs['name']) }}</span>
                                            </a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    </li>
                @endcan


                @if (can([
                        'admin.maintenance.mode',
                        'admin.setting.cookie',
                        'admin.system',
                        'admin.setting.custom.css',
                        'admin.request.report',
                    ]))
                    <li class="sidebar__menu-header">@lang('Extra')</li>
                @endif

                @can('admin.maintenance.mode')
                    <li class="sidebar-menu-item {{ menuActive('admin.maintenance.mode') }}">
                        <a class="nav-link" href="{{ route('admin.maintenance.mode') }}">
                            <i class="menu-icon las la-robot"></i>
                            <span class="menu-title">@lang('Maintenance Mode')</span>
                        </a>
                    </li>
                @endcan

                @can('admin.setting.cookie')
                    <li class="sidebar-menu-item {{ menuActive('admin.setting.cookie') }}">
                        <a class="nav-link" href="{{ route('admin.setting.cookie') }}">
                            <i class="menu-icon las la-cookie-bite"></i>
                            <span class="menu-title">@lang('GDPR Cookie')</span>
                        </a>
                    </li>
                @endcan


                @can(['admin.system*'])
                    <li class="sidebar-menu-item sidebar-dropdown">
                        <a class="{{ menuActive('admin.system*', 3) }}" href="javascript:void(0)">
                            <i class="menu-icon la la-server"></i>
                            <span class="menu-title">@lang('System')</span>
                        </a>
                        <div class="sidebar-submenu {{ menuActive('admin.system*', 2) }} ">
                            <ul>
                                @can('admin.system.info')
                                    <li class="sidebar-menu-item {{ menuActive('admin.system.info') }}">
                                        <a class="nav-link" href="{{ route('admin.system.info') }}">
                                            <i class="menu-icon las la-dot-circle"></i>
                                            <span class="menu-title">@lang('Application')</span>
                                        </a>
                                    </li>
                                @endcan

                                @can('admin.system.server.info')
                                    <li class="sidebar-menu-item {{ menuActive('admin.system.server.info') }}">
                                        <a class="nav-link" href="{{ route('admin.system.server.info') }}">
                                            <i class="menu-icon las la-dot-circle"></i>
                                            <span class="menu-title">@lang('Server')</span>
                                        </a>
                                    </li>
                                @endcan

                                @can('admin.system.optimize')
                                    <li class="sidebar-menu-item {{ menuActive('admin.system.optimize') }}">
                                        <a class="nav-link" href="{{ route('admin.system.optimize') }}">
                                            <i class="menu-icon las la-dot-circle"></i>
                                            <span class="menu-title">@lang('Cache')</span>
                                        </a>
                                    </li>
                                @endcan

                                @can('admin.system.update')
                                    <li class="sidebar-menu-item {{ menuActive('admin.system.update') }} ">
                                        <a class="nav-link" href="{{ route('admin.system.update') }}">
                                            <i class="menu-icon las la-dot-circle"></i>
                                            <span class="menu-title">@lang('Update')</span>
                                        </a>
                                    </li>
                                @endcan

                            </ul>
                        </div>
                    </li>
                @endcan


                @can('admin.setting.custom.css')
                    <li class="sidebar-menu-item {{ menuActive('admin.setting.custom.css') }}">
                        <a class="nav-link" href="{{ route('admin.setting.custom.css') }}">
                            <i class="menu-icon lab la-css3-alt"></i>
                            <span class="menu-title">@lang('Custom CSS')</span>
                        </a>
                    </li>
                @endcan

                @can('admin.request.report')
                    <li class="sidebar-menu-item {{ menuActive('admin.request.report') }}">
                        <a class="nav-link" data-default-url="{{ route('admin.request.report') }}"
                            href="{{ route('admin.request.report') }}">
                            <i class="menu-icon las la-bug"></i>
                            <span class="menu-title">@lang('Report & Request') </span>
                        </a>
                    </li>
                @endcan
            </ul>
            <div class="text-center mb-3 text-uppercase">
                <span class="text--primary">{{ __(systemDetails()['name']) }}</span>
                <span class="text--success">@lang('V'){{ systemDetails()['version'] }} </span>
            </div>
        </div>
    </div>
</div>
<!-- sidebar end -->

@push('script')
    <script>
        if ($('li').hasClass('active')) {
            $('#sidebar__menuWrapper').animate({
                scrollTop: eval($(".active").offset().top - 320)
            }, 500);
        }
    </script>
@endpush
