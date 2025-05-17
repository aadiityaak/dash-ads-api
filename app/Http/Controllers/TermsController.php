<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Term;

class TermsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        $count = $request->input('count', 20);
        $taxonomy = $request->input('taxonomy');
        $terms = Term::where('taxonomy', $taxonomy)
            ->orderBy('name', 'desc')
            ->paginate($count);

        return response()->json($terms);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 🔐 Cek apakah user punya permission 'edit-term'
        $user = auth()->user();
        if (! $user->can('edit-term')) {
            return response()->json([
                'message' => 'You do not have permission.',
            ], 422);
        }

        $request->validate([
            'name'          => 'required|string',
            'description'   => 'nullable|string',
            'taxonomy'      => 'required|string',
        ]);

        //cek slug apakah sudah ada
        $slug = Str::slug($request->taxonomy . '-' . $request->name);
        $term = Term::where('slug', $slug)->first();
        if ($term) {
            return response()->json([
                'message' => 'Nama term sudah ada.',
                '_data' => [
                    'errors' => 'The slug has already been taken.'
                ]
            ], 422);
        }

        $term = Term::create([
            'name'          => $request->name,
            'description'   => $request->description,
            'taxonomy'      => $request->taxonomy ?? 'category',
        ]);

        return response()->json($term);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $term = Term::find($id);
        return response()->json($term);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        // 🔐 Cek apakah user punya permission 'edit-term'
        $user = auth()->user();
        if (! $user->can('edit-term')) {
            return response()->json([
                'message' => 'You do not have permission.',
            ], 422);
        }

        $request->validate([
            'name'          => 'required|string',
            'description'   => 'nullable|string',
        ]);

        $term = Term::find($id);
        $term->name = $request->name;
        $term->description = $request->description;
        $term->save();

        return response()->json($term);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // 🔐 Cek apakah user punya permission 'edit-term'
        $user = auth()->user();
        if (! $user->can('edit-term')) {
            return response()->json([
                'message' => 'You do not have permission.',
            ], 422);
        }
        //
        $term = Term::find($id);
        $term->delete();
    }
}
