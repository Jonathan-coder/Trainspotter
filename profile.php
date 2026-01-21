<?php
require_once 'includes/auth.php';
require_once 'includes/config.php';

// Get user ID from URL or use logged-in user
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
} elseif (isLoggedIn()) {
    $user_id = $_SESSION['user_id'];
} else {
    redirect('index.php');
}

// Fetch user profile
$stmt = $pdo->prepare("SELECT id, username, email, created_at FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found!");
}

// Fetch user's sightings
$sightings_stmt = $pdo->prepare("
    SELECT s.*, t.model as train_model 
    FROM sightings s 
    LEFT JOIN trains t ON s.train_id = t.id 
    WHERE s.user_id = ? 
    ORDER BY s.created_at DESC
");
$sightings_stmt->execute([$user_id]);
$sightings = $sightings_stmt->fetchAll(PDO::FETCH_ASSOC);
// Fetch likes
$likes_received_stmt = $pdo->prepare("
    SELECT COUNT(*) as likes_received
    FROM likes l
    INNER JOIN sightings s ON l.sighting_id = s.id
    WHERE s.user_id = ?
");
$likes_received_stmt->execute([$user_id]);
$likes_received = $likes_received_stmt->fetch(PDO::FETCH_ASSOC)['likes_received'];

// Fetch comment count
$comments_stmt = $pdo->prepare("
    SELECT COUNT(*) as comment_count 
    FROM comments 
    WHERE user_id = ?
");
$comments_stmt->execute([$user_id]);
$comment_count = $comments_stmt->fetch(PDO::FETCH_ASSOC)['comment_count'];



$is_own_profile = isLoggedIn() && $_SESSION['user_id'] == $user_id;
?>

<?php include 'includes/header.php'; ?>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <img src="https://via.placeholder.com/150/4A90E2/FFFFFF?text=<?php echo substr($user['username'], 0, 1); ?>" 
                     class="rounded-circle mb-3" alt="Profile picture" width="150" height="150">
                <h4><?php echo htmlspecialchars($user['username']); ?></h4>
                <p class="text-muted">
                    Member since <?php echo date('F Y', strtotime($user['created_at'])); ?>
                </p>
                
                <?php if ($is_own_profile): ?>
                    <div class="mt-3">
                        <button class="btn btn-outline-primary btn-sm">Edit Profile</button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h6>Statistics</h6>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Total Sightings
                        <span class="badge bg-primary rounded-pill"><?php echo count($sightings); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Comments
                        <span class="badge bg-success rounded-pill"><?php echo $comment_count; ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Likes Received
                        <span class="badge bg-info rounded-pill"><?php echo $likes_received; ?></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5><?php echo $is_own_profile ? 'My Train Spottings' : htmlspecialchars($user['username']) . "'s Spottings"; ?></h5>
            </div>
            <div class="card-body">
                <?php if (empty($sightings)): ?>
                    <div class="text-center py-4">
                        <p class="text-muted">No train sightings yet.</p>
                        <?php if ($is_own_profile): ?>
                            <a href="submit_sighting.php" class="btn btn-primary">Submit Your First Sighting</a>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($sightings as $sighting): ?>
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <?php if ($sighting['photo_path']): ?>
                                    <img src="<?php echo $sighting['photo_path']; ?>" class="card-img-top" alt="Train photo" style="height: 150px; object-fit: cover;">
                                <?php else: ?>
                                    <img src="https://via.placeholder.com/300x150/4A90E2/FFFFFF?text=Train" class="card-img-top" alt="Train photo">
                                <?php endif; ?>
                                <div class="card-body">
                                    <h6 class="card-title"><?php echo htmlspecialchars($sighting['train_model']); ?></h6>
                                    <p class="card-text small">
                                        <strong>Location:</strong> <?php echo htmlspecialchars($sighting['location']); ?><br>
                                        <strong>Date:</strong> <?php echo date('d.m.Y', strtotime($sighting['date'])); ?>
                                    </p>
                                    <a href="sighting.php?id=<?php echo $sighting['id']; ?>" class="btn btn-sm btn-outline-primary">View Details</a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>