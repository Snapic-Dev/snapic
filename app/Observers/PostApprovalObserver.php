<?php

namespace App\Observers;

use App\Model\Post;
use App\Providers\EmailsServiceProvider;
use App\User;
use Illuminate\Support\Facades\App;

class PostApprovalObserver
{
    /**
     * Listen to the User updating event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function saving(Post $post) {}

    public function updating(Post $post)
    {
        if ($post->getOriginal('status') !== $post->status) {
            // Sending out the user notification
            $user = User::find($post->user_id);
            try {
                App::setLocale($user->settings['locale']);
            } catch (\Exception $e) {
                App::setLocale('pt');
            }
            EmailsServiceProvider::sendGenericEmail(
                [
                    'email' => $user->email,
                    'subject' => __("Status do post atualizado"),
                    'title' => __('Olá, :name,', ['name' => $user->name]),
                    'content' => __('Seu post foi :status.', ['status' => Post::getStatusName($post->status)]),
                    'button' => [
                        'text' => __('Ver post'),
                        'url' => route('posts.get', ['post_id' => $post->id, 'username' => $user->username]),
                    ]
                ]
            );
        }
    }
}
