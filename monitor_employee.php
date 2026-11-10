<?php
require 'config.php';
$empID = $_GET['empID'] ?? '';

page_start('Attendance Monitoring (By Employee)');
?>
<div class="links"><a href="index.php">Back to Menu</a></div>
<form method="get">
  Input Employee #: <input type="number" name="empID" value="<?= h($empID) ?>" required>
  <button type="submit">Search</button>
</form>
<?php
if ($empID !== '') {
    $s = $pdo->prepare("SELECT e.*, d.depName FROM Employees e JOIN Departments d ON d.depCode = e.depCode WHERE e.empID = ?");
    $s->execute([$empID]);
    $emp = $s->fetch();

    if (!$emp) {
        echo "<p class='err'>Employee not found.</p>";
    } else {
        echo "<p><b>Name:</b> " . h($emp['empLName'] . ', ' . $emp['empFName']) .
             " &nbsp;&nbsp; <b>Department:</b> " . h($emp['depName']) . "</p>";

       
        $q = $pdo->prepare("SELECT * FROM AttendanceTbl WHERE empID=? AND attStat <> 'Cancelled' ORDER BY attTimeIn");
        $q->execute([$empID]);

        echo '<table><tr><th>Record #</th><th>Emp. ID</th><th>Date/Time In</th><th>Date/Time Out</th><th>Total</th></tr>';
        $totalHours = 0;
        foreach ($q as $a) {
            $hrs = (strtotime($a['attTimeOut']) - strtotime($a['attTimeIn'])) / 3600;
            $totalHours += $hrs;
            echo "<tr><td>" . h($a['attRN']) . "</td><td>" . h($a['empID']) . "</td>
            <td>" . date('n/j/y g:iA', strtotime($a['attTimeIn'])) . "</td>
            <td>" . date('n/j/y g:iA', strtotime($a['attTimeOut'])) . "</td>
            <td>" . number_format($hrs, 2) . "</td></tr>";
        }
        echo '</table>';

        $salary = $totalHours * $emp['empRPH'];
        echo "<p><b>Rate Per Hour:</b> " . number_format($emp['empRPH'], 2) . "<br>
              <b>Total Hours Worked:</b> " . number_format($totalHours, 2) . "<br>
              <b>Salary:</b> " . number_format($salary, 2) . "<br>
              <b>Date Generated:</b> " . date('n/j/Y') . "</p>";
    }
}
page_end();
