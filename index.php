<?php
require_once 'includes/auth.php';
require_once 'includes/config.php';

// Featured sightings für Homepage
$featured_sightings = $pdo->query("
    SELECT s.*, u.username, t.model as train_model 
    FROM sightings s 
    LEFT JOIN users u ON s.user_id = u.id 
    LEFT JOIN trains t ON s.train_id = t.id 
    WHERE s.is_featured = TRUE  -- Nur featured Posts anzeigen
    ORDER BY s.created_at DESC 
    LIMIT 3
")->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'includes/header.php'; ?>

<!-- Hero Section -->
<div class="hero-section text-center">
    <div class="mt-4">
        <h1 class="display-4 fw-bold">🚆 TrainSpotterLog</h1>
        <p class="lead">The social platform for train enthusiasts to share their sightings and connect with other railfans.</p>
        
        <?php if (!isLoggedIn()): ?>
        <div class="mt-4">
            <a href="register.php" class="btn btn-light btn-lg me-3">Join Our Community</a>
            <a href="gallery.php" class="btn btn-outline-light btn-lg">Browse Gallery</a>
        </div>
        <?php else: ?>
        <div class="mt-4">
            <a href="submit_sighting.php" class="btn btn-light btn-lg me-3">Submit New Sighting</a>
            <a href="dashboard.php" class="btn btn-outline-light btn-lg">My Dashboard</a>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Featured Sightings -->
<div class="row mb-5">
    <div class="col-12 text-center mb-4">
        <h2>🌟 Featured Sightings</h2>
        <p class="text-muted">Recently spotted trains from our community</p>
    </div>
    
    <?php if (empty($featured_sightings)): ?>
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5">
                <h4>No sightings yet</h4>
                <p class="text-muted">Be the first to share a train sighting!</p>
                <?php if (isLoggedIn()): ?>
                    <a href="submit_sighting.php" class="btn btn-primary">Submit First Sighting</a>
                <?php else: ?>
                    <a href="register.php" class="btn btn-primary">Join to Share</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php else: ?>
        <?php foreach ($featured_sightings as $sighting): ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <img src="<?php echo $sighting['photo_path'] ?: 'https://via.placeholder.com/400x250/3498db/FFFFFF?text=' . urlencode($sighting['train_model']); ?>" 
                     class="card-img-top" alt="<?php echo htmlspecialchars($sighting['train_model']); ?>">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($sighting['train_model']); ?></h5>
                    <p class="card-text">
                        <strong>📍 Location:</strong> <?php echo htmlspecialchars($sighting['location']); ?><br>
                        <strong>👤 By:</strong> <?php echo htmlspecialchars($sighting['username']); ?>
                    </p>
                </div>
                <div class="card-footer bg-transparent">
                    <a href="sighting.php?id=<?php echo $sighting['id']; ?>" class="btn btn-primary btn-sm">View Details</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Statistics Section -->
<div class="row mt-5">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-body text-center">
                <h4>Join Our Growing Community</h4>
                <div class="row mt-4">
                    <div class="col-md-4">
                        <h3 class="text-primary">
                            <?php echo $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(); ?>
                        </h3>
                        <p class="text-muted">Train Spotters</p>
                    </div>
                    <div class="col-md-4">
                        <h3 class="text-success">
                            <?php echo $pdo->query("SELECT COUNT(*) FROM sightings")->fetchColumn(); ?>
                        </h3>
                        <p class="text-muted">Train Sightings</p>
                    </div>
                    <div class="col-md-4">
                        <h3 class="text-info">
                            <?php echo $pdo->query("SELECT COUNT(*) FROM trains")->fetchColumn(); ?>
                        </h3>
                        <p class="text-muted">Train Models</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>