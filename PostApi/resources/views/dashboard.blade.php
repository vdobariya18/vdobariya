<!DOCTYPE html>
<html>

<head>
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
    </style>
</head>

<body>


    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">
                        <a href="/dashboard" class="text-dark">Dashboard</a>
                        <span class="float-end">
                            <!-- Dropdown Menu -->
                            <div class="dropdown">
                                <button class="btn btn-secondary dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ $currentUser['username'] }}
                                </button>
                                {{-- @dd($currentUser) --}}
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                    <li><a class="dropdown-item" href="{{ route('profile',$currentUser['id']) }}">Profile</a></li>
                                    <li><a class="dropdown-item" href="{{ route('logout') }}">Logout</a></li>
                                </ul>
                            </div>
                        </span>
                    </div>
                    <div class="card-body">
                        @if (isset($error))
                            <div class="alert alert-danger">{{ $error }}</div>
                        @endif

                        @if (session('message'))
                            <div class="alert alert-success">{{ session('message') }}</div>
                        @endif

                        <h3>Welcome, {{ $currentUser['username'] }}</h3>
                        <p>This is your dashboard.</p>

                        <!-- Search Form -->
                        <a href="{{ route('posts.add') }}" class="btn btn-success">Add New Post</a>

                        <div class="float-end mb-4">
                            <form method="GET" action="{{ route('dashboard') }}" class="row" id="searchForm">
                                <div class="col-auto">
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Search users..." value="{{ $searchQuery ?? '' }}">
                                    @error('search')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-auto">
                                    <button type="submit" class="btn btn-primary">Search</button>
                                </div>
                                @if ($searchQuery)
                                    <div class="col-auto">
                                        <a href="{{ route('dashboard') }}" class="btn btn-secondary"
                                            id="clearSearch">Clear Search</a>
                                    </div>
                                @endif
                            </form>
                        </div>

                        <div class="mt-4">
                            <h4>User List </h4>

                            @if (count($users) > 0)
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th><a href="/dashboard?search={{ request('search') }}&sortBy=username&order={{ request('order') == 'asc' ? 'desc' : 'asc' }}&page={{ request('page', 1) }}"
                                                    class="sort-link">Username
                                                    @if (request('sortBy') === 'username')
                                                        <i
                                                            class="sort-icon bi bi-arrow-{{ request('order') === 'asc' ? 'up' : 'down' }}"></i>
                                                    @endif
                                                </a></th>
                                            <th><a href="/dashboard?search={{ request('search') }}&sortBy=email&order={{ request('order') == 'asc' ? 'desc' : 'asc' }}&page={{ request('page', 1) }}"
                                                    class="sort-link">Email
                                                    @if (request('sortBy') === 'email')
                                                        <i
                                                            class="sort-icon bi bi-arrow-{{ request('order') === 'asc' ? 'up' : 'down' }}"></i>
                                                    @endif
                                                </a></th>
                                            <th><a href="/dashboard?search={{ request('search') }}&sortBy=firstName&order={{ request('order') == 'asc' ? 'desc' : 'asc' }}&page={{ request('page', 1) }}"
                                                    class="sort-link">First Name
                                                    @if (request('sortBy') === 'firstName')
                                                        <i
                                                            class="sort-icon bi bi-arrow-{{ request('order') === 'asc' ? 'up' : 'down' }}"></i>
                                                    @endif
                                                </a></th>
                                            <th><a href="/dashboard?search={{ request('search') }}&sortBy=lastName&order={{ request('order') == 'asc' ? 'desc' : 'asc' }}&page={{ request('page', 1) }}"
                                                    class="sort-link">Last Name
                                                    @if (request('sortBy') === 'lastName')
                                                        <i
                                                            class="sort-icon bi bi-arrow-{{ request('order') === 'asc' ? 'up' : 'down' }}"></i>
                                                    @endif
                                                </a></th>
                                            <th>Post Count</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($users as $user)
                                            <tr>
                                                <td>{{ $user['username'] }}</td>
                                                <td>{{ $user['email'] }}</td>
                                                <td>{{ $user['firstName'] }}</td>
                                                <td>{{ $user['lastName'] }}</td>
                                                {{-- <td>{{ $user['postCount'] }}</td> --}}
                                                <td>{{ $postCounts[$user['id']] ?? 0 }}</td>
                                                <!-- Display post count -->
                                                <td>
                                                    @if (($postCounts[$user['id']] ?? 0) > 0)
                                                        <a href="{{ route('posts.view', $user['id']) }}"
                                                            class="btn btn-primary btn-sm">View Posts</a>
                                                    @else
                                                        <form action="{{ route('users.delete', $user['id']) }}"
                                                            method="POST" style="display: inline;">
                                                            @csrf
                                                            <button type="submit" class="btn btn-danger btn-sm"
                                                                onclick="return confirm('Are you sure you want to delete this user?')">
                                                                Delete
                                                            </button>
                                                        </form>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <!-- Pagination Links -->
                                <div class="mt-3">
                                    {{ $users->appends(request()->query())->links() }}
                                </div>
                            @else
                                <p>No users available to display.
                                    @if ($searchQuery || $users->lastPage() < $users->currentPage())
                                        <a href="{{ route('dashboard') }}" id="clearSearchLink">Clear</a>.
                                </p>
                            @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>

</html>
