<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Image\Enums\Fit;

class Post extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    use InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'content',
        'slug',
        'status',
        'author_id',
        'date',
    ];

    protected $appends = [
        'featured_image',
        'author_data',
        'category',
        'tags',
    ];

    protected $hidden = [
        'media'
    ];

    //relasi author
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    // Relasi many-to-many dengan Term
    public function terms()
    {
        return $this->belongsToMany(Term::class, 'post_term', 'post_id', 'term_id');
    }

    // Accessor untuk author_data
    public function getAuthorDataAttribute()
    {
        return $this->author;
    }

    // Accessor untuk featured_image
    public function getFeaturedImageAttribute()
    {

        $media = $this->getFirstMedia('featured_image');

        if (! $media) {
            return [
                'full'      => null,
                'thumbnail' => null,
                'medium'    => null,
                'default'   => asset('assets/images/default-featured_image.jpg')
            ];
        }

        return [
            'full'      => $media->getFullUrl(), // URL gambar asli
            'thumbnail' => $media->getFullUrl('thumbnail'), // URL versi thumbnail
            'medium'    => $media->getFullUrl('medium'),
            'default'   => asset('assets/images/default-featured_image.jpg')
        ];
    }

    // Accessor untuk category
    public function getCategoryAttribute()
    {
        $terms = $this->terms()->where('taxonomy', 'category')->get();
        return $terms;
    }

    // Accessor untuk tags
    public function getTagsAttribute()
    {
        $terms = $this->terms()->where('taxonomy', 'tag')->get();
        return $terms;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('featured_image');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        // Thumbnail 150x150 seperti WordPress
        $this->addMediaConversion('thumbnail')
            ->fit(Fit::Contain, 150, 150)
            ->nonQueued();

        // Medium 300x300 seperti WordPress
        $this->addMediaConversion('medium')
            ->fit(Fit::Contain, 300, 300)
            ->nonQueued();
    }

    //boot
    public static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            $post->slug = Str::slug($post->title) . '-' . Str::random(5);
        });

        static::updating(function ($post) {
            //jika title berubah
            if ($post->isDirty('title')) {
                $post->slug = Str::slug($post->title) . '-' . Str::random(5);
            }
        });

        static::deleting(function ($post) {
            $post->clearMediaCollection('featured_image'); // hapus media dari koleksi 'featured_image'
        });
    }
}
