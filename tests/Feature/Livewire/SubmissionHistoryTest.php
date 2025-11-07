<?php

namespace Tests\Feature\Livewire;

use App\Livewire\SubmissionHistory;
use Livewire\Livewire;
use Tests\TestCase;

class SubmissionHistoryTest extends TestCase
{
    public function test_renders_successfully()
    {
        Livewire::test(SubmissionHistory::class)
            ->assertStatus(200);
    }
}
