<?php
require 'config.php';
$mode = $_GET['mode'] ?? 'list';   
$error = '';


if (isset($_GET['del'])) {
    try {
        $pdo->prepare("DELETE FROM Departments WHERE depCode=?")->execute([$_GET['del']]);
    } catch (PDOException $e) {
        $error = "Cannot delete: department still has employees.";
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = (int)$_POST['depCode'];
    try {
        if ($_POST['mode'] === 'add') {
            $pdo->prepare("INSERT INTO Departments (depCode,depName,depHead,depTelNo) VALUES (?,?,?,?)")
                ->execute([$code, $_POST['depName'], $_POST['depHead'], $_POST['depTelNo']]);
        } else {
            $pdo->prepare("UPDATE Departments SET depName=?, depHead=?, depTelNo=? WHERE depCode=?")
                ->execute([$_POST['depName'], $_POST['depHead'], $_POST['depTelNo'], $code]);
        }
        header('Location: departments.php');
        exit;
    } catch (PDOException $e) {
        $error = "Save failed (duplicate code?): " . $e->getMessage();
        $mode = $_POST['mode'];
    }
}

page_start('Departments Management');
if ($error) echo "<p class='err'>" . h($error) . "</p>";

if ($mode === 'add' || $mode === 'edit') {
    $row = ['depCode'=>'','depName'=>'','depHead'=>'','depTelNo'=>''];
    if ($mode === 'edit') {
        $s = $pdo->prepare("SELECT * FROM Departments WHERE depCode=?");
        $s->execute([$_GET['code']]);
        $row = $s->fetch() ?: $row;
    }
    ?>
    <form class="box" method="post">
      <input type="hidden" name="mode" value="<?= h($mode) ?>">
      <label>Code</label>
      <input type="number" name="depCode" value="<?= h($row['depCode']) ?>" required <?= $mode==='edit' ? 'readonly' : '' ?>>
      <label>Name</label><input name="depName" value="<?= h($row['depName']) ?>" required>
      <label>Head</label><input name="depHead" value="<?= h($row['depHead']) ?>" required>
      <label>Tel. No.</label><input name="depTelNo" value="<?= h($row['depTelNo']) ?>" required>
      <p><button type="submit">Save</button> <a href="departments.php">Cancel</a></p>
    </form>
    <?php
} else {
    echo '<div class="links"><a href="departments.php?mode=add">Add a Department Here</a> | <a href="index.php">Back to Menu</a></div>';
    echo '<table><tr><th>Code</th><th>Name</th><th>Head</th><th>Tel. No.</th><th>Actions</th></tr>';
    foreach ($pdo->query("SELECT * FROM Departments ORDER BY depCode") as $d) {
        echo "<tr><td>" . h($d['depCode']) . "</td><td>" . h($d['depName']) . "</td><td>" . h($d['depHead']) . "</td><td>" . h($d['depTelNo']) . "</td>
        <td><a href='departments.php?mode=edit&code=" . h($d['depCode']) . "'>Edit</a> |
        <a href='departments.php?del=" . h($d['depCode']) . "' onclick=\"return confirm('Delete this department?')\">Del</a></td></tr>";
    }
    echo '</table>';
}
page_end();
