<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\InternalComment;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserMentioned extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public InternalComment $comment,
        public User $mentionedBy
    ) {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('You were mentioned in a comment')
            ->line($this->mentionedBy->name.' mentioned you in a comment.')
            ->line('Comment: '.substr($this->comment->comment, 0, 100))
            ->action('View Comment', url('/portal/tickets/'.$this->comment->commentable_id))
            ->line('Thank you for using ICTServe!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'comment_id' => $this->comment->id,
            'mentioned_by' => $this->mentionedBy->id,
            'mentioned_by_name' => $this->mentionedBy->name,
            'comment' => substr($this->comment->comment, 0, 100),
            'commentable_type' => $this->comment->commentable_type,
            'commentable_id' => $this->comment->commentable_id,
        ];
    }
}
