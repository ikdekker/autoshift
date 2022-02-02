<?php

namespace AutoShift\Strategy;

/**
 * Tries to pick the person with the lowest pick rate. Will fill pick rate
 * bottom up, where available.
 */
class UniformStrategy implements StrategyInterface {

    public function pick(array $statistics) {
        $ak = array_keys($statistics, min($statistics));
        return array_shift($ak);
    }

}