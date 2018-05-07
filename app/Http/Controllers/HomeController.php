<?php

namespace App\Http\Controllers;

use App\Content\Posts;
use App\ViewComponents\Pages\Home;
use App\ViewComponents\TestPage;
use Illuminate\Routing\Controller;
use Spatie\Sheets\Sheets;

class HomeController
{
    public function index(Sheets $sheets)
    {
        $posts = $sheets->collection('posts')->all()
            ->sortByDesc(function ($post) {
                return $post->date;
            })
            ->take(5);

        return new Home($posts);
    }
}
