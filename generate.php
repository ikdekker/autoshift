<?php

use Carbon\Carbon;
use Jupitern\Table\Table;


include_once 'vendor/autoload.php';

include 'userdata.php';


function transformScheduleData($userData, Carbon $start, $startOnSunday = false)
{
    $end = clone $start;
    $end->endOfMonth();
    
    $a = [];
    
    if (!$start->isSunday())
        $start->modify('previous sunday')->startOfDay();
    // Currently this is done in the Table generation, weekstart is always sunday.
    // This is less work. Might change someday.
    // if (!$startOnSunday && !$start->isMonday()) 
    //     $start->modify('previous monday')->startOfDay();

    $userPickCount = array_fill_keys(array_keys($userData), 0);
    $subArray = [];

    for ($date = clone $start; $date <= $end; $date->addDay()) {
        $avail = getAvailableUsers($date, $userData);
        
        if ($date < $start) {

        }

        $leastActive = getLeastActiveUser($avail, $userPickCount);
        
        if ($leastActive)
            @$userPickCount[$leastActive]++;

        
        // get the dayname, for tablegen
        $dayName = strtolower($date->format('l'));
        if (empty($subArray)) {
            $subArray['weekstart'] = clone $date;
        }

        $subArray[$dayName] = $leastActive;
        if (count($subArray) == 8) {
            $a[] = $subArray;
            $subArray = [];
        }

        // exit;
    }
    if (!empty($subArray) && count($subArray) > 1) {
        $a[] = $subArray;
        $subArray = [];
    }

    return $a;
}

function getLeastActiveUser($avail, $stats = []) {
    if (empty($avail)) {
        return false;
    }

    if (empty($stats)) {
        return array_shift($avail);
    }

    $availableStats = array_filter($stats, function ($value) use ($avail) {
        return in_array($value, $avail);
    }, ARRAY_FILTER_USE_KEY);
    
    // array with least actives
    $ak = array_keys($availableStats, min($availableStats));
    return array_shift($ak);
}

function getAvailableUsers($date, $userData = []) {
    $users = [];

    foreach ($userData as $user => $availability) {
        $year = $date->format('Y');
        $startOfYear = clone $date;
        $startOfYear->startOfYear()->startOfDay();
        $day = $startOfYear->diffInDays($date);
        $day++;

        if (@$availability[$year][$day]) {
            $users[] = $user;
        }
    }

    return $users;
}



    // $tableStructure = [
    //     ['weekstart' => Carbon::parse("first sunday of this month")->modify('previous sunday'), 'sunday' => '', 'monday' => 'd', 'tuesday' => 'd'],
    //     ['weekstart' => Carbon::parse("first sunday of this month"), 'sunday' => '', 'monday' => 'f', 'tuesday' => 'e'],
    //     ['weekstart' => Carbon::parse("first sunday of this month")->addDays(7), 'sunday' => '', 'monday' => 'f', 'tuesday' => 'e'],
    //     ['weekstart' => Carbon::parse("first sunday of this month")->addDays(14), 'sunday' => '', 'monday' => 'f', 'tuesday' => 'e'],
    //     ['weekstart' => Carbon::parse("first sunday of this month")->addDays(21), 'sunday' => '', 'monday' => 'f', 'tuesday' => 'e'],
    // ];
function renderTableObject($tableData)
{
    $table = \Jupitern\Table\Table::instance();
    $table->attr('table', 'style', 'border-collapse:collapse;background:white;');

    $table->setData($tableData);

    $days = [
        'monday' => 1,
        'tuesday' => 2,
        'wednesday' => 3,
        'thursday' => 4,
        'friday' => 5,
        'saturday' => 6,
    ];

    $startSunday = isset($_POST['fdow-sunday']) && $_POST['fdow-sunday'] == "true";

    $sunday = ['sunday' => $startSunday ? 0 : 7];
    
    if ($startSunday) {
        $days = $sunday + $days;
    } else {
        $days = $days + $sunday;
    }


    foreach ($days as $day => $info) {
        $css =[];
        if ($day == 'saturday' || $day == 'sunday') {
            $css[] = ['td', 'background', '#d6d6d6'];
        }
        addColumn($table, $day, getCellValueCallable($info), $css);
    }

    $table->render();
}

function addColumn($table, $title, $value, $css = []) {
    $newCol = $table->column()
        ->title($title) // th
        ->value($value)
        ->css('td', 'min-width', '96px')
        ->css('td', 'height', '96px')
        ->css('td', 'margin', '0')
        ->css('td', 'padding', '0')
        ->css('td', 'border', '1px solid black');

    foreach ($css as $style) {
        $newCol->css($style[0], $style[1], $style[2]);
    }

    $newCol->add();
}

function getCellValueCallable($dayOffset) {
    return function ($row) use ($dayOffset) {
        // Get the weekday's name
        $date = clone $row['weekstart'];
        $date->addDays($dayOffset);
        $dayName = strtolower($date->format('l'));
        
        $value = $row[$dayName];

        $dayOfMonth = $date->format('j');
        

        $html = getCellHtml($dayOfMonth, $value);
        return $html;
    };
}

function getCellHtml($dayNumber, $value = '') {
    $top = "<div style='height:16px;padding:4px'>$dayNumber</div>";
    $bottom = "<div style='height:80px;padding:8px;padding-top:12px'>$value</div>";
    $html = $top . $bottom;
    return $html;
}

$start = Carbon::parse("2020-" . $_POST['month']. '-01');
// $start = Carbon::parse("2020-8-01");
$a = transformScheduleData($userData, $start);

renderTableObject($a);
