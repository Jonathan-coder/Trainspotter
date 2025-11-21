<?php
require_once 'includes/auth.php';
require_once 'includes/config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $train_id = $_POST['train_id'];
    $location = $_POST['location'];
    $date = $_POST['date'];
    $notes = $_POST['notes'];
    
    // Basic file upload handling (simplified for now)
    $photo_path = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'assets/uploads/';
        $photo_name = time() . '_' . basename($_FILES['photo']['name']);
        $photo_path = $upload_dir . $photo_name;
        
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $photo_path)) {
            // File uploaded successfully
        }
    }
    
    // Insert into database
    $stmt = $pdo->prepare("INSERT INTO sightings (user_id, train_id, location, date, notes, photo_path) VALUES (?, ?, ?, ?, ?, ?)");
    
    if ($stmt->execute([$_SESSION['user_id'], $train_id, $location, $date, $notes, $photo_path])) {
        $success = 'Train sighting submitted successfully!';
    } else {
        $error = 'Failed to submit sighting!';
    }
}

// Fetch trains for dropdown
$trains = $pdo->query("SELECT * FROM trains ORDER BY model")->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'includes/header.php'; ?>

<h1>Submit Train Sighting</h1>

<?php if ($success): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="train_id" class="form-label">Train Model</label>
                        <select class="form-select" id="train_id" name="train_id" required>
                            <option value="">Select a train model</option>
                            <?php foreach ($trains as $train): ?>
                                <option value="<?php echo $train['id']; ?>"><?php echo htmlspecialchars($train['model']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="location" class="form-label">Location</label>
                        <input type="text" class="form-control" id="location" name="location" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="date" class="form-label">Date Spotted</label>
                        <input type="date" class="form-control" id="date" name="date" required>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="photo" class="form-label">Train Photo</label>
                        <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                        <div class="form-text">Upload a photo of the train (JPG, PNG, WEBP, max 5MB)</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Additional Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="4"></textarea>
                    </div>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary">Submit Sighting</button>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>