<?php

use AutoShift\Strategy\FairStrategy;
use AutoShift\Strategy\Picker;
use AutoShift\Strategy\SequentialStrategy;
use AutoShift\Strategy\UniformStrategy;
use AutoShift\Strategy\WeightedStrategy;
use Carbon\Carbon;
use Jupitern\Table\Table;


include_once 'vendor/autoload.php';

include 'userdata.php';

function transformScheduleData($userData, Carbon $start, $startOnSunday = false)
{
    $a = [];
    $end = clone $start;
    // $end->addMonth()->endOfMonth();
    $end->endOfYear();
    
    if (!$start->isSunday())
        $start->modify('previous sunday')->startOfDay();

    $userPickCount = array_fill_keys(array_keys($userData), 0);
    $subArray = [];

    // todo: fix fairstrategy
    // $picker = new Picker(new FairStrategy(20));
    $picker = new Picker(new UniformStrategy);

    for ($date = clone $start; $date <= $end; $date->addDay()) {
        $avail = getAvailableUsers($date, $userData);

        $eligible = $picker->getEligible($avail, $userPickCount);
        if ($eligible)
            @$userPickCount[$eligible]++;


        // get the dayname, for tablegen
        $dayName = strtolower($date->format('l'));
        if (empty($subArray)) {
            $subArray['weekstart'] = clone $date;
        }

        $subArray[$dayName] = $eligible;
        if (count($subArray) == 8) {
            $a[] = $subArray;
            $subArray = [];
        }
    }

    if (!empty($subArray) && count($subArray) > 1) {
        $a[] = $subArray;
        $subArray = [];
    }

    return $a;
}

function getAvailableUsers($date, $userData = [])
{
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


function renderTableObject($tableData)
{

    $monthTables = [];
    $lastMonth = (clone $tableData[0]['weekstart'])->format('m');

    $overlapped = [];
    foreach ($tableData as $weekData) {
        $monthOfWeekstart = (clone $weekData['weekstart'])->format('Y-m');
        $monthOfFriday = (clone $weekData['weekstart'])->addDays('5')->format('Y-m');
        $monthTables[$monthOfWeekstart][] = $weekData;

        if ($monthOfWeekstart !== $monthOfFriday) {
            $monthTables[$monthOfFriday][] = $weekData;
        }
    }

    $days = [
        'monday' => 1,
        'tuesday' => 2,
        'wednesday' => 3,
        'thursday' => 4,
        'friday' => 5,
        'saturday' => 6,
    ];
    $firstDayOfWeekIsSunday = filter_var(@$_POST['fdow-sunday'], FILTER_SANITIZE_STRING);
    $startSunday = $firstDayOfWeekIsSunday == "true";

    $sunday = ['sunday' => $startSunday ? 0 : 7];
    if ($startSunday) {
        $days = $sunday + $days;
    } else {
        $days = $days + $sunday;
    }

    // sort months in ascending order
    ksort($monthTables);

    foreach ($monthTables as $m => $monthTableData) {
        // usort($monthTableData, function($x, $y) {
        //     return $y['weekstart']->timestamp - $x['weekstart']->timestamp;
        // });
        $table = \Jupitern\Table\Table::instance();
        $table->attr('table', 'style', 'border-collapse:collapse;background:white;');
        if (count($monthTableData) < 3)
            continue;
        $table->setData($monthTableData);

        foreach ($days as $day => $info) {
            $css = [];
            if ($day == 'saturday' || $day == 'sunday') {
                $css[] = ['td', 'background', '#d6d6d6'];
            }
            addColumn($table, $day, getCellValueCallable($info), $css);
        }

        // exit;
        
        echo '<div style="padding:50px 20px;background:white;width:800px;">';
        
        echo $_POST['header'];
        echo '<center>';
        echo "<br><span style='font-size:20px'><b>"
         . (clone $monthTableData[1]['weekstart'])->format('F Y') . "</b></span>";
        $table->render();
        echo '</center>';
        echo $_POST['footer'];
        echo '</div>';
        echo '<br>';
    }
}


function renderListObject($tableData)
{
    $table = \Jupitern\Table\Table::instance();
    $table->attr('table', 'style', 'border-collapse:collapse;background:white;');

    $days = [
        'monday' => 1,
        'tuesday' => 2,
        'wednesday' => 3,
        'thursday' => 4,
        'friday' => 5,
        'saturday' => 6,
    ];

    $startSunday = false;

    $sunday = ['sunday' => $startSunday ? 0 : 7];

    if ($startSunday) {
        $days = $sunday + $days;
    } else {
        $days = $days + $sunday;
    }

    $reformattedData = [];

    foreach ($tableData as $k => $v) {
        $x = 1;
        foreach ($days as $day => $_) {
            $date = clone $v['weekstart'];
            $date->addDays($x);
            $dayName = strtolower($date->format('l M-d'));
            $reformattedData[] = [$dayName, $v[$day]];
            $x++;
        }
    }

    $table->setData($reformattedData);


    addColumnWide($table, 'day', 0, [['td', 'min-width', '180px']]);
    addColumnWide($table, 'responsible for clean up', 1, [['td', 'width', '99%']]);
    addColumnWide($table, 'check', 2, [['td', 'width', '20px']]);
    addColumnWide($table, 'paid', 2, [['td', 'width', '20px']]);

    $table->render();
}

function addColumnWide($table, $title, $value, $css = [])
{
    $newCol = $table->column()
        ->title($title) // th
        ->value($value)
        ->css('td', 'min-width', '96px')
        ->css('td', 'height', '12px')
        ->css('td', 'margin', '0')
        ->css('td', 'padding', (@$_POST['cellpadding'] ?? "4") . 'px')
        ->css('td', 'border', '1px solid black');

    foreach ($css as $style) {
        $newCol->css($style[0], $style[1], $style[2]);
    }

    $newCol->add();
}
function addColumn($table, $title, $value, $css = [])
{
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


function getCellValueCallable($dayOffset)
{
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


function getCellHtml($dayNumber, $value = '')
{
    $top = "<div style='height:16px;padding:4px'>$dayNumber</div>";
    $bottom = "<div style='height:80px;padding:8px;padding-top:12px'>$value</div>";
    $html = $top . $bottom;
    return $html;
}

function getRowHtml($value = '')
{
    $top = "<div style='height:16px;padding:4px'>" . $value . "</div>";
    $html = $top;
    return $html;
}

$year = filter_var($_POST['year'], FILTER_SANITIZE_NUMBER_INT);
$month = filter_var($_POST['month'], FILTER_SANITIZE_NUMBER_INT);
$employees = filter_var($_POST['employees'], FILTER_SANITIZE_STRING);
$workingDays = (array) $_POST['working_days'];
$useWorkingDays = !empty($workingDays);

$start = Carbon::parse($year . "-" . $month . '-01');

// $start = Carbon::parse("2020-8-01");
//temporary userdata fill for all workdays
$userData = [];
$employees = explode(',', $employees);
$dateLooper = Carbon::parse($year . '-01-01');
while ($dateLooper->dayOfYear < 365) {
    foreach ($employees as $emp) {
        if ($workingDays[$emp][$dateLooper->dayOfWeek] == 'true' || ($useWorkingDays == false && !in_array($dateLooper->dayOfWeek, [0,6]))) {
            $userData[$emp][$year][$dateLooper->dayOfYear] = true;
        }
    }
    $dateLooper->addDay();
}
$a = transformScheduleData($userData, $start);

if ($_POST['rendermode'] == 'table') {
    renderTableObject($a);
} else {
    renderListObject($a);
}
