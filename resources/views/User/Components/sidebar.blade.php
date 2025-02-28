<div class="sidebar-content js-simplebar">
    <img src="{{ asset('image/AA_LOGO_White.png') }}" alt="logo" style="width: 200px; height: auto; display: block; margin: auto;">

    <ul class="sidebar-nav">
        <li class="sidebar-header">
            Pages
        </li>

        <li class="sidebar-item {{ request()->routeIs('User.page.dashboard')?'active':''}}">
            <a class="sidebar-link" href="{{route('User.page.dashboard')}}">
                <i class="align-middle" data-feather="sliders"></i> <span class="align-middle">Dashboard</span>
            </a>
        </li>

        <li class="sidebar-item {{ request()->routeis('User.training.Detail')?'active':''}}">
            <a class="sidebar-link" href="{{route('User.training.Detail')}}">
                <i class="align-middle" data-feather="user"></i> <span class="align-middle">Manage Training</span>
            </a>
        </li>
        <li class="sidebar-header">
            Reports
        </li>

        <li class="sidebar-item {{ request()->routeis('User.report.training')?'active':''}}">
            <a class="sidebar-link" href="{{route('User.report.training')}}">
                <i class="align-middle" data-feather="file-text"></i><span class="align-middle">Training Report</span>
            </a>
        </li>
        <li class="sidebar-header">
            Notifications  @if(isset($totalPending) && $totalPending > 0)
            <span class="indicator">{{ $totalPending }}</span>  {{-- Show total pending notifications --}}
        @endif
        </li>
        <li class="sidebar-item {{ request()->routeis('User.notifications.Detail')?'active':''}}">
            <a class="sidebar-link" href="{{route('User.notifications.Detail')}}">
                <i class="align-middle" data-feather="bell"></i> <span class="align-middle">Notifications</span>
            </a>
        </li>
    </ul>
</div>
