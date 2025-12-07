<?php
// public/add_event.php
require_once '../includes/functions.php';
session_start();

// OPTIONAL: restrict to admins only
if ($_SESSION['role'] !== 'admin') { die("Access denied"); }

$orgs = getAllOrgs();
$venues = getAllVenues();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $org_id = $_POST['org_id'];
    $venue_id = $_POST['venue_id'];
    $start_datetime = $_POST['start_datetime'];
    $max_attendees = $_POST['max_attendees'];

    addEvent($title, $description, $org_id, $venue_id, $start_datetime, $max_attendees);

    header("Location: events_list.php?success=1");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Event</title>
</head>
<body>

<h2>Add Event</h2>

<form method="POST">

    <label>Title:</label><br>
    <input type="text" name="title" required><br><br>

    <label>Description:</label><br>
    <textarea name="description" required></textarea><br><br>

    <label>Organization:</label><br>
    <select name="org_id" required>
        <?php foreach ($orgs as $o): ?>
            <option value="<?= $o['org_id'] ?>"><?= $o['name'] ?></option>
        <?php endforeach; ?>
    </select><br><br>

    <label>Venue:</label><br>
    <select name="venue_id" required>
        <?php foreach ($venues as $v): ?>
            <option value="<?= $v['venue_id'] ?>"><?= $v['name'] ?></option>
        <?php endforeach; ?>
    </select><br><br>

    <label>Start Date & Time:</label><br>
    <input type="datetime-local" name="start_datetime" required><br><br>

    <label>Max Attendees:</label><br>
    <input type="number" name="max_attendees" min="1" required><br><br>

    <button type="submit">Add Event</button>

</form>

</body>
</html>
