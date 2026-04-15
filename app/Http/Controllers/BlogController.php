<?php

namespace App\Http\Controllers;

use App\Services\WordPressService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function __construct(private WordPressService $wordpress) {}

    public function index(Request $request): View
    {
        $page   = (int) $request->get('page', 1);
        $result = $this->wordpress->getPosts(12, $page);

        return view('blog.index', [
            'posts'      => $result['posts'],
            'totalPosts' => $result['total'],
            'totalPages' => $result['pages'],
            'currentPage'=> $page,
            'wordpress'  => $this->wordpress,
        ]);
    }

    public function show(string $slug): View
    {
        $post = $this->wordpress->getPost($slug);

        if (!$post) {
            abort(404);
        }

        $related = $this->wordpress->getPosts(3, 1);

        return view('blog.show', [
            'post'     => $post,
            'related'  => collect($related['posts'])
                            ->filter(fn($p) => $p['slug'] !== $slug)
                            ->take(2)
                            ->values()
                            ->toArray(),
            'wordpress' => $this->wordpress,
        ]);
    }
}
