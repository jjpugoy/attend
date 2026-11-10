<?php
require 'config.php';
$mode = $_GET['mode'] ?? 'list';  
$error = '';


if (isset($_GET['cancel'])) {
    $pdo->prepare("UPDATE AttendanceTbl SET attStat='Cancelled' WHERE attRN=?")->execute([$_GET['cancel']]);
    header('Location: attendance.php');
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $in  = str_replace('T', ' ', $_POST['attTimeIn']);
    $out = str_replace('T', ' ', $_POST['attTimeOut']);
    if (strtotime($out) <= strtotime($in)) {
        $error = "Time Out must be later than Time In.";
        $mode = 'add';
    } else {
        try {
            $pdo->prepare("INSERT INTO AttendanceTbl (empID, attDate, attTimeIn, attTimeOut, attStat) VALUES (?,?,?,?,'Active')")
                ->execute([$_POST['empID'], date('Y-m-d', strtotime($in)), $in, $out]);
            header('Location: attendance.php');
            exit;
        } catch (PDOException $e) {
            $error = "Save failed (does the employee ID exist?).";
            $mode = 'add';
        }
    }
}

page_start('Attendance Recording');
if ($error) echo "<p class='err'>" . h($error) . "</p>";

if ($mode === 'add') {
    $emps = $pdo->query("SELECT empID, empFName, empLName FROM Employees ORDER BY empID")->fetchAll();
    ?>
    <form class="box" method="post">
      <label>Employee</label>
      <select name="empID" required>
        <?php foreach ($emps as $e): ?>
          <option value="<?= h($e['empID']) ?>"><?= h($e['empID'] . ' - ' . $e['empLName'] . ', ' . $e['empFName']) ?></option>
        <?php endforeach; ?>
      </select>
      <label>Date/Time In</label><input type="datetime-local" name="attTimeIn" required>
      <label>Date/Time Out</label><input type="datetime-local" name="attTimeOut" required>
      <p><button type="submit">Save</button> <a href="attendance.php">Cancel</a></p>
    </form>
    <?php
} else {
    echo '<div class="links"><a href="attendance.php?mode=add">Record Attendance Here</a> | <a href="index.php">Back to Menu</a></div>';
    echo '<table><tr><th>Record #</th><th>Emp. ID</th><th>Date/Time In</th><th>Date/Time Out</th><th>Status</th><th>Action</th></tr>';
    foreach ($pdo->query("SELECT * FROM AttendanceTbl ORDER BY attRN") as $a) {
        $cancelled = $a['attStat'] === 'Cancelled';
        echo "<tr><td>" . h($a['attRN']) . "</td><td>" . h($a['empID']) . "</td>
        <td>" . date('n/j/y g:iA', strtotime($a['attTimeIn'])) . "</td>
        <td>" . date('n/j/y g:iA', strtotime($a['attTimeOut'])) . "</td>
        <td>" . h($a['attStat']) . "</td><td>";
        echo $cancelled ? '-' : "<a href='attendance.php?cancel=" . h($a['attRN']) . "' onclick=\"return confirm('Cancel this record?')\">Cancel</a>";
        echo "</td></tr>";
    }
    echo '</table>';
}
page_end();
