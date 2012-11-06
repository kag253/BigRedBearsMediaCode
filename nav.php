<?php
	echo '
		<ul id="topNav">
			<li><a href="index.php" title="Home">Home</a></li>
			<li class="dropDown">
				<a href="" title="About Us">About Us</a>
				<ul>
					<li><a class="firstLink" href="officer.php" title="Officer Information">Officer Information</a></li>
					<li><a class="lastLink" href="history.php" title="History">History</a></li>
				</ul>
			</li>
			<li><a href="calendar.php" title="Calendar">Calendar</a></li>
			<li>
				<a href="managePhotos.php" title="Photos">Photos</a>
			</li>
			<li class="dropDown">';
	if (isset($_SESSION['user'])) {
				echo '<a href="" title="User Area"><span class="red">User Area</span></a>
				<ul>
					<li><a class="firstLink" href="yearbook.php" title="Yearbook">Yearbook</a></li>';
				if ($_SESSION['type']=='admin') {
				echo '
					<li><a href="manageupdates.php" title="Manage Updates">Manage Updates</a></li>
					<li><a href="managemembers.php" title="Manage Members">Manage Members</a></li>
					<li><a href="reimbursements.php" title="Reimbursements">Reimbursements</a></li>
					<li><a href="requests.php" title="View Requests">View Requests';
					
				//open database
				require('db_info.inc');
				$navsqli = new mysqli($hostname,$username,$password,$database);
				$findAllRequests= $navsqli->query("SELECT COUNT(*) FROM Events WHERE attending=0");
				$getNumRequests= $findAllRequests->fetch_row();
				$numRequests= $getNumRequests[0];
				if ($numRequests==0) {
					echo '</a></li>';
				} else {
					echo " <span class=\"red\">($numRequests)</span></a></li>";
				}
				mysqli_close($navsqli);
				echo '
					<li><a href="contacts.php" title="Contacts">Contacts</a></li>';
				} 
				if ($_SESSION['type']<>'alum') {
					echo '
					<li><a href="attendance.php" class="lastLink" title="Attendance">Attendance</a></li>';
				}
				echo '
				</ul>
			</li>
			<li><a href="logoutscript.php" title="Login">Logout</a></li>';
	} else {
		echo '
				<a href="" title="Contact Us">Contact Us</a>
				<ul>
					<li><a class="firstLink" href="requestthebear.php" title="Request a Bear">Request a Bear</a></li>
					<li><a class="lastLink" href="becomeabear.php" title="Become a Bear">Become a Bear</a></li>
					<!-- <li><a class="lastLink" href="faq.php" title="FAQ">FAQ</a></li> -->
				</ul>
			</li>
			<li><a href="login.php" title="Login">Login</a></li>';
	}
	echo '</ul>';
?>