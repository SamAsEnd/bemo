<?php

namespace App\Http\Controllers;

use App\Models\Column;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function column(Column $column, $direction)
    {
        abort_unless($column->user_id === auth()->id(), 401, 'Unauthorized');
        abort_unless(in_array($direction, ['left', 'right']), 422, 'Invalid Direction');

        [$operator, $aggregator] = match ($direction) {
            'left' => ['<', 'max'],
            'right' => ['>', 'min'],
        };

        $beyondCount = $this->countColumnsBeyond($column, $operator);

        if ($beyondCount === 0) {
            abort(422, 'Can not move '.$direction);
        }

        $nextStepOrder = $this->nextStepOrder($aggregator, $operator, $column->order);

        if ($beyondCount > 1) {
            $consecutiveStepOrderBeyond = $this->nextStepOrder($aggregator, $operator, $nextStepOrder);
        } else {
            $consecutiveStepOrderBeyond = $direction === 'left' ? $nextStepOrder - 10 : $nextStepOrder + 10;
        }

        $column->order = ($nextStepOrder + $consecutiveStepOrderBeyond) / 2.0;
        $column->save();

        $this->normalizeOrder();

        return response()->noContent();
    }

    private function countColumnsBeyond(Column $column, string $operator)
    {
        return auth()->user()->columns()->where('order', $operator, $column->order)->count();
    }

    private function nextStepOrder(string $aggregator, string $operator, $order)
    {
        return auth()->user()->columns()->where('order', $operator, $order)->{$aggregator}('order');
    }

    private function normalizeOrder(): void
    {
        DB::table('columns')
            ->whereRaw('user_id = ((@rownum := 0) + ?)', auth()->id())
            ->orderBy('order')
            ->update([
                'order' => DB::raw('(@rownum := 10 + @rownum)'),
            ]);
    }
}
