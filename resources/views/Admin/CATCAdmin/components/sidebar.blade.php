<div class="sidebar-content js-simplebar">
    <img src="{{asset('image/AA_LOGO_White.png')}}" alt="logo">

    <ul class="sidebar-nav">
        <li class="sidebar-header">
            Pages
        </li>

        <li class="sidebar-item {{ request()->routeIs('Admin.CATCAdmin.page.dashboard')?'active':''}}">
            <a class="sidebar-link" href="{{route('Admin.CATCAdmin.page.dashboard')}}">
                <i class="align-middle" data-feather="sliders"></i> <span class="align-middle">Dashboard</span>
            </a>
        </li>

        <li class="sidebar-item {{ request()->routeis('Admin.CATCAdmin.training.Detail')?'active':''}}">
            <a class="sidebar-link" href="{{route('Admin.CATCAdmin.training.Detail')}}">
                <i class="align-middle" data-feather="user"></i> <span class="align-middle">Manage Training</span>
            </a>
        </li>
        <li class="sidebar-header">
            Reports
        </li>

        <li class="sidebar-item {{ request()->routeis('Admin.CATCAdmin.report.training')?'active':''}}">
            <a class="sidebar-link" href="{{route('Admin.CATCAdmin.report.training')}}">
                <i class="align-middle" data-feather="user"></i> <span class="align-middle">Training Report</span>
            </a>
        </li>
    </ul>
</div>
