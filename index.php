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
</head>

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

            <p class="text-gray-200 text-lg my-2">Employees</p>
            <input type="text" class="autoshift__people p-2 my-2 rounded" name="autoshift-people" id="autoshift-people">
            <div class="as-example">Example: Vincent,
                Nick,
                Selcuc,
                Czarek,
                Jeroen,
                Samanta,
                Ivo,
                Andre,
                Dennis,
                Shwan
            </div>
            <br>
            <input type="checkbox" class="autoshift__availability p-2 my-2 rounded" name="autoshift-availability" id="autoshift-availability" checked disabled>
            <label class="text-gray-200 select-none" for="autoshift__cellpadding">Employees available every workday</label>
        </div>
        <p class="text-gray-200 text-2xl py-2 option-header">Generation options (click to expand/collapse)</p>
        <div class="xl:px-16 px-4 hidden option-container">
            <p class="text-gray-200 text-lg my-2">Timeframe (simplified)</p>
            <!-- <input type="text" class="rounded p-2" name="timeframe" placeholder='this month'> -->
            <select class="rounded my-2 p-2 mr-4 autoshift__start-year cursor-not-allowed" name="autoshift-start-year">
                <option value="2021">2021</option>
                <option value="2022" selected>2022</option>
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
    $(".autoshift__generate").click(function() {
        if ( $('.autoshift__people').val() == "") {
            alert("Waarschuwing! Je lijst met medewerkers is leeg, heb je ze ingevuld?");
        }
        $.post('/autoshift/generate.php', {
            'employees': $('.autoshift__people').val(),
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

    $(".autoshift__preview").click(function() {
        $.post('/autoshift/preview.php').done(function(r) {
            $("#autoshift-table-output").html(r);
        });
    });

    $('.option-header').click(function () {
        $('.option-container').toggle()
    });
</script>

<?php include '../templates/tutorial.php'; ?>
<script>
    $(document).ready(function() {
        currentStep();
    });
</script>

</html>