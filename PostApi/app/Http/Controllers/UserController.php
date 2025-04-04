<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Http;

class UserController extends Controller
{
    // Show the login form

    public function showRegisterForm()
    {
        return view('register');
    }

    // Handle registration request
    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string|min:6',
            'email' => 'required|email',
            'firstName' => 'required|string',
            'lastName' => 'required|string',
        ]);
        $usersResponse = Http::get('https://dummyjson.com/users?limit=208');
        $usersData = $usersResponse->json();
        foreach ($usersData['users'] as $user) {
            if ($user['username'] === $request->username || $user['email'] === $request->email) {
                return redirect()->back()->with('error', 'Username or email is already registered');
            }
        }

        $response = Http::post('https://dummyjson.com/users/add', [
            'username' => $request->username,
            'password' => $request->password,
            'email' => $request->email,
            'firstName' => $request->firstName,
            'lastName' => $request->lastName,
        ]);

        // dd($response->json());

        if ($response->successful()) {
            return redirect()->route('login')->with('message', 'Registration successful');
        }

        return redirect()->back()->with('error', 'Registration failed');
    }

    public function showLoginForm()
    {
        return view('login');
    }

    //  login request
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $response = Http::post('https://dummyjson.com/auth/login', [
            'username' => $request->username,
            'password' => $request->password,
        ]);

        $data = $response->json();
        // dd($data);

        if ($response->successful() && isset($data['accessToken'])) {
            session([
                'user' => [
                    'id' => $data['id'],
                    'username' => $data['username'],
                    'email' => $data['email'],
                    'firstName' => $data['firstName'],
                    'lastName' => $data['lastName'],
                    'token' => $data['accessToken'],
                ]
            ]);
            return redirect()->route('dashboard')->with('message', 'Login successful');
        }
        return redirect()->route('login')->with('error', 'Invalid credentials');
    }

    public function profile()
    {
        if (!session('user')) {
            return redirect()->route('login');
        }

        return view('profile', [
            'user' => session('user'),
        ]);
    }

    // Update profile
    public function updateProfile(Request $request, $userID)
    {
        if (!session('user')) {
            return redirect()->route('login');
        }

        $request->validate([
            'username' => 'required|string',
            'email' => 'required|email',
            'firstName' => 'required|string',
            'lastName' => 'required|string',
        ]);

        $usersResponse = Http::get('https://dummyjson.com/users?limit=208');
        $usersData = $usersResponse->json();

        foreach ($usersData['users'] as $user) {
            if (
                ($user['username'] === $request->username || $user['email'] === $request->email) &&
                $user['id'] != $userID
            ) {
                return redirect()->back()->with('error', 'Username or email is already used');
            }
        }

        $response = Http::put("https://dummyjson.com/users/{$userID}", [
            'username' => $request->username,
            'email' => $request->email,
            'firstName' => $request->firstName,
            'lastName' => $request->lastName,
        ]);

        if ($response->successful()) {
            return redirect()->back()->with('message', 'Profile updated successfully');
        }

        return redirect()->back()->with('error', 'Profile update failed');
    }

    //
    public function dashboard(Request $request)
    {
        // dd($request->all());
        if (!session('user')) {
            return redirect()->route('login');
        }

        // dd($var);

        $searchQuery = $request->input('search');
        $sortBy = $request->input('sortBy');  //
        $order = $request->input('order');
        $page = $request->input('page', 1);
        $limit = 10;  // Pagination limit
        $skip = ($page - 1) * $limit;
        // dd($sortBy,$order);

        if ($searchQuery && $sortBy) {
            $usersResponse = Http::get("https://dummyjson.com/users/search?q={$searchQuery}&sortBy={$sortBy}&order={$order}&limit={$limit}&skip={$skip}");
        } elseif ($searchQuery) {
            $usersResponse = Http::get("https://dummyjson.com/users/search?q={$searchQuery}&limit={$limit}&skip={$skip}");
        } elseif ($sortBy) {
            $usersResponse = Http::get("https://dummyjson.com/users?sortBy={$sortBy}&order={$order}&limit={$limit}&skip={$skip}");
        } else {
            $usersResponse = Http::get("https://dummyjson.com/users?limit={$limit}&skip={$skip}");
        }

        $usersData = $usersResponse->json();
        $users = $usersData['users'];

        // Fetch posts
        $postsResponse = Http::get('https://dummyjson.com/posts?limit=251&skip=0');
        $postsData = $postsResponse->json();
        $posts = $postsData['posts'];

        $postCounts = [];
        foreach ($posts as $post) {
            $userId = $post['userId'];
            if (!isset($postCounts[$userId])) {
                $postCounts[$userId] = 0;
            }
            $postCounts[$userId]++;
        }

        // else {
        //     $error = $usersData['message'] ?? 'Failed to fetch user list';
        // }
        // dd($usersData);
        $totalUsers = $usersData['total'] ?? count($users);
        // dd($totalUsers);
        $usersPaginator = new LengthAwarePaginator(
            $users,
            $totalUsers,
            $limit,
            $page,
            ['path' => route('dashboard')]
        );
        // dd($usersPaginator);
        return view('dashboard', [
            'currentUser' => session('user'),
            'postCounts' => $postCounts,
            'users' => $usersPaginator,
            'searchQuery' => $searchQuery,
        ]);
    }

    // logout
    public function logout(Request $request)
    {
        $request->session()->forget('user');
        $request->session()->flush();

        return redirect()->route('login')->with('message', 'Logged out successfully');
    }

    public function viewPosts($userId)
    {
        $userResponse = Http::get("https://dummyjson.com/users/{$userId}");
        $user = $userResponse->json();

        $postsResponse = Http::get("https://dummyjson.com/posts/user/{$userId}");
        $posts = $postsResponse->successful() ? $postsResponse->json()['posts'] : [];
        // dd($posts);

        // Get comments for each post
        $comments = [];
        if (!empty($posts)) {
            foreach ($posts as $post) {
                $commentsResponse = Http::get("https://dummyjson.com/comments/post/{$post['id']}");
                $comments[$post['id']] = $commentsResponse->successful() ? $commentsResponse->json()['comments'] : [];
            }
        }

        return view('posts', [
            'user' => $user,
            'posts' => $posts,
            'comments' => $comments
        ]);
    }

    public function deletePost($postId)
    {
        $response = Http::delete("https://dummyjson.com/posts/{$postId}");
        // dd($response->json());

        if ($response->successful()) {
            $deletedUPost = $response->json();
            // dd($deletedUser);
            if (isset($deletedUPost['isDeleted']) && $deletedUPost['isDeleted'] === true) {
                return redirect()
                    ->back()
                    ->with('message', 'Post deleted successfully');
            }
        }

        return redirect()->back()->with('error', 'Failed to delete post');
    }

    public function deleteUser($userId)
    {
        // dd($userId);
        $response = Http::delete("https://dummyjson.com/users/{$userId}");
        // dd($response);

        if ($response->successful()) {
            $deletedUser = $response->json();
            // dd($deletedUser);
            if (isset($deletedUser['isDeleted']) && $deletedUser['isDeleted'] === true) {
                return redirect()
                    ->back()
                    ->with('message', 'User deleted successfully');
            }
        }

        return redirect()->back()->with('error', 'Failed to delete user');
    }

    // Show add post form
    public function showAddPostForm()
    {
        $usersResponse = Http::get('https://dummyjson.com/users?limit=208');
        $usersData = $usersResponse->json();

        if ($usersResponse->successful() && isset($usersData['users'])) {
            $users = $usersData['users'];
        } else {
            $users = [];
        }
        // dd($users);

        return view('addpost', [
            'currentUser' => session('user'),
            'users' => $users
        ]);
    }

    // Handle post creat
    public function addPost(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'userId' => 'required|integer',
        ]);

        $response = Http::post('https://dummyjson.com/posts/add', [
            'title' => $request->title,
            'body' => $request->body,
            'userId' => $request->userId,
        ]);

        // dd($response->json());
        if ($response->successful()) {
            return redirect()
                ->route('dashboard')
                ->with('message', 'Post created successfully');
        }

        return redirect()
            ->back()
            ->with('error', 'Failed to create post')
            ->withInput();
    }
}
