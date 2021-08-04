<?php

namespace App\Http\Controllers;

use App\Http\Requests\CardRegisterRequest;
use App\Models\Card;
use App\Models\Column;

class CardController extends Controller
{
    public function store(Column $column, CardRegisterRequest $request)
    {
        abort_unless($column->user_id === auth()->id(), 401);

        return $column->cards()->create($request->validated());
    }

    public function destroy(Column $column, Card $card)
    {
        abort_unless($column->user_id === auth()->id(), 401);

        $card->delete();

        return response()->noContent();
    }
}
