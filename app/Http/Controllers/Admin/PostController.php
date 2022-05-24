<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\User;
use App\Category;
use App\Post;
use App\Tag;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public $validators = [
        'title'     => 'required|max:100',
        'content'   => 'required'
    ];

    private function getValidators($model)
    {
        return [
            'title'     => 'required|max:100',
            'slug' => [
                'required',
                Rule::unique('posts')->ignore($model),
                'max:100'
            ],
            'category_id'   => 'required|exists:App\Category,id',
            'content'       => 'required',
            'tags'          => 'exists:App\Tag,id'
        ];
    }

    public function myindex(Request $request)
    {
        $posts = Post::where('user_id', Auth::user()->id)->paginate(50);
        $categories = Category::all();
        $users = User::all();
        return view('admin.posts.index', [
            'posts'         => $posts,
            'categories'    => $categories,
            'users'         => $users,
            'request'       => $request
        ]);
    }

    public function index(Request $request)
    {

        $posts = Post::where('id', '>', 0);

        if ($request->s) {
            $posts->where(function ($query) use ($request) {
                $query->where('title', 'LIKE', "%$request->s%")
                    ->orWhere('content', 'LIKE', "%$request->s%");
            });
        }

        if ($request->category) {
            $posts->where('category_id', $request->category);
        }

        if ($request->author) {
            $posts->where('user_id', $request->author);
        }

        $posts = $posts->paginate(20);
        $categories = Category::all();
        $users = User::all();
        return view('admin.posts.index', [
            'posts'         => $posts,
            'categories'    => $categories,
            'users'         => $users,
            'request'       => $request
        ]);
    }

    public function create()
    {
        $categories = Category::all();
        $tags = Tag::all();
        return view('admin.posts.create', [
            'categories'    => $categories,
            'tags'          => $tags
        ]);
    }

    public function store(Request $request)
    {
        $request->validate($this->getValidators(null));

        $formData = $request->all() + [
            'user_id' => Auth::user()->id
        ];
        $post = Post::create($formData);
        $post->tags()->attach($formData['tags']);

        return redirect()->route('admin.posts.show', $post->slug);
    }

    public function show(Post $post)
    {
        return view('admin.posts.show', compact('post'));
    }

    public function edit(Post $post)
    {
        if (Auth::user()->id !== $post->user_id) abort(403);
        $categories = Category::all();
        $tags = Tag::all();
        return view('admin.posts.edit', [
            'post'          => $post,
            'categories'    => $categories,
            'tags'          => $tags
        ]);
    }

    public function update(Request $request, Post $post)
    {
        if (Auth::user()->id !== $post->user_id) abort(403);

        $request->validate($this->getValidators($post));
        $formData = $request->all();
        $post->update($formData);
        $post->tags()->sync($formData['tags']);

        return redirect()->route('admin.posts.show', $post->slug);
    }

    public function destroy(Post $post)
    {
        if (Auth::user()->id !== $post->user_id) abort(403);
        $post->tags()->detach();
        $post->delete();

        return redirect((url()->previous()));
    }
}
