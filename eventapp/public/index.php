<?php 
require('connect-db.php');
require('event-db.php');

// --- 1. Handle POST Requests (Add, Update, Delete) ---
$event_id_to_update = '';
$title_to_show = '';
$desc_to_show = '';
$time_to_show = '';
$capacity_to_show = '';
$org_to_show = ''; 
$venue_to_show = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    if (isset($_POST['addBtn']))
    {
        addEvent($_POST['title'], $_POST['description'], $_POST['org_id'], $_POST['venue_id'], $_POST['start_datetime'], $_POST['max_attendees']);
    }
    else if (isset($_POST['deleteBtn']))
    {
        deleteEvent($_POST['event_id_to_delete']);
    }
    else if (isset($_POST['updateBtn']))
    {
        $event_info = getEventById($_POST['event_id_to_update']);
        $event_id_to_update = $event_info['event_id'];
        $title_to_show = $event_info['title'];
        $desc_to_show = $event_info['description'];
        $time_to_show = $event_info['start_datetime'];
        $capacity_to_show = $event_info['max_attendees'];
        $org_to_show = $event_info['org_id'];
        $venue_to_show = $event_info['venue_id'];
    }
    else if (isset($_POST['confirmUpdateBtn']))
    {
        updateEvent($_POST['event_id'], $_POST['title'], $_POST['description'], $_POST['org_id'], $_POST['venue_id'], $_POST['start_datetime'], $_POST['max_attendees']);
    }
}

// --- 2. Handle GET Requests (Search, Filter, Sort) ---
$search_keyword = isset($_GET['search']) ? $_GET['search'] : '';
$filter_org = isset($_GET['filter_org']) ? $_GET['filter_org'] : '';
$filter_venue = isset($_GET['filter_venue']) ? $_GET['filter_venue'] : '';
$sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'date';

// Fetch lists for dropdowns
$orgs_list = getAllOrgs();
$venues_list = getAllVenues();

// Fetch filtered events
$list_of_events = getEventsWithFilters($search_keyword, $filter_org, $filter_venue, $sort_by);
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">    
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Campus Event Management</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">  
  <link rel="stylesheet" href="maintenance-system.css">  
</head>

<body>  
<?php include('header.php'); ?>

<div class="container mt-4">

  <div class="card bg-light mb-4">
    <div class="card-body">
      <h4>Find Events</h4>
      <form action="index.php" method="get" class="row g-3">
        <div class="col-md-3">
            <input type="text" class="form-control" name="search" placeholder="Search title or description..." value="<?php echo htmlspecialchars($search_keyword); ?>">
        </div>
        
        <div class="col-md-3">
            <select class="form-select" name="filter_org">
                <option value="">All Organizations</option>
                <?php foreach ($orgs_list as $org): ?>
                    <option value="<?php echo $org['org_id']; ?>" <?php if ($filter_org == $org['org_id']) echo 'selected'; ?>>
                        <?php echo $org['name']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-2">
            <select class="form-select" name="filter_venue">
                <option value="">All Venues</option>
                <?php foreach ($venues_list as $venue): ?>
                    <option value="<?php echo $venue['venue_id']; ?>" <?php if ($filter_venue == $venue['venue_id']) echo 'selected'; ?>>
                        <?php echo $venue['name']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-2">
            <select class="form-select" name="sort">
                <option value="date" <?php if ($sort_by == 'date') echo 'selected'; ?>>Sort by Date</option>
                <option value="title" <?php if ($sort_by == 'title') echo 'selected'; ?>>Sort by Title</option>
                <option value="org" <?php if ($sort_by == 'org') echo 'selected'; ?>>Sort by Organization</option>
                <option value="capacity" <?php if ($sort_by == 'capacity') echo 'selected'; ?>>Sort by Size</option>
            </select>
        </div>

        <div class="col-md-2 d-grid">
            <button type="submit" class="btn btn-primary">Filter</button>
        </div>
      </form>
    </div>
  </div>

  <hr/>

  <h3>Upcoming Events</h3>
  <div class="table-responsive">
  <table class="table table-striped table-hover align-middle">
    <thead class="table-dark">
      <tr>
        <th>Date</th>
        <th>Title</th>        
        <th>Organization</th> 
        <th>Venue</th>
        <th>Status</th>        
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
    <?php if (empty($list_of_events)): ?>
        <tr><td colspan="6" class="text-center">No events found matching your criteria.</td></tr>
    <?php else: ?>
        <?php foreach ($list_of_events as $event): ?>
          <tr>
            <td><?php echo date("M j, Y g:i A", strtotime($event['start_datetime'])); ?></td>
            <td>
                <strong><?php echo $event['title']; ?></strong><br/>
                <small class="text-muted"><?php echo substr($event['description'], 0, 50) . '...'; ?></small>
            </td>
            <td><?php echo $event['organization']; ?></td>
            <td><?php echo $event['venue_name']; ?></td>
            <td>
                <span class="badge <?php echo ($event['event_state'] == 'Current') ? 'bg-success' : 'bg-secondary'; ?>">
                    <?php echo $event['event_state']; ?>
                </span>
            </td>
            <td>
              <div class="btn-group" role="group">
                  <form action="<?php $_SERVER['PHP_SELF'] ?>" method="post" class="d-inline">
                      <input type="hidden" name="event_id_to_update" value="<?php echo $event['event_id']; ?>" />
                      <button type="submit" name="updateBtn" class="btn btn-sm btn-outline-primary">Edit</button>
                  </form>
                  <form action="<?php $_SERVER['PHP_SELF'] ?>" method="post" class="d-inline ms-1">
                      <input type="hidden" name="event_id_to_delete" value="<?php echo $event['event_id']; ?>" />
                      <button type="submit" name="deleteBtn" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?');">Delete</button>
                  </form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
  </table>
  </div>

  <hr/>

  <div class="row g-3 mt-4 mb-5">
    <div class="col-md-12">
      <div class="card border-secondary">
        <div class="card-header bg-secondary text-white">
            <?php echo $event_id_to_update ? 'Edit Event' : 'Create New Event'; ?>
        </div>
        <div class="card-body">
            <form method="post" action="<?php $_SERVER['PHP_SELF'] ?>">
                <input type="hidden" name="event_id" value="<?php echo $event_id_to_update; ?>" />
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Event Title:</label>
                        <input type="text" class="form-control" name="title" required value="<?php echo $title_to_show; ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Start Date & Time:</label>
                        <input type="datetime-local" class="form-control" name="start_datetime" required value="<?php echo $time_to_show; ?>">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                         <label class="form-label">Organization:</label>
                         <select class="form-select" name="org_id">
                            <?php foreach ($orgs_list as $org): ?>
                                <option value="<?php echo $org['org_id']; ?>" <?php if ($org_to_show == $org['org_id']) echo 'selected'; ?>>
                                    <?php echo $org['name']; ?>
                                </option>
                            <?php endforeach; ?>
                         </select>
                    </div>
                    <div class="col-md-6">
                         <label class="form-label">Venue:</label>
                         <select class="form-select" name="venue_id">
                            <?php foreach ($venues_list as $venue): ?>
                                <option value="<?php echo $venue['venue_id']; ?>" <?php if ($venue_to_show == $venue['venue_id']) echo 'selected'; ?>>
                                    <?php echo $venue['name']; ?>
                                </option>
                            <?php endforeach; ?>
                         </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description:</label>
                    <textarea class="form-control" name="description" rows="2"><?php echo $desc_to_show; ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Max Attendees:</label>
                    <input type="number" class="form-control" name="max_attendees" value="<?php echo $capacity_to_show; ?>">
                </div>

                <div class="d-grid gap-2 d-md-block">
                    <?php if ($event_id_to_update): ?>
                        <input type="submit" value="Confirm Update" name="confirmUpdateBtn" class="btn btn-primary" />
                        <a href="index.php" class="btn btn-secondary">Cancel</a>
                    <?php else: ?>
                        <input type="submit" value="Add Event" name="addBtn" class="btn btn-dark" />
                    <?php endif; ?>
                </div>
            </form>
        </div>
      </div>
    </div>  
  </div>

</div>
<?php include('footer.html'); ?>
</body>
</html>
