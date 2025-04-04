<!DOCTYPE html>
<html>
<head>
    <title>Add New Post</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .alert { padding: 10px; margin-bottom: 15px; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <span>Add New Post</span>
                        <span class="float-end">
                            <a href="{{ route('dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
                        </span>
                    </div>
                    <div class="card-body">
                        {{-- @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif --}}

                        {{-- @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif --}}

                        <form method="POST" action="{{ route('posts.add') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="userId" class="form-label">Select User</label>
                                <select class="form-control" id="userId" name="userId" required>
                                    <option value="">Select a user</option>
                                    @error('userId')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                    @foreach ($users as $user)
                                        <option value="{{ $user['id'] }}" {{ old('userId') == $user['id'] ? 'selected' : '' }}>
                                            {{ $user['username'] }} ({{ $user['firstName'] }} {{ $user['lastName'] }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" class="form-control" id="title" name="title"
                                       value="{{ old('title') }}" required>
                                       @error('title')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                            </div>

                            <div class="mb-3">
                                <label for="body" class="form-label">Content</label>
                                <textarea class="form-control" id="body" name="body" rows="5" required>{{ old('body') }}</textarea>
                                @error('body')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror

                            </div>



                            <button type="submit" class="btn btn-primary">Create Post</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>