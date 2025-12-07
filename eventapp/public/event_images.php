<?php
include 'includes/header.php';
require 'includes/db.php';

// Get event_id from URL
$event_id = isset($_GET['event_id']) ? (int)$_GET['event_id'] : 0;

// Fetch event details
$stmt = $db->prepare("SELECT e.*, o.name as org_name, v.name as venue_name 
                      FROM Events e 
                      JOIN Organizations o ON e.org_id = o.org_id 
                      JOIN Venues v ON e.venue_id = v.venue_id 
                      WHERE e.event_id = :event_id");
$stmt->bindValue(':event_id', $event_id);
$stmt->execute();
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    echo "<p>Event not found.</p>";
    include 'includes/footer.php';
    exit;
}

// Handle image upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['event_image'])) {
    $upload_dir = 'uploads/';
    
    // Create uploads directory if it doesn't exist
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    $file = $_FILES['event_image'];
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    if ($file['error'] === 0) {
        if (in_array($file['type'], $allowed_types) && $file['size'] <= $max_size) {
            $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $new_filename = 'event_' . $event_id . '_' . time() . '.' . $file_extension;
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                // Insert into database
                $caption = $_POST['caption'] ?? '';
                $stmt = $db->prepare("INSERT INTO EventImages (event_id, image_path, caption, uploaded_at) 
                                     VALUES (:event_id, :image_path, :caption, NOW())");
                $stmt->bindValue(':event_id', $event_id);
                $stmt->bindValue(':image_path', $upload_path);
                $stmt->bindValue(':caption', $caption);
                $stmt->execute();
                
                $success_message = "Image uploaded successfully!";
            } else {
                $error_message = "Failed to upload image.";
            }
        } else {
            $error_message = "Invalid file type or size. Only JPG, PNG, GIF under 5MB allowed.";
        }
    } else {
        $error_message = "Error uploading file.";
    }
}

// Fetch all images for this event
$stmt = $db->prepare("SELECT * FROM EventImages WHERE event_id = :event_id ORDER BY uploaded_at DESC");
$stmt->bindValue(':event_id', $event_id);
$stmt->execute();
$images = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-4">
    <h2><?php echo htmlspecialchars($event['title']); ?> - Images</h2>
    <p><strong>Organization:</strong> <?php echo htmlspecialchars($event['org_name']); ?></p>
    <p><strong>Date:</strong> <?php echo date('M j, Y g:i A', strtotime($event['start_datetime'])); ?></p>
    
    <?php if (isset($success_message)): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>
    
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>
    
    <!-- Upload Form -->
    <div class="card mb-4">
        <div class="card-header">
            <h4>Upload Event Image</h4>
        </div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="event_image" class="form-label">Select Image (JPG, PNG, GIF - Max 5MB)</label>
                    <input type="file" class="form-control" id="event_image" name="event_image" accept="image/*" required>
                </div>
                <div class="mb-3">
                    <label for="caption" class="form-label">Caption (Optional)</label>
                    <input type="text" class="form-control" id="caption" name="caption" maxlength="255">
                </div>
                <button type="submit" class="btn btn-primary">Upload Image</button>
                <a href="event_view.php?event_id=<?php echo $event_id; ?>" class="btn btn-secondary">Back to Event</a>
            </form>
        </div>
    </div>
    
    <!-- Display Images -->
    <div class="card">
        <div class="card-header">
            <h4>Event Images (<?php echo count($images); ?>)</h4>
        </div>
        <div class="card-body">
            <?php if (empty($images)): ?>
                <p>No images uploaded yet.</p>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($images as $image): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card">
                                <img src="<?php echo htmlspecialchars($image['image_path']); ?>" 
                                     class="card-img-top" 
                                     alt="Event Image"
                                     style="height: 250px; object-fit: cover;">
                                <div class="card-body">
                                    <?php if ($image['caption']): ?>
                                        <p class="card-text"><?php echo htmlspecialchars($image['caption']); ?></p>
                                    <?php endif; ?>
                                    <small class="text-muted">
                                        Uploaded: <?php echo date('M j, Y g:i A', strtotime($image['uploaded_at'])); ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>