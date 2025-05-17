<?php

namespace App\Observers;

use App\Notifications\PostBaruNotification;
use Illuminate\Support\Facades\Notification;
use App\Models\Post;
use App\Models\User;

class PostObserver
{
    /**
     * Handle the Post "created" event.
     */
    public function created(Post $post): void
    {
        //send notification
        $users = User::all();
        Notification::send($users, new PostBaruNotification($post));
    }

    /**
     * Handle the Post "updated" event.
     */
    public function updated(Post $post): void
    {
        //
    }

    /**
     * Handle the Post "deleted" event.
     */
    public function deleted(Post $post): void
    {
        //
    }

    /**
     * Handle the Post "restored" event.
     */
    public function restored(Post $post): void
    {
        //
    }

    /**
     * Handle the Post "force deleted" event.
     */
    public function forceDeleted(Post $post): void
    {
        //
    }
}
