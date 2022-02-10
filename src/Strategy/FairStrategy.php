<?php

namespace AutoShift\Strategy;

/**
 * Picks users based on weighted values. Modifies the pick rate by multiplying
 * the pick rate by the (inverse) weight. Weights are calculated based on
 * availability. Very similar to weighted, but keeps track of weight by a
 * moving factor.
 * 
 * Based on a weight window with a modifiable size, 20 means the last 20 picks
 * influence the next weight.
 */
class FairStrategy implements StrategyInterface {

    protected $weights = [];

    protected $weightWindowSize = 20;

    private $previousPicks = [];

    public function __construct(int $windowSize) {
        $this->weightWindowSize = $windowSize;
    }

    public function pick(array $statistics) {
        if (count($this->previousPicks) > $this->weightWindowSize) {
            array_shift($this->previousPicks);
        }
        $this->recalculateWeights();
        // Add weights of 1 
        foreach ($statistics as $user => $pickCount) {
            if (!array_key_exists($user, $this->weights)) {
                $this->weights[$user] = 1;
            }
        }
        $this->weights = array_filter($this->weights, function ($value) use ($statistics) {
            return in_array($value, $statistics);
        }, ARRAY_FILTER_USE_KEY);

        // Extract all users with the largest weight
        $maxWeightUsers = array_keys($this->weights, max($this->weights));
        
        // var_dump($maxWeightUsers);
        // Pick the first, should perhaps be random
        $pick = array_shift($maxWeightUsers);
        $this->previousPicks[] = $pick;
        
        return $pick;
    }

    private function recalculateWeights() {
        $calculatedWeights = [];

        $uniques = array_unique($this->statistics);
        // First aggregate per user
        $pickCounts = array_count_values($this->statistics);
        // Loop every user and add their ratio to the new weight table
        foreach ($uniques as $user) {
            // We use the count over window, and use the minimal pick count to
            // weigh in the pick function.
            // 20/20 pick means a weight of 0, 
            // 1/20 picks mean a weight of .95
            // 0/20 picks mean a weight of 1
            $calculatedWeights[$user] = 1 - ($pickCounts[$user] / $this->weightWindowSize);
        }
        // Reset weights with updated value
        $this->weights = $calculatedWeights;
    }

}