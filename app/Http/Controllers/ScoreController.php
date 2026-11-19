<?php

namespace App\Http\Controllers;

use App\Models\Record;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScoreController extends Controller
{
    public function index(): View
    {
        $records = Record::with('user')
            ->get()
            ->sortByDesc('score')
            ->unique('user_id')
            ->take(10)
            ->values();

        return view('records', ['records' => $records]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'score' => ['required', 'integer', 'min:1', 'max:1000000'],
        ]);

        $request->user()->records()->create(['score' => $data['score']]);

        return response()->json([
            'best' => $request->user()->records()->max('score'),
        ]);
    }
}
