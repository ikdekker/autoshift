<?php

namespace AutoShift\Strategy;

/**
 * Always tries to pick the first person that has less than its predecessor.
 * Could also be called something like "CliffFill" / "EdgeFill"? since it fills
 * from one side.
 * 
 * todo: make animation to show how this would fill up with some attendances.
 */
class SequentialStrategy implements StrategyInterface {

    public function pick(array $statistics) {
        $previous = reset($statistics);
        foreach ($statistics as $user => $pickCount) {
            if ($previous > $pickCount) {
                return $user;
            }
            $previous = $pickCount;
        }
        // All values were the same, no one was picked, return first.
        // note: some "weird" behaviour can occur, too much trouble to go into..
        // I guess the chances to get picked sort of evens out.
        return reset(array_keys($statistics));
    }

}