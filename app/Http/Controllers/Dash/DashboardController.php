<?php

namespace App\Http\Controllers\Dash;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function datatable()
    {
        $Posts = Post::with('author:id,name,avatar')
            ->orderBy('date', 'desc')
            ->paginate(8);
        return response()->json($Posts);
    }
}
