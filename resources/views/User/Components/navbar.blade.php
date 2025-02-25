<a class="sidebar-toggle js-sidebar-toggle">
    <i class="hamburger align-self-center"></i>
  </a>

<div class="navbar-collapse collapse">
    <ul class="navbar-nav navbar-align">
        <li class="nav-item dropdown">
            <a class="nav-icon dropdown-toggle" href="#" id="alertsDropdown" data-bs-toggle="dropdown">
                <div class="position-relative">
                    <i class="align-middle" data-feather="bell"></i>
                    @if(isset($notifications) && count($notifications) > 0)
                        <span class="indicator">{{ count($notifications) }}</span>
                    @endif
                </div>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end py-0">
                <div class="dropdown-menu-header">
                    @if(isset($notifications) && count($notifications) > 0)
                        {{ count($notifications) }} New Notifications
                    @endif
                </div>
                <div class="list-group">
                    @foreach ($notifications as $notification)
                        <a href="#" class="list-group-item">
                            <div class="row g-0 align-items-center">
                                <div class="col-2">
                                    <i class="text-primary" data-feather="bell"></i>
                                </div>
                                <div class="col-10">
                                    <div class="text-dark">{{ $notification->title }}</div>
                                    <div class="text-muted small mt-1">{{ $notification->message }}</div>
                                    <div class="text-muted small mt-1">{{ $notification->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </li>
        <li class="nav-item dropdown">
        <a class="nav-icon dropdown-toggle d-inline-block d-sm-none" href="#" data-bs-toggle="dropdown">
            <i class="align-middle" data-feather="settings"></i>
        </a>

        <a class="nav-link dropdown-toggle d-none d-sm-inline-block" href="#" data-bs-toggle="dropdown">
            <span class="text-dark">{{Auth::user()->name}}</span>
        </a>
        <div class="dropdown-menu dropdown-menu-end">
            <a class="dropdown-item" href="pages-profile.html"><i class="align-middle me-1" data-feather="user"></i> Profile</a>
            <a class="dropdown-item" href="#"><i class="align-middle me-1" data-feather="pie-chart"></i> Analytics</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="index.html"><i class="align-middle me-1" data-feather="settings"></i> Settings & Privacy</a>
            <a class="dropdown-item" href="#"><i class="align-middle me-1" data-feather="help-circle"></i> Help Center</a>
            <div class="dropdown-divider"></div>
            <div class="dropdown-item">
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" style="border: none; background: transparent; padding: 0;">
                        <i class="align-middle me-1" data-feather="log-out" style="width: 24px; height: 24px;"></i>
                        Log Out
                    </button>
                </form>
            </div>
            </div>
        </li>
    </ul>
</div>