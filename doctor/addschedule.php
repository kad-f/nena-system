<?php
session_start();
include_once '../assets/conn/dbconnect.php';
// include_once 'connection/server.php';
if (!isset($_SESSION['doctorSession'])) {
    header("Location: ../index.php");
}
$usersession = $_SESSION['doctorSession'];
$res = mysqli_query($con, "SELECT * FROM doctor WHERE doctorId=" . $usersession);
$userRow = mysqli_fetch_array($res, MYSQLI_ASSOC);
// insert


if (isset($_POST['submit'])) {
    $date = mysqli_real_escape_string($con, $_POST['date']);
    $scheduleday = isset($_POST['scheduleday']) ? mysqli_real_escape_string($con, $_POST['scheduleday']) : '';
    $starttime = isset($_POST['starttime']) ? mysqli_real_escape_string($con, $_POST['starttime']) : '';
    $endtime = isset($_POST['endtime']) ? mysqli_real_escape_string($con, $_POST['endtime']) : '';
    $bookavail = isset($_POST['bookavail']) ? mysqli_real_escape_string($con, $_POST['bookavail']) : '';

    // Check the user's role
    if ($userRow['doctorRole'] == 'superAdmin') {
        // If user is 'superAdmin', use the selected doctor from the dropdown
        $selectedDoctorId = mysqli_real_escape_string($con, $_POST['selectDoctor']);
    } else {
        // For other doctors, use their own doctorId
        $selectedDoctorId = $usersession;
    }

    // INSERT
    $query = "INSERT INTO doctorschedule (scheduleDate, scheduleDay, startTime, endTime, bookAvail, doctorId)
              VALUES ('$date', '$scheduleday', '$starttime', '$endtime', '$bookavail', '$selectedDoctorId')";

    $result = mysqli_query($con, $query);

    if ($result) {
?>
        <script type="text/javascript">
            alert('Schedule added successfully.');
        </script>
    <?php
    } else {
    ?>
        <script type="text/javascript">
            alert('Added fail. Please try again.');
        </script>
<?php
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>
        <?php
        if ($userRow['doctorRole'] == 'superAdmin') {
            echo "Welcome " . $userRow['doctorFirstName'] . " " . $userRow['doctorLastName'];
        } else {
            echo "Welcome Dr " . $userRow['doctorFirstName'] . " " . $userRow['doctorLastName'];
        }
        ?>
    </title>
    <!-- Bootstrap Core CSS -->
    <!-- <link href="assets/css/bootstrap.css" rel="stylesheet"> -->
    <link href="assets/css/material.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="assets/css/sb-admin.css" rel="stylesheet">
    <link href="assets/css/time/bootstrap-clockpicker.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="assets/font-awesome/css/font-awesome.css" rel="stylesheet">
    <!-- Special version of Bootstrap that only affects content wrapped in .bootstrap-iso -->
    <link rel="stylesheet" href="https://formden.com/static/cdn/bootstrap-iso.css" />
    <!--Font Awesome (added because you use icons in your prepend/append)-->
    <link rel="stylesheet" href="https://formden.com/static/cdn/font-awesome/4.4.0/css/font-awesome.min.css" />

    <!-- Inline CSS based on choices in "Settings" tab -->
    <style>
        .bootstrap-iso .formden_header h2,
        .bootstrap-iso .formden_header p,
        .bootstrap-iso form {
            font-family: Arial, Helvetica, sans-serif;
            color: black
        }

        .bootstrap-iso form button,
        .bootstrap-iso form button:hover {
            color: white !important;
        }

        .asteriskField {
            color: red;
        }
    </style>

    <!-- Custom Fonts -->
</head>

<body>
    <div id="wrapper">

        <!-- Navigation -->
        <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="doctordashboard.php">
                    <?php

                    if ($userRow['doctorRole'] == 'superAdmin') {
                        echo "Welcome " . $userRow['doctorFirstName'] . " " . $userRow['doctorLastName'];
                    } else {
                        echo "Welcome Dr " . $userRow['doctorFirstName'] . " " . $userRow['doctorLastName'];
                    }
                    ?>
                </a>
            </div>
            <!-- Top Menu Items -->
            <ul class="nav navbar-right top-nav">


                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> <?php echo $userRow['doctorFirstName']; ?> <?php echo $userRow['doctorLastName']; ?><b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="doctorprofile.php"><i class="fa fa-fw fa-user"></i> Profile</a>
                        </li>
                        <li>
                            <a href="inbox.php"><i class="fa fa-fw fa-envelope"></i> Inbox</a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="logout.php?logout"><i class="fa fa-fw fa-power-off"></i> Log Out</a>
                        </li>
                    </ul>
                </li>
            </ul>
            <!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->
            <div class="collapse navbar-collapse navbar-ex1-collapse">
                <ul class="nav navbar-nav side-nav">
                    <li>
                        <a href="doctordashboard.php"><i class="fa fa-fw fa-dashboard"></i> Dashboard</a>
                    </li>
                    <?php
                    // Check if the user's role is "superAdmin"
                    if ($userRow['doctorRole'] == 'superAdmin') {
                        // Display the following options for the superAdmin role
                    ?>
                        <li class="active">
                            <a href="addschedule.php"><i class="fa fa-fw fa-table"></i> Doctor Schedule</a>
                        </li>
                        <li>
                            <a href="doctor.php"><i class="fa fa-fw fa-user"></i> Doctor</a>
                        </li>
                        <li>
                            <a href="patientlist.php"><i class="fa fa-fw fa-user"></i>Prenatal Patient List</a>
                        </li>
                        <li>
                            <a href="tbpatientlist.php"><i class="fa fa-fw fa-user"></i>TB Patient List</a>
                        </li>
                    <?php
                    }
                    ?>
                    <?php
                    $allowedRoles = ['Pulmonologist', 'Obstetrician'];

                    // Check if the user's role is in the allowedRoles array
                    if (in_array($userRow['doctorRole'], $allowedRoles)) {
                        // Display the following options for specific roles
                    ?>
                        <li class="active">
                            <a href="addschedule.php"><i class="fa fa-fw fa-table"></i> Doctor Schedule</a>
                        </li>
                        <li>
                            <a href="patientlist.php"><i class="fa fa-fw fa-user"></i> Patient List</a>
                        </li>
                    <?php
                    }
                    ?>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </nav>
        <!-- navigation end -->

        <div id="page-wrapper">
            <div class="container-fluid">

                <!-- Page Heading -->
                <div class="row">
                    <div class="col-lg-12">
                        <h2 class="page-header">
                            Doctor Schedule
                        </h2>
                        <ol class="breadcrumb">
                            <li class="active">
                                <i class="fa fa-calendar"></i> Schedule
                            </li>
                        </ol>
                    </div>
                </div>
                <!-- Page Heading end-->

                <!-- panel start -->
                <div class="panel panel-primary">

                    <!-- panel heading starat -->
                    <div class="panel-heading">
                        <h3 class="panel-title">Add Schedule</h3>
                    </div>
                    <!-- panel heading end -->

                    <div class="panel-body">
                        <!-- panel content start -->
                        <div class="bootstrap-iso">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <form class="form-horizontal" method="post">
                                            <?php if ($userRow['doctorRole'] == 'superAdmin') { ?>
                                                <div class="form-group form-group-lg">
                                                    <label class="control-label col-sm-2 requiredField" for="selectDoctor">
                                                        Select Doctor
                                                        <span class="asteriskField">*</span>
                                                    </label>
                                                    <div class="col-sm-10">
                                                        <select class="select form-control" id="selectDoctor" name="selectDoctor" required>
                                                            <?php
                                                            // Fetch the list of doctors from the database
                                                            $doctorQuery = "SELECT * FROM doctor";
                                                            $doctorResult = mysqli_query($con, $doctorQuery);

                                                            if ($doctorResult) {
                                                                while ($doctorRow = mysqli_fetch_assoc($doctorResult)) {
                                                                    echo "<option value='" . $doctorRow['doctorId'] . "'>" . $doctorRow['doctorFirstName'] . " " . $doctorRow['doctorLastName'] . "</option>";
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                            <div class="form-group form-group-lg">
                                                <label class="control-label col-sm-2 requiredField" for="date">
                                                    Date
                                                    <span class="asteriskField">
                                                        *
                                                    </span>
                                                </label>
                                                <div class="col-sm-10">
                                                    <div class="input-group">
                                                        <div class="input-group-addon">
                                                            <i class="fa fa-calendar">
                                                            </i>
                                                        </div>
                                                        <input class="form-control" id="date" name="date" type="text" required />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group form-group-lg">
                                                <label class="control-label col-sm-2 requiredField" for="scheduleday">
                                                    Day
                                                    <span class="asteriskField">
                                                        *
                                                    </span>
                                                </label>
                                                <div class="col-sm-10">
                                                    <select class="select form-control" id="scheduleday" name="scheduleday" required>
                                                        <option value="Monday">
                                                            Monday
                                                        </option>
                                                        <option value="Tuesday">
                                                            Tuesday
                                                        </option>
                                                        <option value="Wednesday">
                                                            Wednesday
                                                        </option>
                                                        <option value="Thursday">
                                                            Thursday
                                                        </option>
                                                        <option value="Friday">
                                                            Friday
                                                        </option>
                                                        <option value="Saturday">
                                                            Saturday
                                                        </option>
                                                        <option value="Sunday">
                                                            Sunday
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group form-group-lg">
                                                <label class="control-label col-sm-2 requiredField" for="starttime">
                                                    Start Time
                                                    <span class="asteriskField">
                                                        *
                                                    </span>
                                                </label>

                                                <div class="col-sm-10">
                                                    <div class="input-group clockpicker" data-align="top" data-autoclose="true">
                                                        <div class="input-group-addon">
                                                            <i class="fa fa-clock-o">
                                                            </i>
                                                        </div>
                                                        <input class="form-control" id="starttime" name="starttime" type="text" required />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group form-group-lg">
                                                <label class="control-label col-sm-2 requiredField" for="endtime">
                                                    End Time
                                                    <span class="asteriskField">
                                                        *
                                                    </span>
                                                </label>
                                                <div class="col-sm-10">
                                                    <div class="input-group clockpicker" data-align="top" data-autoclose="true">
                                                        <div class="input-group-addon">
                                                            <i class="fa fa-clock-o">
                                                            </i>
                                                        </div>
                                                        <input class="form-control" id="endtime" name="endtime" type="text" required />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group form-group-lg">
                                                <label class="control-label col-sm-2 requiredField" for="bookavail">
                                                    Availabilty
                                                    <span class="asteriskField">
                                                        *
                                                    </span>
                                                </label>
                                                <div class="col-sm-10">
                                                    <select class="select form-control" id="bookavail" name="bookavail" required>
                                                        <option value="available">
                                                            available
                                                        </option>
                                                        <option value="notavail">
                                                            notavail
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-sm-10 col-sm-offset-2">
                                                    <button class="btn btn-primary " name="submit" type="submit">
                                                        Submit
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- panel content end -->
                        <!-- panel end -->
                    </div>
                </div>
                <!-- panel start -->

                <!-- panel start -->
                <div class="panel panel-primary filterable">

                    <!-- panel heading starat -->
                    <div class="panel-heading">
                        <h3 class="panel-title">List of Schedule</h3>
                        <div class="pull-right">
                            <button class="btn btn-default btn-xs btn-filter"><span class="fa fa-filter"></span> Filter</button>
                        </div>
                    </div>
                    <!-- panel heading end -->

                    <div class="panel-body">
                        <!-- panel content start -->
                        <!-- Table -->
                        <table class="table table-hover table-bordered">
                            <thead>
                                <tr class="filters">
                                    <th><input type="text" class="form-control" placeholder="Doctor's Name" disabled></th>
                                    <th><input type="text" class="form-control" placeholder="scheduleDate" disabled></th>
                                    <th><input type="text" class="form-control" placeholder="scheduleDay" disabled></th>
                                    <th><input type="text" class="form-control" placeholder="startTime." disabled></th>
                                    <th><input type="text" class="form-control" placeholder="endTime" disabled></th>
                                    <th><input type="text" class="form-control" placeholder="bookAvail" disabled></th>
                                </tr>
                            </thead>

                            <?php
                            if ($userRow['doctorRole'] == 'superAdmin') {
                                $scheduleQuery = "SELECT ds.*, d.doctorFirstName, d.doctorLastName FROM doctorschedule ds
                      LEFT JOIN doctor d ON ds.doctorId = d.doctorId";
                            } else {
                                $scheduleQuery = "SELECT ds.*, d.doctorFirstName, d.doctorLastName FROM doctorschedule ds
                      LEFT JOIN doctor d ON ds.doctorId = d.doctorId
                      WHERE ds.doctorId = $usersession";
                            }
                            $result = mysqli_query($con, $scheduleQuery);

                            if (!$result) {
                                die("Error in SQL query: " . mysqli_error($con));
                            }

                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tbody>";
                                echo "<tr class='filters'>";
                                echo "<td>" . $row['doctorFirstName'] . " " . $row['doctorLastName'] . "</td>";
                                echo "<td>" . $row['scheduleDate'] . "</td>";
                                echo "<td>" . $row['scheduleDay'] . "</td>";
                                echo "<td>" . $row['startTime'] . "</td>";
                                echo "<td>" . $row['endTime'] . "</td>";
                                echo "<td>" . $row['bookAvail'] . "</td>";
                                echo "<td class='text-center'>
        <button type='button' class='btn btn-danger'>
            <a href='#' id='" . $row['scheduleId'] . "' class='delete'>Delete</a>
        </button>
       <button type='submit' class='btn btn-primary update-btn' data-schedule-id='" . $row['scheduleId'] . "' id='" . $row['scheduleId'] . "'>Update</button>
    </td>";

                                echo "</tr>";
                                echo "</tbody>";
                            }
                            echo "</table>";
                            echo "<div class='panel panel-default'>";
                            echo "<div class='col-md-offset-3 pull-right'>";

                            echo "</div>";
                            echo "</div>";

                            ?>

                            <!-- panel content end -->
                            <!-- panel end -->
                    </div>
                </div>
                <!-- panel start -->
            </div>
        </div>
        <!-- /#wrapper -->



        <!-- jQuery -->
        <script src="../patient/assets/js/jquery.js"></script>

        <!-- Bootstrap Core JavaScript -->
        <script src="../patient/assets/js/bootstrap.min.js"></script>
        <script src="assets/js/bootstrap-clockpicker.js"></script>
        <!-- Latest compiled and minified JavaScript -->
        <!-- script for jquery datatable start-->
        <!-- Include Date Range Picker -->
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/js/bootstrap-datepicker.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/css/bootstrap-datepicker3.css" />

        <script>
            $(document).ready(function() {
                var date_input = $('input[name="date"]'); //our date input has the name "date"
                var container = $('.bootstrap-iso form').length > 0 ? $('.bootstrap-iso form').parent() : "body";
                date_input.datepicker({
                    format: 'yyyy/mm/dd',
                    container: container,
                    todayHighlight: true,
                    autoclose: true,
                })
            })
        </script>
        <script type="text/javascript">
            $('.clockpicker').clockpicker();
        </script>
        <script type="text/javascript">
            $(function() {
                $(".delete").click(function() {
                    var element = $(this);
                    var id = element.attr("id");
                    var info = 'id=' + id;
                    if (confirm("Are you sure you want to delete this?")) {
                        $.ajax({
                            type: "POST",
                            url: "deleteschedule.php",
                            data: info,
                            success: function() {}
                        });
                        $(this).parent().parent().fadeOut(300, function() {
                            $(this).remove();
                        });
                    }
                    return false;
                });

                $(".update-btn").click(function() {
                    var scheduleId = $(this).attr("id");
                    $.ajax({
                        type: "POST",
                        url: "updatestatus.php",
                        data: {
                            updateSchedule: true,
                            scheduleId: scheduleId
                        },
                        dataType: 'json',
                        success: function() {
                            // Reload the page after the update is successful
                            location.reload();
                        },
                        error: function(xhr, status, error) {
                            location.reload();
                        }
                    });
                });

            });
        </script>
        <script type="text/javascript">
            /*
            Please consider that the JS part isn't production ready at all, I just code it to show the concept of merging filters and titles together !
            */
            $(document).ready(function() {
                $('.filterable .btn-filter').click(function() {
                    var $panel = $(this).parents('.filterable'),
                        $filters = $panel.find('.filters input'),
                        $tbody = $panel.find('.table tbody');
                    if ($filters.prop('disabled') == true) {
                        $filters.prop('disabled', false);
                        $filters.first().focus();
                    } else {
                        $filters.val('').prop('disabled', true);
                        $tbody.find('.no-result').remove();
                        $tbody.find('tr').show();
                    }
                });

                $('.filterable .filters input').keyup(function(e) {
                    /* Ignore tab key */
                    var code = e.keyCode || e.which;
                    if (code == '9') return;
                    /* Useful DOM data and selectors */
                    var $input = $(this),
                        inputContent = $input.val().toLowerCase(),
                        $panel = $input.parents('.filterable'),
                        column = $panel.find('.filters th').index($input.parents('th')),
                        $table = $panel.find('.table'),
                        $rows = $table.find('tbody tr');
                    /* Dirtiest filter function ever ;) */
                    var $filteredRows = $rows.filter(function() {
                        var value = $(this).find('td').eq(column).text().toLowerCase();
                        return value.indexOf(inputContent) === -1;
                    });
                    /* Clean previous no-result if exist */
                    $table.find('tbody .no-result').remove();
                    /* Show all rows, hide filtered ones (never do that outside of a demo ! xD) */
                    $rows.show();
                    $filteredRows.hide();
                    /* Prepend no-result row if all rows are filtered */
                    if ($filteredRows.length === $rows.length) {
                        $table.find('tbody').prepend($('<tr class="no-result text-center"><td colspan="' + $table.find('.filters th').length + '">No result found</td></tr>'));
                    }
                });
            });
        </script>

</body>

</html>