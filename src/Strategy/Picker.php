<?php

namespace AutoShift\Strategy;

class Picker
{

    protected $strategy;

    public function __construct(StrategyInterface $strategy)
    {
        $this->strategy = $strategy;
    }

    /**
     * function that determines the most eligible person, with the stats of frequency
     *
     * @param array $available
     * @param array $stats
     * @return String the name of the picked user
     */
    function getEligible($available, $statistics = [])
    {
        if (empty($available)) {
            return false;
        }

        if (empty($statistics)) {
            return array_shift($available);
        }

        $availableStats = array_filter($statistics, function ($value) use ($available) {
            return in_array($value, $available);
        }, ARRAY_FILTER_USE_KEY);

        return $this->strategy->pick($availableStats);
    }
}
