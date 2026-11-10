<?php
require 'config.php';
$from = $_GET['from'] ?? '';
$to   = $_GET['to'] ?? '';

page_start('Attendance Monitoring (Date Range)');
?>
<div class="links"><a href="index.php">Back to Menu</a></div>
<form method="get">
  Date From: <input type="date" name="from" value="<?= h($from) ?>" required>
  Date To: <input type="date" name="to" value="<?= h($to) ?>" required>
  <button type="submit">Generate</button>
</form>
<?php
if ($from !== '' && $to !== '') {
    $q = $pdo->prepare("SELECT * FROM AttendanceTbl
                        WHERE attDate BETWEEN ? AND ? AND attStat <> 'Cancelled'
                        ORDER BY attDate, attTimeIn");
    $q->execute([$from, $to]);

    echo '<h3>Attendance Records</h3>';
    echo '<table><tr><th>Record #</th><th>Emp. ID</th><th>Date/Time In</th><th>Date/Time Out</th><th>Total</th></tr>';
    $grand = 0;
    foreach ($q as $a) {
        $hrs = (strtotime($a['attTimeOut']) - strtotime($a['attTimeIn'])) / 3600;
        $grand += $hrs;
        echo "<tr><td>" . h($a['attRN']) . "</td><td>" . h($a['empID']) . "</td>
        <td>" . date('n/j/y g:iA', strtotime($a['attTimeIn'])) . "</td>
        <td>" . date('n/j/y g:iA', strtotime($a['attTimeOut'])) . "</td>
        <td>" . number_format($hrs, 2) . "</td></tr>";
    }
    echo '</table>';
    echo "<p><b>Date Generated:</b> " . date('n/j/Y') . " &nbsp;&nbsp; <b>Total:</b> " . number_format($grand, 2) . "</p>";
}
page_end();
