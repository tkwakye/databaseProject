<?php
// Add this to your index.php in the table where you display events
// Replace the Action column <td> section with this updated version:
?>

<td>
    <div class="btn-group" role="group">
        <!-- Existing Edit button -->
        <form action="<?php $_SERVER['PHP_SELF'] ?>" method="post" class="d-inline">
            <input type="hidden" name="event_id_to_update" value="<?php echo $event['event_id']; ?>" />
            <button type="submit" name="updateBtn" class="btn btn-sm btn-outline-primary">Edit</button>
        </form>
        
        <!-- Existing Delete button -->
        <form action="<?php $_SERVER['PHP_SELF'] ?>" method="post" class="d-inline ms-1">
            <input type="hidden" name="event_id_to_delete" value="<?php echo $event['event_id']; ?>" />
            <button type="submit" name="deleteBtn" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?');">Delete</button>
        </form>
    </div>
    
    <!-- NEW: Add these buttons below the existing action buttons -->
    <div class="mt-1">
        <a href="event_images.php?event_id=<?php echo $event['event_id']; ?>" class="btn btn-sm btn-info">
            📷 Images
            <?php 
            $img_count = getImageCount($event['event_id']); 
            if ($img_count > 0) echo "($img_count)";
            ?>
        </a>
        <a href="event_feedback.php?event_id=<?php echo $event['event_id']; ?>" class="btn btn-sm btn-warning">
            ⭐ Feedback
            <?php 
            $feedback_stats = getFeedbackStats($event['event_id']); 
            if ($feedback_stats['feedback_count'] > 0) {
                echo "(" . number_format($feedback_stats['avg_rating'], 1) . ")";
            }
            ?>
        </a>
    </div>
</td>

<?php
/*
ALTERNATIVELY, if you want a cleaner look, replace the entire Action column section with this:

<td>
    <div class="d-grid gap-1">
        <div class="btn-group btn-group-sm" role="group">
            <form action="<?php $_SERVER['PHP_SELF'] ?>" method="post" class="d-inline">
                <input type="hidden" name="event_id_to_update" value="<?php echo $event['event_id']; ?>" />
                <button type="submit" name="updateBtn" class="btn btn-outline-primary">Edit</button>
            </form>
            <form action="<?php $_SERVER['PHP_SELF'] ?>" method="post" class="d-inline">
                <input type="hidden" name="event_id_to_delete" value="<?php echo $event['event_id']; ?>" />
                <button type="submit" name="deleteBtn" class="btn btn-outline-danger" onclick="return confirm('Are you sure?');">Delete</button>
            </form>
        </div>
        
        <div class="btn-group btn-group-sm" role="group">
            <a href="event_images.php?event_id=<?php echo $event['event_id']; ?>" class="btn btn-outline-info" title="View Images">
                📷 <?php $c = getImageCount($event['event_id']); if($c>0) echo $c; ?>
            </a>
            <a href="event_feedback.php?event_id=<?php echo $event['event_id']; ?>" class="btn btn-outline-warning" title="View Feedback">
                ⭐ <?php $s = getFeedbackStats($event['event_id']); if($s['feedback_count']>0) echo number_format($s['avg_rating'],1); ?>
            </a>
        </div>
    </div>
</td>
*/
?>