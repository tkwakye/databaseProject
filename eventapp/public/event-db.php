<?php
function addEvent($title, $description, $org_id, $venue_id, $start_datetime, $max_attendees)
{
    global $db;
    // Defaulting created_by_user_id to 1 for this prototype
    $query = "INSERT INTO Events 
              (title, description, org_id, created_by_user_id, venue_id, start_datetime, max_attendees, status) 
              VALUES (:title, :description, :org_id, 1, :venue_id, :start_datetime, :max_attendees, 'published')";
    
    try {
        $statement = $db->prepare($query);
        $statement->bindValue(':title', $title);
        $statement->bindValue(':description', $description);
        $statement->bindValue(':org_id', $org_id);
        $statement->bindValue(':venue_id', $venue_id);
        $statement->bindValue(':start_datetime', $start_datetime);
        $statement->bindValue(':max_attendees', $max_attendees);
        $statement->execute();
        $statement->closeCursor();
    } catch (PDOException $e) {
        if ($statement->rowCount() == 0) echo "Failed to add a record <br/>";
    }
}

function updateEvent($event_id, $title, $description, $org_id, $venue_id, $start_datetime, $max_attendees)
{
    global $db;
    $query = "UPDATE Events 
              SET title = :title, 
                  description = :description, 
                  org_id = :org_id,
                  venue_id = :venue_id,
                  start_datetime = :start_datetime, 
                  max_attendees = :max_attendees 
              WHERE event_id = :event_id";
    
    $statement = $db->prepare($query);
    $statement->bindValue(':event_id', $event_id);
    $statement->bindValue(':title', $title);
    $statement->bindValue(':description', $description);
    $statement->bindValue(':org_id', $org_id);
    $statement->bindValue(':venue_id', $venue_id);
    $statement->bindValue(':start_datetime', $start_datetime);
    $statement->bindValue(':max_attendees', $max_attendees);
    $statement->execute();
    $statement->closeCursor();
}

function deleteEvent($event_id)
{
    global $db;
    $query = "DELETE FROM Events WHERE event_id = :event_id";
    $statement = $db->prepare($query);
    $statement->bindValue(':event_id', $event_id);
    $statement->execute();
    $statement->closeCursor();
}

function getEventById($id)  
{
    global $db;
    $query = "SELECT * FROM Events WHERE event_id = :id";
    $statement = $db->prepare($query);
    $statement->bindValue(':id', $id);
    $statement->execute();
    $result = $statement->fetch();
    $statement->closeCursor();
    return $result;
}

/**
 * Advanced Fetch with Search, Filter, and Sort
 */
function getEventsWithFilters($keyword, $filter_org, $filter_venue, $sort_by)
{
    global $db;

    // Base query [cite: 699-710]
    $query = "
        SELECT 
            e.event_id,
            e.title, 
            e.description,
            e.start_datetime, 
            e.max_attendees,
            e.status,
            o.name AS organization, 
            v.name AS venue_name,
            CASE 
                WHEN e.end_datetime < NOW() THEN 'Expired' 
                ELSE 'Current' 
            END AS event_state
        FROM Events e
        JOIN Organizations o ON e.org_id = o.org_id
        LEFT JOIN Venues v ON e.venue_id = v.venue_id
        WHERE 1=1
    ";

    // Dynamic Filtering
    $params = [];

    if (!empty($keyword)) {
        $query .= " AND (e.title LIKE :keyword OR e.description LIKE :keyword)";
        $params[':keyword'] = '%' . $keyword . '%';
    }

    if (!empty($filter_org)) {
        $query .= " AND e.org_id = :org_id";
        $params[':org_id'] = $filter_org;
    }

    if (!empty($filter_venue)) {
        $query .= " AND e.venue_id = :venue_id";
        $params[':venue_id'] = $filter_venue;
    }

    // Dynamic Sorting 
    switch ($sort_by) {
        case 'title':
            $query .= " ORDER BY e.title ASC";
            break;
        case 'org':
            $query .= " ORDER BY o.name ASC";
            break;
        case 'capacity':
            $query .= " ORDER BY e.max_attendees DESC";
            break;
        case 'date':
        default:
            $query .= " ORDER BY e.start_datetime ASC"; // Default to earliest events first [cite: 710]
            break;
    }

    $statement = $db->prepare($query);
    $statement->execute($params);
    $results = $statement->fetchAll();
    $statement->closeCursor();
    return $results;
}

// Helpers for Dropdowns
function getAllOrgs() {
    global $db;
    $query = "SELECT org_id, name FROM Organizations";
    $statement = $db->prepare($query);
    $statement->execute();
    return $statement->fetchAll();
}

function getAllVenues() {
    global $db;
    $query = "SELECT venue_id, name FROM Venues";
    $statement = $db->prepare($query);
    $statement->execute();
    return $statement->fetchAll();
}
?>
