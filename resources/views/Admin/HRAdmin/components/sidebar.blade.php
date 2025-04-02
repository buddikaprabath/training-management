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
            Notifications  @if(isset($totalPending) && $totalPending > 0)
            <span class="indicator">{{ $totalPending }}</span>  {{-- Show total pending notifications --}}
        @endif
        </li>
        <li class="sidebar-item {{ request()->routeis('Admin.HRAdmin.notifications.Detail')?'active':''}}">
            <a class="sidebar-link" href="{{route('Admin.HRAdmin.notifications.Detail')}}">
                <i class="align-middle" data-feather="bell"></i> <span class="align-middle">Notifications</span>
            </a>
        </li>
        <li class="sidebar-header">
            Reports
        </li>

        <li class="sidebar-item {{ request()->routeis('Admin.HRAdmin.report.trainingSummary')?'active':''}}">
            <a class="sidebar-link" href="{{route('Admin.HRAdmin.report.trainingSummary')}}">
                <i class="align-middle" data-feather="user"></i> <span class="align-middle">Training Report</span>
            </a>
        </li>
        <li class="sidebar-item {{ request()->routeis('Admin.HRAdmin.report.IndividualEmployeeTrainingRecordReport')?'active':''}}">
            <a class="sidebar-link" href="{{route('Admin.HRAdmin.report.IndividualEmployeeTrainingRecordReport')}}">
                <i class="align-middle" data-feather="file-text"></i> <span class="align-middle">Individual Employee Training Record</span>
            </a>
        </li>

        <li class="sidebar-item {{ request()->routeis('Admin.HRAdmin.report.ParticularCourseCompletedSummery')?'active':''}}">
            <a class="sidebar-link" href="{{route('Admin.HRAdmin.report.ParticularCourseCompletedSummery')}}">
                <i class="align-middle" data-feather="file-text"></i> <span class="align-middle">Particular Course Completed Summary</span>
            </a>
        </li>

        <li class="sidebar-item {{ request()->routeis('Admin.HRAdmin.report.TrainingFullSummery')?'active':''}}">
            <a class="sidebar-link" href="{{route('Admin.HRAdmin.report.TrainingFullSummery')}}">
                <i class="align-middle" data-feather="file-text"></i> <span class="align-middle">Training Full Summary</span>
            </a>
        </li>
        <li class="sidebar-item{{request()->routeis('Admin.HRAdmin.report.TrainingCustodianWiseSummery')?'active':''}}">
            <a href="{{route('Admin.HRAdmin.report.TrainingCustodianWiseSummery')}}" class="sidebar-link">
                <i class="align-middle" data-feather="file-text"></i> <span class="align-middle">Training Custodian-Wise Summary</span>
            </a>
        </li>
        <li class="sidebar-item{{request()->routeis('Admin.HRAdmin.report.DesignationWiseSummery')?'active':''}}">
            <a href="{{route('Admin.HRAdmin.report.DesignationWiseSummery')}}" class="sidebar-link">
                <i class="align-middle" data-feather="file-text"></i> <span class="align-middle">Designation Wise Summary</span>
            </a>
        </li>
        <li class="sidebar-item{{request()->routeis('Admin.HRAdmin.report.CourseCode-wise_summary')?'active':''}}">
            <a href="{{route('Admin.HRAdmin.report.CourseCode-wise_summary')}}" class="sidebar-link">
                <i class="align-middle" data-feather="file-text"></i> <span class="align-middle">Course Code Wise Summary</span>
            </a>
        </li>
        <li class="sidebar-item{{request()->routeis('Admin.HRAdmin.report.ListOfAbsenteesReport')?'active':''}}">
            <a href="{{route('Admin.HRAdmin.report.ListOfAbsenteesReport')}}" class="sidebar-link">
                <i class="align-middle" data-feather="file-text"></i> <span class="align-middle">List Of Absentees</span>
            </a>
        </li>
        <li class="sidebar-item{{request()->routeis('Admin.HRAdmin.report.TrainingsRequiredtobeRenewed_Recurrent')?'active':''}}">
            <a href="{{route('Admin.HRAdmin.report.TrainingsRequiredtobeRenewed_Recurrent')}}" class="sidebar-link">
                <i class="align-middle" data-feather="file-text"></i> <span class="align-middle">Training Required to be Renewed Recurrent</span>
            </a>
        </li>
        <li class="sidebar-item {{ request()->routeis('Admin.HRAdmin.report.BONDSummary')?'active':''}}">
            <a class="sidebar-link" href="{{route('Admin.HRAdmin.report.BONDSummary')}}">
                <i class="align-middle" data-feather="file-text"></i> <span class="align-middle">BOND Report</span>
            </a>
        </li>
        <li class="sidebar-item {{request()->routeis('Admin.HRAdmin.report.BudgetSummery')?'active':''}}">
            <a href="{{route('Admin.HRAdmin.report.BudgetSummery')}}" class="sidebar-link">
                <i class="align-middle" data-feather="file-text"></i> <span class="align-middle">Budget Report</span>
            </a>
        </li>
    </ul>
</div>
