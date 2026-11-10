<?php
require 'config.php';
$mode = $_GET['mode'] ?? 'list';   
$error = '';


if (isset($_GET['del'])) {
    try {
        $pdo->prepare("DELETE FROM Employees WHERE empID=?")->execute([$_GET['del']]);
    } catch (PDOException $e) {
        $error = "Cannot delete: employee has attendance records.";
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if ($_POST['mode'] === 'add') {
            $pdo->prepare("INSERT INTO Employees (empID,depCode,empFName,empLName,empRPH) VALUES (?,?,?,?,?)")
                ->execute([$_POST['empID'], $_POST['depCode'], $_POST['empFName'], $_POST['empLName'], $_POST['empRPH']]);
        } else {
            $pdo->prepare("UPDATE Employees SET depCode=?, empFName=?, empLName=?, empRPH=? WHERE empID=?")
                ->execute([$_POST['depCode'], $_POST['empFName'], $_POST['empLName'], $_POST['empRPH'], $_POST['empID']]);
        }
        header('Location: employees.php');
        exit;
    } catch (PDOException $e) {
        $error = "Save failed (duplicate ID?): " . $e->getMessage();
        $mode = $_POST['mode'];
    }
}

page_start('Employees Management');
if ($error) echo "<p class='err'>" . h($error) . "</p>";

if ($mode === 'add' || $mode === 'edit') {
    $row = ['empID'=>'','depCode'=>'','empFName'=>'','empLName'=>'','empRPH'=>''];
    if ($mode === 'edit') {
        $s = $pdo->prepare("SELECT * FROM Employees WHERE empID=?");
        $s->execute([$_GET['id']]);
        $row = $s->fetch() ?: $row;
    }
    $deps = $pdo->query("SELECT depCode, depName FROM Departments ORDER BY depCode")->fetchAll();
    ?>
    <form class="box" method="post">
      <input type="hidden" name="mode" value="<?= h($mode) ?>">
      <label>ID</label>
      <input type="number" name="empID" value="<?= h($row['empID']) ?>" required <?= $mode==='edit' ? 'readonly' : '' ?>>
      <label>Department</label>
      <select name="depCode" required>
        <?php foreach ($deps as $d): ?>
          <option value="<?= h($d['depCode']) ?>" <?= $d['depCode']==$row['depCode'] ? 'selected' : '' ?>>
            <?= h($d['depCode'] . ' - ' . $d['depName']) ?></option>
        <?php endforeach; ?>
      </select>
      <label>Last Name</label><input name="empLName" value="<?= h($row['empLName']) ?>" required>
      <label>First Name</label><input name="empFName" value="<?= h($row['empFName']) ?>" required>
      <label>Rate / Hour</label><input type="number" step="0.01" name="empRPH" value="<?= h($row['empRPH']) ?>" required>
      <p><button type="submit">Save</button> <a href="employees.php">Cancel</a></p>
    </form>
    <?php
} else {
    echo '<div class="links"><a href="employees.php?mode=add">Add an Employee Here</a> | <a href="index.php">Back to Menu</a></div>';
    echo '<table><tr><th>ID</th><th>Dept.</th><th>Lastname</th><th>Firstname</th><th>Rate/Hour</th><th>Actions</th></tr>';
    foreach ($pdo->query("SELECT * FROM Employees ORDER BY empID") as $e) {
        echo "<tr><td>" . h($e['empID']) . "</td><td>" . h($e['depCode']) . "</td><td>" . h($e['empLName']) . "</td><td>" . h($e['empFName']) . "</td><td>" . number_format($e['empRPH'], 2) . "</td>
        <td><a href='employees.php?mode=edit&id=" . h($e['empID']) . "'>Edit</a> |
        <a href='employees.php?del=" . h($e['empID']) . "' onclick=\"return confirm('Delete this employee?')\">Del</a></td></tr>";
    }
    echo '</table>';
}
page_end();
