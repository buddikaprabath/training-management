<div class="sidebar-content js-simplebar">
    <img src="{{ asset('image/AA_LOGO_White.png') }}" alt="logo" style="width: 200px; height: auto; display: block; margin: auto;">

    <ul class="sidebar-nav">
        <li class="sidebar-header">
            Pages
        </li>

        <li class="sidebar-item {{ request()->routeIs('Admin.HRAdmin.page.dashboard')?'active':''}}">
            <a class="sidebar-link" href="{{route('Admin.HRAdmin.page.dashboard')}}">
                <i class="align-middle" data-feather="sliders"></i> <span class="align-middle">Dashboard</span>
            </a>
        </li>

        <li class="sidebar-item {{ request()->routeis('Admin.HRAdmin.training.Detail')?'active':''}}">
            <a class="sidebar-link" href="{{route('Admin.HRAdmin.training.Detail')}}">
                <i class="align-middle" data-feather="user"></i> <span class="align-middle">Manage Training</span>
            </a>
        </li>

        <li class="sidebar-item {{ request()->routeis('Admin.HRAdmin.budget.Detail')?'active':''}}">
            <a class="sidebar-link" href="{{route('Admin.HRAdmin.budget.Detail')}}">
                <i class="align-middle" data-feather="user"></i> <span class="align-middle">Manage Budget</span>
            </a>
        </li>

        <li class="sidebar-item {{ request()->routeis('Admin.HRAdmin.institute.Detail')?'active':''}}">
            <a class="sidebar-link" href="{{route('Admin.HRAdmin.institute.Detail')}}">
                <i class="align-middle" data-feather="user"></i> <span class="align-middle">Manage Institute</span>
            </a>
        </li>
        <li class="sidebar-header">
            Reports
        </li>

        <li class="sidebar-item {{ request()->routeis('Admin.HRAdmin.report.training')?'active':''}}">
            <a class="sidebar-link" href="{{route('Admin.HRAdmin.report.training')}}">
                <i class="align-middle" data-feather="user"></i> <span class="align-middle">Training Report</span>
            </a>
        </li>
        <li class="sidebar-header">
            Notifications  @if(isset($totalPending) && $totalPending > 0)
            <span class="indicator">{{ $totalPending }}</span>  {{-- Show total pending notifications --}}
        @endif
        </li>
        <li class="sidebar-item {{ request()->routeis('Admin.HRAdmin.notifications.Detail')?'active':''}}">
            <a class="sidebar-link" href="{{route('Admin.HRAdmin.notifications.Detail')}}">
                <i class="align-middle" data-feather="bell"></i> <span class="align-middle">Notifications</span>
            </a>
        </li>
    </ul>
</div>
