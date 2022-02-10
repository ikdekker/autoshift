<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="Autoshift is a tool that generates rosters for employees in a very specific way.">

    <script src="/dist/jquery.js"></script>
    <link rel="stylesheet" type="text/css" href="/css/base/style.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes" />
    <title>Fresh Coders</title>
    <script src="https://kit.fontawesome.com/198ac2fe7d.js" crossorigin="anonymous"></script>
</head>

<div class="empty-split hidden">
    <div class='day-label label'>x</div>
    <div class='none-label'>ALL <i class="fa-solid fa-arrow-left-long"></i></div>
    <div class='all-label'>ALL <i class="fa-solid fa-arrow-right-long"></i></i></div>
    <div class="split false">
        <div class='absent-label label'>Absent</div>
        <div class="false-list"></div>
    </div>
    <div class="split true">
        <div class='present-label label'>Present</div>
        <div class="true-list"></div>
    </div>
</div>

<style>
    #autoshift-dailies,
    .option-header,
    .none-label,
    .all-label {
        clear: both;
    }

    .all-label {
        background-color: darkcyan;
        cursor: pointer;
        padding: 4px;
        height: 32px;
        text-align: center;
        align-items: center;
    }

    .none-label {
        background-color: orangered;
        cursor: pointer;
        padding: 4px;
        height: 32px;
        text-align: center;
        align-items: center;
    }

    #autoshift-dailies {
        background-color: aliceblue;
        /* overflow-y:;  */
        overflow-x: scroll;
        display: -webkit-flex;
        display: flex;
        list-style-type: none;
        padding: 0;
        /* justify-content:flex-end; */
        resize: both;
        overflow: auto;
    }

    .autoshift-dailies-container {
        /* width: 100%; */
        font-weight: 600;
    }

    .split-container {
        width: 100%;
        display: grid;
        grid-template-columns: auto auto;
        grid-template-rows: auto auto auto 1fr;
        padding: 1em;
        column-gap: 1em;
        /* margin:1em; */
        border-right: 2px solid black;
        height: 100%;
        align-items: start;
    }
    .autoshift__availability-daily-container hidden {
        position: relative;
    }

    .split-container .day-label {
        width: 100%;
        grid-column-start: 1;
        grid-column-end: 3;
    }

    .split {
        padding: .4em;
        border: 2px solid cadetblue;
        float: left;
    }

    .true {
        left: 0;
    }

    /* Control the right side */
    .false {
        right: 0;
    }

    .worker-btn {
        background-color: yellowgreen;
        border-radius: 2px;
        margin: 0.4em;
        padding: 0.65em;
        float: left;
        -webkit-user-select: none;
        -moz-user-select: none;
    }

    .autoshift-dailies-container-SATURDAY,
    .autoshift-dailies-container-SUNDAY {
        background-color: darkslategray;
    }

    .autoshift-dailies-container-SATURDAY .label,
    .autoshift-dailies-container-SUNDAY .label .autoshift-dailies-container-SATURDAY .split.label,
    .autoshift-dailies-container-SUNDAY .label {
        color: white;
    }
</style>

<body class="h-screen">
    <?php include '../templates/header.php'; ?>

    Check out <a target='_blank' href="https://github.com/ikdekker/autoshift">https://github.com/ikdekker/autoshift</a>


    <div class="xl:px-64 py-8">
        <h4 class="ml-10 text-2xl font-bold text-blue-700 tracking-tight sm:text-4xl p-0 m-0">Autoshift</h4>
        <div class="pb-4 px-10 text-gray-600 font-bold">
            Autoshift helps creating rosters for your employees. This is a work in progress.
        </div>
        <div class="pb-4 px-10 text-gray-600 font-bold">
            This interface may be moved to a page behind authorization someday.
        </div>
    </div>

    <div class="xl:px-64 py-8 bg-blue-500 px-8">
        <p class="text-gray-200 text-2xl py-2">Generation data</p>
        <div class="xl:px-16 px-4">

            <p class="text-gray-200 text-lg my-2">Timeframe (simplified)</p>
            <!-- <input type="text" class="rounded p-2" name="timeframe" placeholder='this month'> -->
            <select class="rounded my-2 p-2 mr-4 autoshift__start-year" name="autoshift-start-year">
                <option value="2021">2021</option>
                <option value="2022" selected>2022</option>
                <option value="2023">2023</option>
                <option value="2024">2024</option>
                <option value="5000">5000</option>
            </select>
            <select class="rounded my-2 p-2 autoshift__start-month" name="autoshift-start-month">
                <option value="1">January</option>
                <option value="2">February</option>
                <option value="3">March</option>
                <option value="4">April</option>
                <option value="5">May</option>
                <option value="6">June</option>
                <option value="7">Juli</option>
                <option value="8">August</option>
                <option value="9">September</option>
                <option value="10">October</option>
                <option value="11">November</option>
                <option value="12">December</option>
            </select>
            <br>
            <p class="text-gray-200 text-lg my-2">Employees</p>
            <input type="text" class="autoshift__people p-2 my-2 rounded w-full md:w-1/2" name="autoshift-people" id="autoshift-people" value="Alice,Bob">
            <div class="as-example">Example: Vincent,
                Nick,
                Selcuc,
                Czarek,
                Jeroen,
                Samanta,
                Ivo,
                Andre,
                Dennis,
                Shwan,
                Maarten
            </div>
            <br>
            <input type="checkbox" class="autoshift__availability p-2 my-2 rounded" name="autoshift-availability" id="autoshift-availability" checked>
            <label class="text-gray-200 select-none" for="autoshift-availability">Employees available every workday</label>
            <br>
            <div class="autoshift__availability-daily-container hidden">
                <label class="text-gray-200">
                    Geef per werknemer aan wanneer deze werkt!
                </label>
                <div id="autoshift-dailies"></div>
            </div>
        </div>
        <p class="text-gray-200 text-2xl py-2 option-header">Generation options (click to expand/collapse)</p>
        <div class="xl:px-16 px-4 hidden option-container">
            <input type="checkbox" class="autoshift__firstdayofweek p-4 my-8" name="autoshift-firstdayofweek" id="autoshift-firstdayofweek">
            <label class="text-gray-200 select-none" for="autoshift-firstdayofweek">Start weeks on Sunday</label>
            <!-- <p class="text-gray-200 text-lg mt-4 mb-2">Something  (optional)</p>
        <input type="text" class="rounded p-2" name="timeframe" placeholder='this month'> -->
            <br>
            <p class="text-gray-200 text-lg my-2">Render options</p>
            <label class="text-gray-200 select-none" for="autoshift__rendermode">Render mode</label>
            <select class="rounded my-2 p-2 autoshift__rendermode" name="autoshift-rendermode">
                <option value="table">table</option>
                <option value="list">list</option>
            </select>
            <br>
            <label class="text-gray-200 select-none" for="autoshift__cellpadding">Cell padding (only for list)</label>
            <select class="rounded my-2 p-2 autoshift__cellpadding" name="autoshift-cellpadding">
                <option value="0">none</option>
                <option value="2">small</option>
                <option value="4">medium</option>
                <option value="8">big</option>
            </select>
            <br>
            <label class="text-gray-200 select-none" for="autoshift__table_header">Table HTML header</label>
            <textarea cols="40" name='autoshift__table_header' class='autoshift__table_header'></textarea>
            <label class="text-gray-800">Example:
                &lt;center&gt;My Cafeteria Roster!&lt;/center&gt;&lt;img style='float:right;' src=&quot;https://i.pinimg.com/originals/3f/28/0a/3f280aeeb8a9bfa4b6287f4313a501ff.jpg&quot; width=&quot;100&quot; height=&quot;90&quot; /&gt;
            </label>

            <br>
            <label class="text-gray-200 select-none" for="autoshift__table_footer">Table HTML footer</label>
            <textarea cols="40" name='autoshift__table_footer' class='autoshift__table_footer'></textarea>
            <label class="text-gray-800">Example:
                &lt;center&gt;
                &lt;ul&gt;
                &lt;li&gt;List item!&lt;/li&gt;
                &lt;li&gt;List item!&lt;/li&gt;
                &lt;/ul&gt;
                &lt;/center&gt;
            </label>

            <!--p class="text-gray-200 text-lg my-4 mb-2">Generate</p-->
            <!--button class="bg-blue-700 xhover:bg-blue-800 text-gray-200 rounded my-2 p-2 autoshift__preview">Preview data</button-->


        </div>
        <div>
            <button class="bg-blue-700 hover:bg-blue-800 text-gray-200 rounded p-2 my-2 autoshift__generate">Generate</button>
        </div>
        <div id="autoshift-table-output">

        </div>
    </div>

    <?php include '../templates/footer.php'; ?>

</body>

<script>
    working_days = {};

    $(".autoshift__generate").click(function() {
        if ($('.autoshift__people').val() == "") {
            alert("Waarschuwing! Je lijst met medewerkers is leeg, heb je ze ingevuld?");
        }
        $.post('/autoshift/generate.php', {
            'employees': $('.autoshift__people').val(),
            'working_days': aggregateWorkingDays(),
            'year': $('.autoshift__start-year').val(),
            'month': $('.autoshift__start-month').val(),
            'fdow-sunday': $('.autoshift__firstdayofweek').is(':checked') ? true : false,
            'rendermode': $('.autoshift__rendermode').val(),
            'cellpadding': $('.autoshift__cellpadding').val(),
            'header': $('.autoshift__table_header').val(),
            'footer': $('.autoshift__table_footer').val()
        }).done(function(r) {
            $("#autoshift-table-output").html(r);
        });
    });

    $(".autoshift__availability").click(function() {
        if (!$('.autoshift__availability').is(':checked')) {
            $('.autoshift__availability-daily-container').show();
            updateDailies();
        } else {
            $('.autoshift__availability-daily-container').hide();
        }
    })

    $('.autoshift__people').change(function() {
        updateDailies();
    });


    function updateDailies() {
        emps = $('.autoshift__people').val().split(",");

        // may remove
        // if ($.isEmptyObject(working_days)) {
        //     // Fill the object with defaults
        //     $.each(emps, function(k, v) {
        //         working_days[v] = Array(7).fill(false);
        //     });
        // }

        $.each(emps, function(k, emp) {
            if (!(emp in working_days)) {
                working_days[emp] = Array(7).fill(false);
            }
        });

        weekdays = {
            " 1": 'MONDAY',
            " 2": 'TUESDAY',
            " 3": 'WEDNESDAY',
            " 4": 'THURSDAY',
            " 5": 'FRIDAY',
            " 6": 'SATURDAY',
            " 0": "SUNDAY"
        };

        $("#autoshift-dailies").html('');

        $.each(weekdays, function(dayNumber, dayName) {
            createSingleDaySplit(dayNumber.trim(), dayName, emps);
        });

    }

    function createSingleDaySplit(dayNumber, dayName, employees) {
        daySplit = $('.empty-split').clone();
        daySplit.removeClass('empty-split');
        daySplit.addClass('split-container');
        daySplit.addClass('.split-day-' + dayNumber);
        daySplit.find('.day-label').text(dayName);
        daySplit.find('.all-label').attr('data-day', dayNumber);
        daySplit.find('.none-label').attr('data-day', dayNumber);
        daySplit.find('.all-label').attr('onClick', 'transferAll(true, ' + dayNumber + ')');
        daySplit.find('.none-label').attr('onClick', 'transferAll(false, ' + dayNumber + ')');
        // domStruct = $("<div>").append($("<label>").text("dayName")).append($("<ul>"));
        $.each(employees, function(k, emp) {
            if (working_days[emp][dayNumber] == false) {
                daySplit.find(".false-list").append(buildWorkerButton(emp, dayNumber, false));
            } else {
                daySplit.find(".true-list").append(buildWorkerButton(emp, dayNumber, true));
            }
        });
        daySplit.show();
        $("#autoshift-dailies").append($("<div>").addClass('autoshift-dailies-container autoshift-dailies-container-' + dayName).append(daySplit));
    }

    function buildWorkerButton(name, day, status) {
        return $("<div>")
            .text(name)
            .addClass('worker-btn')
            .attr('data-day', day)
            .attr('data-name', name)
            .attr('data-status', status ? 1 : 0)
            .attr('onClick', 'clickWorkerbutton(this);')
            // .attr('ondragover', 'dragWorkerbutton(this);')
            // .attr('draggable', true)
            .css('cursor', 'pointer');
    }

    function clickWorkerbutton(e) {
        name = $(e).attr('data-name');
        day = $(e).attr('data-day');
        status = parseInt($(e).attr('data-status'));
        working_days[name][day] = !parseInt($(e).attr('data-status'));
        // working_days[name][day] = !status;
        updateDailies();
    }

    function dragWorkerbutton(e) {
        console.log(e);
        name = $(e).attr('data-name');
        day = $(e).attr('data-day');
        status = parseInt($(e).attr('data-status'));
        working_days[name][day] = !parseInt($(e).attr('data-status'));
        // working_days[name][day] = !status;
        updateDailies();
    }

    function transferAll(status, dayNumber) {
        console.log(status, dayNumber, working_days);
        $.each(working_days, function(k, v) {
            console.log(k, v);
            working_days[k][dayNumber] = status;
        });
        updateDailies();
    }

    function aggregateWorkingDays() {
        if ($('.autoshift__availability').is(':checked')) {
            return {};
        }

        // todo, convert to json string
        return working_days;
    }

    $(".autoshift__preview").click(function() {
        $.post('/autoshift/preview.php').done(function(r) {
            $("#autoshift-table-output").html(r);
        });
    });

    $('.option-header').click(function() {
        $('.option-container').toggle();
    });
</script>

<?php include '../templates/tutorial.php'; ?>
<script>
    $(document).ready(function() {
        currentStep();
    });
</script>

</html>