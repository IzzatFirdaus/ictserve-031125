<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Livewire\SubmissionDetail;
use Illuminate\View\View;

class SubmissionDetailController extends Controller
{
    public function show(int $ticket): View
    {
        return view('livewire.portal.submission-detail', [
            'component' => SubmissionDetail::class,
            'ticket' => $ticket,
        ]);
    }
}
