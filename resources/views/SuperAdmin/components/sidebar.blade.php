<div class="sidebar-content js-simplebar" style="background-color: #6482ADDE;">
    <img class="m-3" src="{{asset('image/AASL.png')}}" alt="logo">
    <h4 class="ms-3" style="color: #cccccc;">Airport & Aviation Services (Srilanka)(Private) Limited</h4>

    <ul class="sidebar-nav">
        <li class="sidebar-header">
            Pages
        </li>

        <li class="sidebar-item {{ request()->routeIs('SuperAdmin.page.dashboard') ? 'active' : '' }}">
            <a class="sidebar-link" href="{{ route('SuperAdmin.page.dashboard') }}"
               style="background-color: {{ request()->routeIs('SuperAdmin.page.dashboard') ? '#ff000080' : '#ffffff00' }}; color: {{ request()->routeIs('SuperAdmin.page.dashboard') ? '#cccccc' : '#cccccc' }};">
                <i class="align-middle" data-feather="sliders"></i> <span class="align-middle">Dashboard</span>
            </a>
        </li>

        <li class="sidebar-item {{ request()->routeis('SuperAdmin.Users.Details')?'active':''}}">
            <a class="sidebar-link" href="{{route('SuperAdmin.Users.Details')}}"
                style="background-color: {{ request()->routeIs('SuperAdmin.Users.Details') ? '#ff000080' : '#ffffff00' }}; color: {{ request()->routeIs('SuperAdmin.Users.Details') ? '#cccccc' : '#cccccc' }};">
                <i class="align-middle" data-feather="user"></i> <span class="align-middle">Manage User</span>
            </a>
        </li>

        <li class="sidebar-item {{ request()->routeis('SuperAdmin.training.Detail')?'active':''}}">
            <a class="sidebar-link" href="{{route('SuperAdmin.training.Detail')}}"
                style="background-color: {{ request()->routeIs('SuperAdmin.training.Detail') ? '#ff000080' : '#ffffff00' }}; color: {{ request()->routeIs('SuperAdmin.training.Detail') ? '#cccccc' : '#cccccc' }};">
                <i class="align-middle" data-feather="user"></i> <span class="align-middle">Manage Training</span>
            </a>
        </li>

        <li class="sidebar-item {{ request()->routeis('SuperAdmin.budget.Detail')?'active':''}}">
            <a class="sidebar-link" href="{{route('SuperAdmin.budget.Detail')}}"
            style="background-color: {{ request()->routeIs('SuperAdmin.budget.Detail') ? '#ff000080' : '#ffffff00' }}; color: {{ request()->routeIs('SuperAdmin.budget.Detail') ? '#cccccc' : '#cccccc' }};">
                <i class="align-middle" data-feather="user"></i> <span class="align-middle">Manage Budget</span>
            </a>
        </li>

        <li class="sidebar-item {{ request()->routeis('SuperAdmin.institute.Detail')?'active':''}}">
            <a class="sidebar-link" href="{{route('SuperAdmin.institute.Detail')}}"
            style="background-color: {{ request()->routeIs('SuperAdmin.institute.Detail') ? '#ff000080' : '#ffffff00' }}; color: {{ request()->routeIs('SuperAdmin.institute.Detail') ? '#cccccc' : '#cccccc' }};">
                <i class="align-middle" data-feather="user"></i> <span class="align-middle">Manage Institute</span>
            </a>
        </li>
        <li class="sidebar-item {{ request()->routeis('SuperAdmin.approvel.Detail')?'active':''}}">
            <a class="sidebar-link" href="{{route('SuperAdmin.approvel.Detail')}}"
            style="background-color: {{ request()->routeIs('SuperAdmin.approvel.Detail') ? '#ff000080' : '#ffffff00' }}; color: {{ request()->routeIs('SuperAdmin.approvel.Detail') ? '#cccccc' : '#cccccc' }};">
                <i class="align-middle" data-feather="user"></i> <span class="align-middle">Manage Approvel</span>
            </a>
        </li>
        <li class="sidebar-header">
            Reports
        </li>

        <li class="sidebar-item {{ request()->routeis('SuperAdmin.report.trainingSummary')?'active':''}}">
            <a class="sidebar-link" href="{{route('SuperAdmin.report.trainingSummary')}}"
            style="background-color: {{ request()->routeIs('SuperAdmin.report.trainingSummary') ? '#ff000080' : '#ffffff00' }}; color: {{ request()->routeIs('SuperAdmin.report.trainingSummary') ? '#cccccc' : '#cccccc' }};">
                <i class="align-middle" data-feather="user"></i> <span class="align-middle">Manage Approvel</span>
            </a>
        </li>
    </ul>
</div>
