<?php
require_once 'includes/auth.php';
require_once 'includes/config.php';

// Fetch sightings from database
$stmt = $pdo->query("
    SELECT s.*, u.username, t.model as train_model 
    FROM sightings s 
    LEFT JOIN users u ON s.user_id = u.id 
    LEFT JOIN trains t ON s.train_id = t.id 
    ORDER BY s.created_at DESC 
    LIMIT 12
");
$sightings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'includes/header.php'; ?>

<h1>Train Gallery</h1>
<p class="lead">Browse recent train sightings from our community.</p>

<div class="row">
    <?php if (empty($sightings)): ?>
        <div class="col-12">
            <div class="alert alert-info">No train sightings yet. Be the first to <a href="submit_sighting.php">submit one</a>!</div>
        </div>
    <?php else: ?>
        <?php foreach ($sightings as $sighting): ?>
        <div class="col-md-4 mb-4">
            <div class="card">
                <img src="<?php echo $sighting['photo_path'] ?: 'https://via.placeholder.com/400x250/3498db/FFFFFF?text=' . urlencode($sighting['train_model']); ?>" class="card-img-top" alt="Train photo">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($sighting['train_model']); ?></h5>
                    <p class="card-text">
                        <strong>Location:</strong> <?php echo htmlspecialchars($sighting['location']); ?><br>
                        <strong>Spotted by:</strong> <?php echo htmlspecialchars($sighting['username']); ?>
                    </p>
                    <a href="sighting.php?id=<?php echo $sighting['id']; ?>" class="btn btn-primary">View Details</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>