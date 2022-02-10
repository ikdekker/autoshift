<?php

namespace AutoShift\Strategy;

/**
 * Picks users based on weighted values. Modifies the pick rate by multiplying
 * the pick rate by the (inverse) weight. This means a weight of 0.5 will get
 * picked half the times of a weight of 1 (default).
 */
class WeightedStrategy implements StrategyInterface {

    protected $weights = [];

    public function __construct(array $weights = []) {
        $this->weights = $weights;
    }

    public function pick(array $statistics) {
        $statisticsWeighted = [];

        foreach ($statistics as $user => $pickCount) {
            if (!array_key_exists($user, $this->weights)) {
                $weight = 1;
            } else {
                $weight = $this->weights[$user];
            }

            $statisticsWeighted[$user] = $pickCount / $weight;
        }
        $ak = array_keys($statisticsWeighted, min($statisticsWeighted));
        return array_shift($ak);
    }

}