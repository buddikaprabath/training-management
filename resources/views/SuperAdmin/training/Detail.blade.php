@extends('SuperAdmin.index')
@section('content')

<div class="card">
    <div class="m-3 d-flex justify-content-between align-items-center">
        <p class="p-1 m-0">Training Details</p>
        <!-- Search Form -->
        <form class="d-flex" method="GET" action="#">
            <input class="form-control me-2" type="search" name="query" placeholder="Enter training Name" value="{{ request('query') }}">
            <button class="btn btn-outline-success" type="submit">Search</button>
        </form>
        <a href="{{route('SuperAdmin.training.create')}}" style="text-decoration: none">
         <button type="button" class="btn btn-primary d-flex align-items-center">
            <svg xmlns="http://www.w3.org/2000/svg" style="width: 30px; height: 30px; margin-right: 5px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
               <circle cx="12" cy="12" r="10"></circle>
               <line x1="12" y1="8" x2="12" y2="16"></line>
               <line x1="8" y1="12" x2="16" y2="12"></line>
            </svg>
            Create Training
         </button>
        </a>
    </div>
    @if(session('success'))
        <div class="alert alert-success fw-bold text-success ml-5">
            {{ session('success') }}
        </div>
    @elseif(session('error'))
        <div class="alert alert-danger fw-bold text-danger ml-5">
            {{ session('error') }}
        </div>
    @endif
    <div class="card p-2">
        <div class="table-responsive">
          <table class="table">
            <thead>
                <tr>
                    <th>Training Name</th>
                    <th>Mode of Delivery</th>
                    <th>Total Training Hours</th>
                    <th>Total Program Cost</th>
                    <th>Division</th>
                    <th>Batch Size</th>
                    <th>Category</th>
                    <th>Participant</th>
                    <th>Cost break down</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <td>AASL</td>
                <td>Online</td>
                <td>40</td>
                <td>6,500,000</td>
                <td>IT</td>
                <td>25</td>
                <td>Development</td>
                <td>
                    <a href="#">
                        <i class="align-middle me-2" data-feather="eye"></i>
                    </a>
                    <a href="#">
                        <i class="align-middle me-2" data-feather="user-plus"></i>
                    </a>
                </td>
                <td>
                    <a href="#">
                        <i class="align-middle me-2" data-feather="file-plus"></i>
                    </a>
                </td>
                <td>
                    <a href="#">
                        <i class="align-middle me-2" data-feather="edit"></i>
                    </a>
                    <a href="#">
                        <i class="align-middle me-2" data-feather="trash-2"></i>
                    </a>
                </td>

            </tbody>
        </table>
        <nav aria-label="Page navigation example">
            <ul class="pagination d-flex align-items-end flex-column mb-3">
                <!-- This will dynamically generate the pagination links -->

            </ul>
        </nav>

        </div>
    </div>

</div>

@endsection
