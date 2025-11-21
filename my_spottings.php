<?php
require_once 'includes/auth.php';
require_once 'includes/config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

// Fetch user's sightings
$stmt = $pdo->prepare("
    SELECT s.*, t.model as train_model 
    FROM sightings s 
    LEFT JOIN trains t ON s.train_id = t.id 
    WHERE s.user_id = ? 
    ORDER BY s.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$sightings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'includes/header.php'; ?>

<h1>My Spottings</h1>

<?php if (empty($sightings)): ?>
    <div class="alert alert-info">
        You haven't submitted any train sightings yet. 
        <a href="submit_sighting.php" class="alert-link">Submit your first one now!</a>
    </div>
<?php else: ?>
    <div class="row">
        <?php foreach ($sightings as $sighting): ?>
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($sighting['train_model']); ?></h5>
                    <p class="card-text">
                        <strong>Location:</strong> <?php echo htmlspecialchars($sighting['location']); ?><br>
                        <strong>Date:</strong> <?php echo date('d.m.Y', strtotime($sighting['date'])); ?><br>
                        <?php if ($sighting['notes']): ?>
                            <strong>Notes:</strong> <?php echo htmlspecialchars($sighting['notes']); ?>
                        <?php endif; ?>
                    </p>
                    <div class="btn-group">
                        <a href="sighting.php?id=<?php echo $sighting['id']; ?>" class="btn btn-primary">View</a>
                        <a href="#" class="btn btn-outline-secondary">Edit</a>
                        <a href="#" class="btn btn-outline-danger">Delete</a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>