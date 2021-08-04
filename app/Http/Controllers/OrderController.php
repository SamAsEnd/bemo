<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Column;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function column(Column $column, $direction)
    {
        $this->safeGuard($column, $direction, ['left', 'right']);

        [$operator, $aggregator] = $this->opretorsBasedOnDirection($direction);

        $query = fn() => auth()->user()->columns(); // micro optimization 🤷🏾‍

        $beyondCount = $this->countOrdersBeyondCurrent($query(), $column->order, $operator);

        // If there is nothing beyond that order, bail
        abort_if($beyondCount === 0, 422, 'Can not move '.$direction);

        $nextStepOrder = $this->getOrderBeyondCurrent($query(), $operator, $column->order, $aggregator);

        if ($beyondCount >= 2) {
            $consecutiveStepOrderBeyond = $this->getOrderBeyondCurrent($query(), $operator, $nextStepOrder, $aggregator);
        } else {
            $consecutiveStepOrderBeyond = $direction === 'left' ? $nextStepOrder - 10 : $nextStepOrder + 10;
        }

        $column->order = ($nextStepOrder + $consecutiveStepOrderBeyond) / 2.0;
        $column->save();

        $this->normalizeColumnOrder();

        return response()->noContent();
    }

    /**
     * Check whether the user is the owner of the column
     * Also checks the direction is valid from the given allowed array
     *
     * @param  \App\Models\Column  $column
     * @param  string  $direction
     * @param  array  $allowed
     */
    private function safeGuard(Column $column, string $direction, array $allowed): void
    {
        abort_unless($column->user_id === auth()->id(), 401, 'Unauthorized');
        abort_unless(in_array($direction, $allowed), 422, 'Invalid Direction');
    }

    /**
     * Map left and up to less than and max
     * Map right and down to greater than and min
     *
     * That's how I find the next order
     *
     * @param $direction
     *
     * @return string[]
     */
    private function opretorsBasedOnDirection($direction): array
    {
        return match ($direction) {
            'left', 'up' => ['<', 'max'],
            'right', 'down' => ['>', 'min'],
        };
    }

    /**
     * Count those beyond the current order
     *      -> if dir is left/up means *below*
     *      -> if dir is right/down means *above*
     *
     * @param $query
     * @param $order
     * @param  string  $operator
     *
     * @return int
     */
    private function countOrdersBeyondCurrent($query, $order, string $operator): int
    {
        return $query->where('order', $operator, $order)->count();
    }

    /**
     * Similar to above method but instread of counting
     *      -> returns min if right/down
     *      -> returns max if left/up
     *
     * @param $query
     * @param  string  $aggregator
     * @param  string  $operator
     * @param $order
     *
     * @return mixed
     */
    private function getOrderBeyondCurrent($query, string $operator, $order, string $aggregator)
    {
        return $query->where('order', $operator, $order)->{$aggregator}('order');
    }

    /**
     * reassign order based on @rownumber
     *
     * !!! THIS IS ONLY BEEN TESTED ON MariaDB
     */
    private function normalizeColumnOrder(): void
    {
        DB::table('columns')
            ->whereRaw('user_id = ((@rownum := 0) + ?)', auth()->id())
            ->orderBy('order')
            ->update([
                'order' => DB::raw('(@rownum := 10 + @rownum)'),
            ]);
    }

    public function card(Column $column, Card $card, $direction)
    {
        $this->safeGuard($column, $direction, ['left', 'right', 'up', 'down']);

        [$operator, $aggregator] = $this->opretorsBasedOnDirection($direction);

        $topQuery = fn() => auth()->user()->columns();
        $query = fn() => Card::query()->where('column_id', $card->column_id);

        if (in_array($direction, ['left', 'right'])) {
            $beyondCount = $this->countOrdersBeyondCurrent($topQuery(), $column->order, $operator);
            // If nothing beyond current, bail
            abort_if($beyondCount === 0, 422, 'Can not move '.$direction);
            $nextStepOrder = $this->getOrderBeyondCurrent($topQuery(), $operator, $column->order, $aggregator);

            $this->cardHorizontalMovement($nextStepOrder, $card);
        } else { // up | down
            $beyondCount = $this->countOrdersBeyondCurrent($query(), $card->order, $operator);
            // If nothing beyond current, bail
            abort_if($beyondCount === 0, 422, 'Can not move '.$direction);
            $nextStepOrder = $this->getOrderBeyondCurrent($query(), $operator, $card->order, $aggregator);

            if ($beyondCount >= 2) {
                $consecutiveStepOrderBeyond = $this->getOrderBeyondCurrent($query(), $operator, $nextStepOrder, $aggregator);
            } else {
                $consecutiveStepOrderBeyond = $direction === 'up' ? $nextStepOrder - 10 : $nextStepOrder + 10;
            }

            $card->order = ($nextStepOrder + $consecutiveStepOrderBeyond) / 2.0;
            $card->save();

            $this->normalizeCardOrder($column);
        }
    }

    private function cardHorizontalMovement(mixed $nextStepOrder, Card $card): void
    {
        /** @var Column $theNewColumn */
        $theNewColumn = Column::query()->where('order', $nextStepOrder)->first();

        $card->column_id = $theNewColumn->id;
        $card->order = $theNewColumn->cards()->max('order') + 10; // add it below everything
        $card->save();
    }

    /**
     * reassign order based on @rownumber
     *
     * !!! THIS IS ONLY BEEN TESTED ON MariaDB
     */
    private function normalizeCardOrder(Column $column): void
    {
        DB::table('cards')
            ->whereRaw('column_id = ((@rownum := 0) + ?)', $column->id)
            ->orderBy('order')
            ->update([
                'order' => DB::raw('(@rownum := 10 + @rownum)'),
            ]);
    }

    /** Set arbitrary order from the request */
    public function setColumn(Column $column)
    {
        $this->safeGuard($column, 'pass', ['pass']);

        $column->order = request('order');
        $column->save();

        $this->normalizeColumnOrder();

        return response()->noContent();
    }

    /** Set arbitrary order from the request */
    public function setCard(Column $column, Card $card)
    {
        $this->safeGuard($column, 'pass', ['pass']);

        $card->order = request('order');

        if (request()->has('column')) {
            $card->column_id = request('column');
        }

        $card->save();

        $this->normalizeCardOrder($card->column);

        return response()->noContent();
    }
}
