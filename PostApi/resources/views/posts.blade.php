<!DOCTYPE html>
<html>
<head>
    <title>User Posts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">
                        Posts by : {{ $user['username'] }} ({{ $user['firstName'] }} {{ $user['lastName'] }} - {{ $user['email'] }})
                        <a href="{{ route('dashboard') }}" class="btn btn-secondary float-end">Back to Dashboard</a>
                    </div>
                    <div class="card-body">
                        @if (session('message'))
                            <div class="alert alert-success">{{ session('message') }}</div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        @if (count($posts) > 0)
                            @foreach ($posts as $post)
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h5>{{ $post['title'] }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <p>{{ $post['body'] }}</p>
                                        <div class="mt-2">
                                            <strong>Tags:</strong> {{ implode(', ', $post['tags']) }}<br>
                                            <strong>Likes:</strong> {{ $post['reactions']['likes'] }} |
                                            <strong>Dislikes:</strong> {{ $post['reactions']['dislikes'] }} |
                                            <strong>Views:</strong> {{ $post['views'] }}
                                        </div>

                                        <!-- Comments Section -->
                                        @if (!empty($comments[$post['id']]))
                                            <div class="mt-3">
                                                {{-- @dd(count($comments[$post['id']])) --}}
                                                <h6>Comments: {{ count($comments[$post['id']]) }}</h6>
                                                @foreach ($comments[$post['id']] as $comment)
                                                    <div class="border-top pt-2 mt-2">
                                                        <p class="mb-1">{{ $comment['body'] }}</p>
                                                        <small class="text-muted">
                                                            By: {{ $comment['user']['fullName'] }}  |
                                                            Likes: {{ $comment['likes'] }}
                                                        </small>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="mt-3 text-muted">No comments yet.</p>
                                        @endif
                                    </div>
                                    <div class="card-footer">
                                        <form action="{{ route('posts.delete', $post['id']) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('POST')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Are you sure you want to delete this post?')">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p>No posts found for this user.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>