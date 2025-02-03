<div class="sidebar-content js-simplebar">
    <img src="{{asset('image/log.png')}}" alt="logo">

    <ul class="sidebar-nav">
        <li class="sidebar-header">
            Pages
        </li>

        <li class="sidebar-item {{ request()->routeIs('SuperAdmin.page.dashboard')?'active':''}}">
            <a class="sidebar-link" href="{{route('SuperAdmin.page.dashboard')}}">
                <i class="align-middle" data-feather="sliders"></i> <span class="align-middle">Dashboard</span>
            </a>
        </li>

        <li class="sidebar-item {{ request()->routeis('SuperAdmin.Users.Details')?'active':''}}">
            <a class="sidebar-link" href="{{route('SuperAdmin.Users.Details')}}">
                <i class="align-middle" data-feather="user"></i> <span class="align-middle">Manage User</span>
            </a>
        </li>

        <li class="sidebar-item {{ request()->routeis('SuperAdmin.training.Detail')?'active':''}}">
            <a class="sidebar-link" href="{{route('SuperAdmin.training.Detail')}}">
                <i class="align-middle" data-feather="user"></i> <span class="align-middle">Manage Training</span>
            </a>
        </li>

        <li class="sidebar-item {{ request()->routeis('SuperAdmin.budget.Detail')?'active':''}}">
            <a class="sidebar-link" href="{{route('SuperAdmin.budget.Detail')}}">
                <i class="align-middle" data-feather="user"></i> <span class="align-middle">Manage Budget</span>
            </a>
        </li>

        <li class="sidebar-item {{ request()->routeis('SuperAdmin.institute.Detail')?'active':''}}">
            <a class="sidebar-link" href="{{route('SuperAdmin.institute.Detail')}}">
                <i class="align-middle" data-feather="user"></i> <span class="align-middle">Manage Institute</span>
            </a>
        </li>
        <li class="sidebar-item {{ request()->routeis('SuperAdmin.approvel.Detail')?'active':''}}">
            <a class="sidebar-link" href="{{route('SuperAdmin.approvel.Detail')}}">
                <i class="align-middle" data-feather="user"></i> <span class="align-middle">Manage Approvel</span>
            </a>
        </li>
        <li class="sidebar-header">
            Reports
        </li>

        <li class="sidebar-item {{ request()->routeis('SuperAdmin.report.trainingSummary')?'active':''}}">
            <a class="sidebar-link" href="{{route('SuperAdmin.report.trainingSummary')}}">
                <i class="align-middle" data-feather="user"></i> <span class="align-middle">Manage Approvel</span>
            </a>
        </li>
    </ul>
</div>
