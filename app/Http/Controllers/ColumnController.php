<?php

namespace App\Http\Controllers;

use App\Http\Requests\ColumnRegisterRequest;
use App\Models\Column;
use Illuminate\Http\Request;

class ColumnController extends Controller
{

    public function index(Request $request)
    {
        return Column::with('cards')
            ->where('user_id', $request->user()->id)
            ->orderBy('order')
            ->get();
    }

    public function store(ColumnRegisterRequest $request)
    {
        $data = $request->validated();
        $data['order'] = $request->user()->columns()->max('order') + 10;

        $column = $request->user()->columns()->create($data);

        return $column->load('cards');
    }

    public function destroy(Column $column)
    {
        abort_unless($column->user_id === auth()->id(), 401);

        $column->delete();

        return response()->noContent();
    }
}
