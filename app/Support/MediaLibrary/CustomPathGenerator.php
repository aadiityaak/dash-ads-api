<?php

namespace App\Support\MediaLibrary;

use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class CustomPathGenerator implements PathGenerator
{
    public function getPath(Media $media): string
    {

        $date = $this->created_at ?? now(); // fallback kalau null
        $year = $date->format('Y');
        $month = $date->format('m');

        return "media/{$year}/{$month}/{$media->id}/";
    }

    public function getPathForConversions(Media $media): string
    {
        return $this->getPath($media);
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getPath($media) . 'responsive-images/';
    }
}
