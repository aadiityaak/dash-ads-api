<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Term extends Model
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'taxonomy',
    ];

    protected $hidden = ['created_at', 'updated_at', 'pivot'];

    // Relasi many-to-many dengan Post
    public function posts()
    {
        return $this->belongsToMany(Post::class, 'post_term', 'term_id', 'post_id');
    }

    //boot
    public static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            $post->slug = Str::slug($post->taxonomy . '-' . $post->name);
        });

        static::updating(function ($post) {
            //jika name berubah
            if ($post->isDirty('name')) {
                $post->slug = Str::slug($post->taxonomy . '-' . $post->name);
            }
        });
    }
}
