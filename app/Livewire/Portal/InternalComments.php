<?php

declare(strict_types=1);

namespace App\Livewire\Portal;

use App\Models\HelpdeskTicket;
use Livewire\Component;

class InternalComments extends Component
{
    public HelpdeskTicket $ticket;
    public string $comment = '';

    public function mount(HelpdeskTicket $ticket): void
    {
        $this->ticket = $ticket;
    }

    public function addComment(): void
    {
        $this->validate(['comment' => 'required|string|min:3']);

        $this->ticket->comments()->create([
            'user_id' => auth()->id(),
            'comment' => $this->comment,
            'is_internal' => true,
        ]);

        $this->comment = '';
        $this->dispatch('comment-added');
    }

    public function render()
    {
        return view('livewire.portal.internal-comments', [
            'comments' => $this->ticket->comments()->where('is_internal', true)->latest()->get(),
        ]);
    }
}
