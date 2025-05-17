<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Post;
use App\Models\Term;
use App\Models\User;

class PostsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $count = $request->input('count', 20);
        $title_search = $request->input('title');

        $Posts = Post::with('author:id,name,avatar')
            ->when($title_search, function ($query) use ($title_search) {
                $query->where('title', 'like', '%' . $title_search . '%');
            })
            ->orderBy('date', 'desc')
            ->paginate($count);

        return response()->json($Posts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 🔐 Cek apakah user punya permission 'create-post'
        $user = auth()->user();
        if (! $user->can('create-post')) {
            return response()->json([
                'message' => 'You do not have permission.',
            ], 422);
        }

        $request->validate([
            'title'     => 'required|min:4|string',
            'content'   => 'required|min:4',
            'date'      => 'nullable|date',
            'featured_image'    => 'nullable|image|mimes:jpeg,png,webp,jpg,gif,svg|max:2048',
            'status'    => 'required|string',
        ]);

        //if date is null, set date to now
        if (!$request->input('date')) {
            $date = now();
        } else {
            $date = $request->input('date');
        }

        //create post
        $post = Post::create([
            'title'     => $request->title,
            'content'   => $request->content,
            'date'      => $date,
            'status'    => $request->status,
        ]);

        //author
        $post->author()->associate(auth()->user());
        $post->save();

        if ($request->hasFile('featured_image') && $request->file('featured_image')->isValid()) {

            // Tambahkan media baru ke koleksi 'featured'
            $post->addMedia($request->file('featured_image'))
                ->toMediaCollection('featured_image');
        }

        return response()->json($post);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //get post
        $post = Post::with('author:id,name,avatar')
            ->where('id', $id)
            ->first();

        return response()->json($post);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = auth()->user();

        // 🔐 Cek apakah user punya permission 'edit-post'
        if (! $user->can('edit-post')) {
            return response()->json([
                'message' => 'You do not have permission.',
            ], 422);
        }

        $request->validate([
            'title'     => 'required|min:4|string',
            'content'   => 'required|min:4',
            'date'      => 'nullable|date',
            'status'    => 'required|string',
            'category'  => 'nullable|string',
            'tags'      => 'nullable|string',
            'featured_image'    => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,svg|max:2048',
        ]);

        //if date is null, set date to now
        if (!$request->input('date')) {
            $date = now();
        } else {
            $date = $request->input('date');
        }

        //update post
        $post = Post::find($id);
        $post->update([
            'title'     => $request->title,
            'content'   => $request->content,
            'date'      => $date,
            'status'    => $request->status,
        ]);

        if ($request->hasFile('featured_image') && $request->file('featured_image')->isValid()) {

            // Hapus media lama (jika hanya ingin 1 gambar per post)
            $post->clearMediaCollection('featured_image');

            // Tambahkan media baru ke koleksi 'featured'
            $post->addMedia($request->file('featured_image'))
                ->toMediaCollection('featured_image');
        }

        //category
        $post->terms()->detach();

        //tags
        if ($request->tags) {
            // Pisahkan string tags menjadi array
            $tagNames = array_map('trim', explode(',', $request->tags));

            // Array untuk menyimpan ID term
            $termIds = [];

            // Loop untuk mencari atau membuat term
            foreach ($tagNames as $tagName) {
                if (!empty($tagName)) { // Pastikan nama tag tidak kosong

                    $term = Term::firstOrCreate(
                        ['name'     => $tagName], // Cari term berdasarkan nama
                        ['taxonomy' => 'tag'] // Jika tidak ada, buat baru dengan taxonomy 'tags'
                    );

                    // Simpan ID term ke array
                    $termIds[] = $term->id;
                }
            }

            // Sinkronisasi relasi antara post dan term
            $post->terms()->sync($termIds);
        }

        if ($request->category) {
            $category = explode(',', $request->category);
            $post->terms()->attach($category);
        }


        return response()->json($post);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        // 🔐 Cek apakah user punya permission 'edit-post'
        $user = auth()->user();
        if (! $user->can('delete-post')) {
            return response()->json([
                'message' => 'You do not have permission.',
            ], 422);
        }

        //get post
        $post = Post::find($id);

        //delete post
        $post->delete();
    }
}
