<?php
session_start();
include_once '../assets/conn/dbconnect.php';
$session = $_SESSION['patientSession'];

if (!empty($_GET['scheduleDate']) && !empty($_GET['appid'])) {
	$appdate = $_GET['scheduleDate'];
	$appid = $_GET['appid'];

	// on b.icPatient = a.icPatient
	$res = mysqli_query($con, "SELECT a.*, b.* FROM doctorschedule a INNER JOIN patient b
        WHERE a.scheduleDate='$appdate' AND scheduleId=$appid AND b.philhealthId=$session");

	if ($res) {
		$userRow = mysqli_fetch_array($res, MYSQLI_ASSOC);
	} else {
		// Handle the case when the query fails
		echo "Error: Failed to fetch data from the database.";
		exit;
	}
} else {
	// Handle the case when the variables are not set
	echo "Error: Schedule date and appointment ID are not set.";
	exit;
}
// INSERT
if (isset($_POST['appointment'])) {
	$philhealthId = mysqli_real_escape_string($con, $userRow['philhealthId']);
	$scheduleid = mysqli_real_escape_string($con, $appid);
	$symptom = mysqli_real_escape_string($con, $_POST['symptom']);
	$comment = mysqli_real_escape_string($con, $_POST['comment']);
	$pregnancyWeek = mysqli_real_escape_string($con, $_POST['pregnancy_week']);
	$weight = mysqli_real_escape_string($con, $_POST['weight']);
	$bloodPressure = mysqli_real_escape_string($con, $_POST['blood_pressure']);
	if (!empty($symptom) && !empty($comment)) {
		// Check if there's an existing appointment
		$existingAppointmentQuery = "SELECT * FROM appointment WHERE scheduleId = $scheduleid AND philhealthId = '$philhealthId' AND status = 'process'";
		$existingAppointmentResult = mysqli_query($con, $existingAppointmentQuery);

		if ($existingAppointmentResult && mysqli_num_rows($existingAppointmentResult) > 0) {
			// Existing appointment found, display an alert
			echo "<script>alert('You already have an existing appointment for this schedule.');</script>";
		} else {
			// Check if the appointment date is missed
			$missedAppointmentQuery = "SELECT * FROM doctorschedule WHERE scheduleId = $scheduleid AND scheduleDate < CURDATE()";
			$missedAppointmentResult = mysqli_query($con, $missedAppointmentQuery);

			if ($missedAppointmentResult && mysqli_num_rows($missedAppointmentResult) > 0) {
				// Appointment date is missed, update the status to "missed"
				$updateMissedStatusQuery = "UPDATE appointment SET status = 'missed' WHERE scheduleId = $scheduleid AND philhealthId = '$philhealthId'";
				$updateMissedStatusResult = mysqli_query($con, $updateMissedStatusQuery);

				if (!$updateMissedStatusResult) {
					echo "Error updating appointment status: " . mysqli_error($con);
				}
			} else {
				// Proceed with creating a new appointment
				$avail = "notavail";
				$query = "INSERT INTO appointment (philhealthId, scheduleId, appSymptom, appComment, pregnancyWeek, weight, bloodPressure)
          VALUES ('$philhealthId', '$scheduleid', '$symptom', '$comment', '$pregnancyWeek', '$weight', '$bloodPressure')";



				// Update table appointment schedule
				$sql = "UPDATE doctorschedule SET bookAvail = '$avail' WHERE scheduleId = $scheduleid";
				$scheduleres = mysqli_query($con, $sql);

				if ($scheduleres) {
					$btn = "disable";
				}

				$result = mysqli_query($con, $query);

				if ($result) {
?>
					<script type="text/javascript">
						alert('Appointment made successfully.');
						window.location.href = "patientapplist.php";
					</script>
				<?php
				} else {
					echo mysqli_error($con);
				?>
					<script type="text/javascript">
						alert('Appointment booking failed. Please try again.');
					</script>
		<?php
				}
			}
		}
	} else {
		?>
		<script type="text/javascript">
			alert('Please fill in all the appointment details.');
		</script>
<?php
	}
}
?>

<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

	<title>Make Appoinment</title>
	<link href="assets/css/bootstrap.min.css" rel="stylesheet">
	<link href="assets/css/default/style.css" rel="stylesheet">
	<link href="assets/css/default/blocks.css" rcel="stylesheet">


	<link rel="stylesheet" href="https://formden.com/static/cdn/font-awesome/4.4.0/css/font-awesome.min.css" />

</head>

<body>
	<!-- navigation -->
	<nav class="navbar navbar-default " role="navigation">
		<div class="container-fluid">
			<!-- Brand and toggle get grouped for better mobile display -->
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="patient.php"><img alt="Brand" src="assets/img/cd-logo.png" height="20px"></a>
			</div>
			<!-- Collect the nav links, forms, and other content for toggling -->
			<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
				<ul class="nav navbar-nav">
					<ul class="nav navbar-nav">
						<li><a href="patient.php">Home</a></li>
						<!-- <li><a href="profile.php?patientId=<?php echo $userRow['philhealthId']; ?>" >Profile</a></li> -->
						<li><a href="patientapplist.php?patientId=<?php echo $userRow['philhealthId']; ?>">Appointment</a></li>
					</ul>
				</ul>

				<ul class="nav navbar-nav navbar-right">
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> <?php echo $userRow['patientFirstName']; ?> <?php echo $userRow['patientLastName']; ?><b class="caret"></b></a>
						<ul class="dropdown-menu">
							<li>
								<a href="profile.php?patientId=<?php echo $userRow['philhealthId']; ?>"><i class="fa fa-fw fa-user"></i> Profile</a>
							</li>
							<li>
								<a href="patientapplist.php?patientId=<?php echo $userRow['philhealthId']; ?>"><i class="glyphicon glyphicon-file"></i> Appointment</a>
							</li>
							<li class="divider"></li>
							<li>
								<a href="patientlogout.php?logout"><i class="fa fa-fw fa-power-off"></i> Log Out</a>
							</li>
						</ul>
					</li>
				</ul>
			</div>
		</div>
	</nav>
	<!-- navigation -->
	<div class="container">
		<section style="padding-bottom: 50px; padding-top: 50px;">
			<div class="row">
				<!-- start -->
				<!-- USER PROFILE ROW STARTS-->
				<div class="row">
					<div class="col-md-3 col-sm-3">

						<div class="user-wrapper">
							<img src="assets/img/patient.png" class="img-responsive" />
							<div class="description">
								<h4><?php echo $userRow['patientFirstName']; ?> <?php echo $userRow['patientLastName']; ?></h4>
								<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">Update Profile</button>
							</div>
						</div>
					</div>

					<div class="col-md-9 col-sm-9  user-wrapper">
						<div class="description">


							<div class="panel panel-default">
								<div class="panel-body">


									<form class="form" role="form" method="POST" accept-charset="UTF-8">
										<div class="panel panel-default">
											<div class="panel-heading">Patient Information</div>
											<div class="panel-body">

												Patient Name: <?php echo $userRow['patientFirstName'] ?> <?php echo $userRow['patientLastName'] ?><br>
												Philhealth ID: <?php echo $userRow['philhealthId'] ?><br>
												Contact Number: <?php echo $userRow['patientPhone'] ?><br>
												Address: <?php echo $userRow['patientAddress'] ?>
											</div>
										</div>
										<div class="panel panel-default">
											<div class="panel-heading">Appointment Information</div>
											<div class="panel-body">
												Day: <?php echo $userRow['scheduleDay'] ?><br>
												Date: <?php echo $userRow['scheduleDate'] ?><br>
												Time: <?php echo $userRow['startTime'] ?> - <?php echo $userRow['endTime'] ?><br>
											</div>
										</div>

										<div class="panel panel-default">
											<div class="panel-heading">Prenatal Consultation Information</div>
											<div class="panel-body">

												<div class="form-group">
													<label for="pregnancy-week" class="control-label">Pregnancy Week:</label>
													<input type="text" class="form-control" name="pregnancy_week" required>
												</div>
												<div class="form-group">
													<label for="weight" class="control-label">Weight:</label>
													<input type="text" class="form-control" name="weight" required>
												</div>
												<div class="form-group">
													<label for="blood-pressure" class="control-label">Blood Pressure:</label>
													<input type="text" class="form-control" name="blood_pressure" required>
												</div>
												<div class="form-group">
													<label for="symptom" class="control-label">Symptoms/Concerns:</label>
													<input type="text" class="form-control" name="symptom" required>
												</div>
												<div class="form-group">
													<label for="comment" class="control-label">Comment:</label>
													<textarea class="form-control" name="comment" required></textarea>
												</div>
												<div class="form-group">
													<input type="submit" name="appointment" id="submit" class="btn btn-primary" value="Make Appointment">
												</div>
											</div>
										</div>


									</form>
								</div>
							</div>

						</div>

					</div>
				</div>
				<!-- USER PROFILE ROW END-->
				<!-- end -->
				<script src="assets/js/jquery.js"></script>
				<script src="assets/js/bootstrap.min.js"></script>
</body>

</html>