<?php
session_start();
$con = mysqli_connect("localhost","sthunna_isasys","5pGbs2G3xFunbZn","sthunna_isasys");
if(!$con)
	die("Could not connect: " . mysqli_error($con));

if(isset($_SESSION['isasys']))
{
	$has_permission = mysqli_fetch_array(mysqli_query($con,"SELECT * FROM ospos_permissions WHERE person_id=".$_SESSION['isasys']." AND module_id='config'"));
	if(isset($has_permission['person_id']))
	{
		echo "<style>form{margin:1}</style>";
		echo "<h3>";
		echo "<a href='?receivable'>Payments for Collectibles</a> | ";
		//echo "<a href='?purchase'>Purchase</a> | ";
		echo "<a href='?logs'>User Logs</a> | ";
		echo "<a href='?logout'>Logout</a>";
		echo "</h3>";
		
		if(isset($_GET['logout']))
		{
			unset($_SESSION['isasys']);
			header('location: payments.php');
		}
		else if(isset($_GET['receivable']))
		{
			$page = isset($_GET['page'])?(int)$_GET['page']:0;
			$perpage = isset($_GET['perpage'])?(int)$_GET['perpage']:25;
			$search = isset($_GET['search'])?$_GET['search']:'';
			$search = isset($_POST['search'])?$_POST['search']:$search;
			$by = isset($_GET['type'])?$_GET['type']:'';
			$by = isset($_POST['type'])?$_POST['type']:$by;
			
			if(isset($_GET['delete']))
			{
				$payment_id = (int)$_GET['delete'];
				mysqli_query($con,"UPDATE ospos_receivable_payments SET mode=-1 WHERE payment_id=$payment_id");
				mysqli_query($con,"INSERT INTO ospos_user_logs (person_id,action) VALUES (".((int)$_SESSION['isasys']).",'100,$payment_id')");
				echo "Success: Payment ID $payment_id deleted.";
			}
			else if(isset($_GET['undelete']))
			{
				$payment_id = (int)$_GET['undelete'];
				mysqli_query($con,"UPDATE ospos_receivable_payments SET mode=0 WHERE payment_id=$payment_id");
				mysqli_query($con,"INSERT INTO ospos_user_logs (person_id,action) VALUES (".((int)$_SESSION['isasys']).",'101,$payment_id')");
				echo "Success: Payment ID $payment_id undo deletion.";
			}
			
			echo "<style>tr:hover{background-color:#eee}</style>";
			
			echo "<form action='?receivable' method='post'>";
			echo "Search: <input type='text' name='search' value='$search' placeholder='Enter keywords...' />";
			echo "<select name='type'>";
				echo "<option value='itr_number'".($by=='itr_number'?" selected":"").">DR#</option>";
				echo "<option value='payment_amount'".($by=='payment_amount'?" selected":"").">Amount</option>";
				echo "<option value='or_number'".($by=='or_number'?" selected":"").">OR#</option>";
				echo "<option value='comments'".($by=='comments'?" selected":"").">Comments</option>";
			echo "</select>";
			echo "<input type='submit' name='form_submit' value='Go!' />";
			echo "</form>";
			
			echo "<table border='1' width='1000px'>";
			echo "<tr>";
				echo "<th>ID#</th>";
				echo "<th>DR#</th>";
				echo "<th>Payment Date</th>";
				echo "<th>Amount</th>";
				echo "<th>OR#</th>";
				echo "<th>Comments</th>";
				echo "<th>Actions</th>";
			echo "</tr>";
			
			$where = "";
			if($search!='')
			{
				switch($by)
				{
					case 'itr_number':
						$where = "WHERE itr_number=".((int)$search);
						break;
					case 'payment_amount':
						$where = "WHERE payment_amount=".((double)$search);
						break;
					case 'or_number':
						$where = "WHERE or_number LIKE '%".mysqli_real_escape_string($con,$search)."%'";
						break;
					case 'comments':
						$where = "WHERE ospos_receivable_payments.comments LIKE '%".mysqli_real_escape_string($con,$search)."%'";
						break;
					default: break;
				}
			}
			
			$count = mysqli_fetch_array(mysqli_query($con,"SELECT COUNT(*) count FROM ospos_receivable_payments LEFT JOIN ospos_receivables ON ospos_receivables.receivable_id=ospos_receivable_payments.receivable_id $where"));
			$total = $count['count'];
			$payments = mysqli_query($con,"
				SELECT
					*,
					ospos_receivable_payments.comments comments
				FROM ospos_receivable_payments
				LEFT JOIN ospos_receivables ON ospos_receivables.receivable_id=ospos_receivable_payments.receivable_id
				$where
				ORDER BY payment_date DESC
				LIMIT ".($page*$perpage).",$perpage");
			while($payment = mysqli_fetch_array($payments))
			{
				echo "<tr".($payment['mode']==-1?" style='background-color:#fdd'":"").">";
					echo "<td align='right'><b>".$payment['payment_id']."</b></td>";
					echo "<td align='right'>".$payment['itr_number']."</td>";
					echo "<td align='center'>".$payment['payment_date']."</td>";
					echo "<td align='right'>".number_format($payment['payment_amount'],4,'.',',')."</td>";
					echo "<td>".$payment['or_number']."&nbsp;</td>";
					echo "<td>".$payment['comments']."&nbsp;</td>";
					echo "<td align='center'>";
						if($payment['mode']==-1)
							echo "<a href='?receivable&page=$page&perpage=$perpage&search=$search&type=$by&undelete=".$payment['payment_id']."'>Undo deletion</a>";
						else if($payment['mode']==0)
							echo "<a href='?receivable&page=$page&perpage=$perpage&search=$search&type=$by&delete=".$payment['payment_id']."'>Delete</a>";
					echo "</td>";
				echo "</tr>";
			}
			echo "</table>";
			echo "Page: ";
			for($x=0;$x<ceil($total/$perpage);$x++)
			{
				if($x==$page)
					echo "<b>".($x+1)."</b> ";
				else
					echo "<a href='?receivable&page=$x'>".($x+1)."</a> ";
			}
			if(ceil($total/$perpage)>1)
				echo "(<a href='?receivable&perpage=$total'>View All</a>)";
			echo "<br />";
			echo "Total found: $total";
		}
		else if(isset($_GET['purchase']))
		{
			$page = isset($_GET['page'])?(int)$_GET['page']:0;
			$perpage = isset($_GET['perpage'])?(int)$_GET['perpage']:25;
			$search = isset($_GET['search'])?$_GET['search']:'';
			$search = isset($_POST['search'])?$_POST['search']:$search;
			$by = isset($_GET['type'])?$_GET['type']:'';
			$by = isset($_POST['type'])?$_POST['type']:$by;
			
			if(isset($_GET['delete']))
			{
				$payment_id = (int)$_GET['delete'];
				mysqli_query($con,"UPDATE ospos_purchase_payments SET mode=-1 WHERE payment_id=$payment_id");
				mysqli_query($con,"INSERT INTO ospos_user_logs (person_id,action) VALUES (".((int)$_SESSION['isasys']).",'100,$payment_id')");
				echo "Success: Payment ID $payment_id deleted.";
			}
			else if(isset($_GET['undelete']))
			{
				$payment_id = (int)$_GET['undelete'];
				mysqli_query($con,"UPDATE ospos_purchase_payments SET mode=0 WHERE payment_id=$payment_id");
				mysqli_query($con,"INSERT INTO ospos_user_logs (person_id,action) VALUES (".((int)$_SESSION['isasys']).",'101,$payment_id')");
				echo "Success: Payment ID $payment_id undo deletion.";
			}
			
			echo "<style>tr:hover{background-color:#eee}</style>";
			
			echo "<form action='?purchase' method='post'>";
			echo "Search: <input type='text' name='search' value='$search' placeholder='Enter keywords...' />";
			echo "<select name='type'>";
				echo "<option value='po_number'".($by=='po_number'?" selected":"").">PR#</option>";
				echo "<option value='payment_amount'".($by=='payment_amount'?" selected":"").">Amount</option>";
				echo "<option value='or_number'".($by=='or_number'?" selected":"").">OR#</option>";
				echo "<option value='comments'".($by=='comments'?" selected":"").">Comments</option>";
			echo "</select>";
			echo "<input type='submit' name='form_submit' value='Go!' />";
			echo "</form>";
			
			echo "<table border='1' width='1000px'>";
			echo "<tr>";
				echo "<th>ID#</th>";
				echo "<th>PR#</th>";
				echo "<th>Payment Date</th>";
				echo "<th>Amount</th>";
				echo "<th>OR#</th>";
				echo "<th>Comments</th>";
				echo "<th>Actions</th>";
			echo "</tr>";
			
			$where = "";
			if($search!='')
			{
				switch($by)
				{
					case 'po_number':
						$where = "WHERE po_number=".((int)$search);
						break;
					case 'payment_amount':
						$where = "WHERE payment_amount=".((double)$search);
						break;
					case 'or_number':
						$where = "WHERE or_number LIKE '%".mysqli_real_escape_string($con,$search)."%'";
						break;
					case 'comments':
						$where = "WHERE ospos_purchase_payments.comments LIKE '%".mysqli_real_escape_string($con,$search)."%'";
						break;
					default: break;
				}
			}
			
			$count = mysqli_fetch_array(mysqli_query($con,"SELECT COUNT(*) count FROM ospos_purchase_payments LEFT JOIN ospos_purchases ON ospos_purchases.purchase_id=ospos_purchase_payments.purchase_id $where"));
			$total = $count['count'];
			$payments = mysqli_query($con,"
				SELECT
					*,
					ospos_purchase_payments.comments comments
				FROM ospos_purchase_payments
				LEFT JOIN ospos_purchases ON ospos_purchases.purchase_id=ospos_purchase_payments.purchase_id
				$where
				ORDER BY payment_date DESC
				LIMIT ".($page*$perpage).",$perpage");
			while($payment = mysqli_fetch_array($payments))
			{
				echo "<tr".($payment['mode']==-1?" style='background-color:#fdd'":"").">";
					echo "<td align='right'><b>".$payment['payment_id']."</b></td>";
					echo "<td align='right'>".$payment['po_number']."</td>";
					echo "<td align='center'>".$payment['payment_date']."</td>";
					echo "<td align='right'>".number_format($payment['payment_amount'],4,'.',',')."</td>";
					echo "<td>".$payment['or_number']."&nbsp;</td>";
					echo "<td>".$payment['comments']."&nbsp;</td>";
					echo "<td align='center'>";
						if($payment['mode']==-1)
							echo "<a href='?purchase&page=$page&perpage=$perpage&search=$search&type=$by&undelete=".$payment['payment_id']."'>Undo deletion</a>";
						else if($payment['mode']==0)
							echo "<a href='?purchase&page=$page&perpage=$perpage&search=$search&type=$by&delete=".$payment['payment_id']."'>Delete</a>";
					echo "</td>";
				echo "</tr>";
			}
			echo "</table>";
			echo "Page: ";
			for($x=0;$x<ceil($total/$perpage);$x++)
			{
				if($x==$page)
					echo "<b>".($x+1)."</b> ";
				else
					echo "<a href='?purchase&page=$x'>".($x+1)."</a> ";
			}
			if(ceil($total/$perpage)>1)
				echo "(<a href='?purchase&perpage=$total'>View All</a>)";
			echo "<br />";
			echo "Total found: $total";
		}
		else if(isset($_GET['logs']))
		{
			echo "<table border='1'>";
			echo "<tr>";
				echo "<th>Timestamp</th>";
				echo "<th>Employee</th>";
				echo "<th>Action Done</th>";
			echo "</tr>";
			$logs = mysqli_query($con,"SELECT * FROM ospos_user_logs LEFT JOIN ospos_people ON ospos_people.person_id=ospos_user_logs.person_id WHERE action LIKE '10%' OR action LIKE '20%' ORDER BY log_time DESC");
			while($log = mysqli_fetch_array($logs))
			{
				echo "<tr>";
					echo "<td>".$log['log_time']."</td>";
					echo "<td>".$log['first_name']." ".$log['last_name']."</td>";
					echo "<td>";
						$actions = str_getcsv($log['action']);
						switch($actions[0])
						{
							case 100:
								$payment_info = mysqli_fetch_array(mysqli_query($con,"SELECT * FROM ospos_receivable_payments LEFT JOIN ospos_receivables ON ospos_receivables.receivable_id=ospos_receivable_payments.receivable_id WHERE payment_id=".((int)$actions[1])));
								echo "Deleted payment for DR#".$payment_info['itr_number']." (Amount: ".$payment_info['payment_amount'].")";
								break;
							case 101:
								$payment_info = mysqli_fetch_array(mysqli_query($con,"SELECT * FROM ospos_receivable_payments LEFT JOIN ospos_receivables ON ospos_receivables.receivable_id=ospos_receivable_payments.receivable_id WHERE payment_id=".((int)$actions[1])));
								echo "Undo deletion of payment for DR#".$payment_info['itr_number']." (Amount: ".$payment_info['payment_amount'].")";
								break;
							case 200:
								$payment_info = mysqli_fetch_array(mysqli_query($con,"SELECT * FROM ospos_purchase_payments LEFT JOIN ospos_purchases ON ospos_purchases.purchase_id=ospos_purchase_payments.purchase_id WHERE payment_id=".((int)$actions[1])));
								echo "Deleted payment for PO#".$payment_info['po_number']." (Amount: ".$payment_info['payment_amount'].")";
								break;
							case 201:
								$payment_info = mysqli_fetch_array(mysqli_query($con,"SELECT * FROM ospos_purchase_payments LEFT JOIN ospos_purchases ON ospos_purchases.purchase_id=ospos_purchase_payments.purchase_id WHERE payment_id=".((int)$actions[1])));
								echo "Undo deletion of payment for PO#".$payment_info['po_number']." (Amount: ".$payment_info['payment_amount'].")";
								break;
						}
					echo "</td>";
				echo "</tr>";
			}
			echo "</table>";
		}
	}
	else
	{
		echo "No permission to access this facility. <a href='?logout'>Logout</a>";
	}
}
else
{
	if(isset($_POST['form_submit']))
	{
		$username = mysqli_real_escape_string($con,$_POST['username']);
		$password = md5($_POST['password']);
		$user = mysqli_fetch_array(mysqli_query($con,"SELECT * FROM ospos_employees WHERE username='$username' AND password='$password'"));
		if(isset($user['person_id']))
		{
			$_SESSION['isasys'] = $user['person_id'];
			header('location: payments.php');
		}
		else
		{
			echo "Error: Invalid username and/or password.";
		}
	}
	echo "<form action='?' method='post'>";
	echo "<table border='0'>";
	echo "<tr><td>Username:</td><td><input type='text' name='username' /></td></tr>";
	echo "<tr><td>Password:</td><td><input type='password' name='password' /></td></tr>";
	echo "<tr><td>&nbsp;</td><td><input type='submit' name='form_submit' value='Login' /></td></tr>";
	echo "</table>";
	echo "</form>";
}

mysqli_close($con);
?>
