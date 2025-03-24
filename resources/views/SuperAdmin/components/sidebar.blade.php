<div class="sidebar-content js-simplebar">
    <img src="{{ asset('image/AA_LOGO_White.png') }}" alt="logo" style="width: 200px; height: auto; display: block; margin: auto;">



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
                <i class="align-middle" data-feather="book"></i> <span class="align-middle">Manage Training</span>
            </a>
        </li>

        <li class="sidebar-item {{ request()->routeis('SuperAdmin.budget.Detail')?'active':''}}">
            <a class="sidebar-link" href="{{route('SuperAdmin.budget.Detail')}}">
                <i class="align-middle" data-feather="dollar-sign"></i> <span class="align-middle">Manage Budget</span>
            </a>
        </li>

        <li class="sidebar-item {{ request()->routeis('SuperAdmin.institute.Detail')?'active':''}}">
            <a class="sidebar-link" href="{{route('SuperAdmin.institute.Detail')}}">
                <i class="align-middle" data-feather="book-open"></i> <span class="align-middle">Manage Institute</span>
            </a>
        </li>
        <li class="sidebar-item {{ request()->routeis('SuperAdmin.approval.Detail')?'active':''}}">
            <a class="sidebar-link" href="{{route('SuperAdmin.approval.Detail')}}">
                <i class="align-middle" data-feather="send"></i> <span class="align-middle">Manage Approvel</span>
            </a>
        </li>
        <li class="sidebar-header">
            Reports
        </li>

        <li class="sidebar-item {{ request()->routeis('SuperAdmin.report.trainingSummary')?'active':''}}">
            <a class="sidebar-link" href="{{route('SuperAdmin.report.trainingSummary')}}">
                <i class="align-middle" data-feather="file-text"></i> <span class="align-middle">Training Report</span>
            </a>
        </li>

        <li class="sidebar-item {{ request()->routeis('SuperAdmin.report.IndividualEmployeeTrainingRecordReport')?'active':''}}">
            <a class="sidebar-link" href="{{route('SuperAdmin.report.IndividualEmployeeTrainingRecordReport')}}">
                <i class="align-middle" data-feather="file-text"></i> <span class="align-middle">Individual Employee Training Record</span>
            </a>
        </li>

        <li class="sidebar-item {{ request()->routeis('SuperAdmin.report.ParticularCourseCompletedSummery')?'active':''}}">
            <a class="sidebar-link" href="{{route('SuperAdmin.report.ParticularCourseCompletedSummery')}}">
                <i class="align-middle" data-feather="file-text"></i> <span class="align-middle">Particular Course Completed Summery</span>
            </a>
        </li>

        <li class="sidebar-item {{ request()->routeis('SuperAdmin.report.TrainingFullSummery')?'active':''}}">
            <a class="sidebar-link" href="{{route('SuperAdmin.report.TrainingFullSummery')}}">
                <i class="align-middle" data-feather="file-text"></i> <span class="align-middle">Training Full Summery</span>
            </a>
        </li>
        <li class="sidebar-item{{request()->routeis('SuperAdmin.report.TrainingCustodianWiseSummery')?'active':''}}">
            <a href="{{route('SuperAdmin.report.TrainingCustodianWiseSummery')}}" class="sidebar-link">
                <i class="align-middle" data-feather="file-text"></i> <span class="align-middle">Training Custodian-Wise Summery</span>
            </a>
        </li>
        <li class="sidebar-item{{request()->routeis('SuperAdmin.report.DesignationWiseSummery')?'active':''}}">
            <a href="{{route('SuperAdmin.report.DesignationWiseSummery')}}" class="sidebar-link">
                <i class="align-middle" data-feather="file-text"></i> <span class="align-middle">Designation Wise Summery</span>
            </a>
        </li>
        <li class="sidebar-item{{request()->routeis('SuperAdmin.report.CourseCode-wise_summary')?'active':''}}">
            <a href="{{route('SuperAdmin.report.CourseCode-wise_summary')}}" class="sidebar-link">
                <i class="align-middle" data-feather="file-text"></i> <span class="align-middle">Course Code Wise Summery</span>
            </a>
        </li>
        <li class="sidebar-item{{request()->routeis('SuperAdmin.report.ListOfAbsenteesReport')?'active':''}}">
            <a href="{{route('SuperAdmin.report.ListOfAbsenteesReport')}}" class="sidebar-link">
                <i class="align-middle" data-feather="file-text"></i> <span class="align-middle">List Of Absentees</span>
            </a>
        </li>
        <li class="sidebar-item{{request()->routeis('SuperAdmin.report.TrainingsRequiredtobeRenewed_Recurrent')?'active':''}}">
            <a href="{{route('SuperAdmin.report.TrainingsRequiredtobeRenewed_Recurrent')}}" class="sidebar-link">
                <i class="align-middle" data-feather="file-text"></i> <span class="align-middle">Training Required to be Renewed Recurrent</span>
            </a>
        </li>
        <li class="sidebar-item {{ request()->routeis('SuperAdmin.report.BONDSummary')?'active':''}}">
            <a class="sidebar-link" href="{{route('SuperAdmin.report.BONDSummary')}}">
                <i class="align-middle" data-feather="file-text"></i> <span class="align-middle">BOND Report</span>
            </a>
        </li>
        <li class="sidebar-item {{request()->routeis('SuperAdmin.report.BudgetSummery')?'active':''}}">
            <a href="{{route('SuperAdmin.report.BudgetSummery')}}" class="sidebar-link">
                <i class="align-middle" data-feather="file-text"></i> <span class="align-middle">Budget Report</span>
            </a>
        </li>
    </ul>
</div>
