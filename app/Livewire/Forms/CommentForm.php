<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class CommentForm extends Form
{
    #[Validate('required|string|min:3|max:2000')]
    public string $comment = '';
}
