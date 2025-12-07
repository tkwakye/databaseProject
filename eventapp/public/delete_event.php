<?php
session_start();
require_once '../includes/event-db.php';

// ADMIN PROTECTION
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Unauthorized: Admins only.");
}

$event_id = $_GET['id'] ?? null;

if (!$event_id) {
    die("Missing event ID.");
}

deleteEvent($event_id);

header("Location: events_list.php");
exit;
