<?php
require_once 'includes/auth.php';
require_once 'includes/config.php';

if (!isset($_GET['id'])) {
    redirect('gallery.php');
}

$sighting_id = $_GET['id'];

// Fetch sighting details
$stmt = $pdo->prepare("
    SELECT s.*, u.username, u.id as user_id, t.model as train_model, t.number as train_number 
    FROM sightings s 
    LEFT JOIN users u ON s.user_id = u.id 
    LEFT JOIN trains t ON s.train_id = t.id 
    WHERE s.id = ?
");
$stmt->execute([$sighting_id]);
$sighting = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$sighting) {
    die("Sighting not found!");
}

// Fetch comments
$comments_stmt = $pdo->prepare("
    SELECT c.*, u.username 
    FROM comments c 
    LEFT JOIN users u ON c.user_id = u.id 
    WHERE c.sighting_id = ? 
    ORDER BY c.created_at ASC
");
$comments_stmt->execute([$sighting_id]);
$comments = $comments_stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle new comment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isLoggedIn() && isset($_POST['comment'])) {
    $comment = $_POST['comment'];
    $stmt = $pdo->prepare("INSERT INTO comments (sighting_id, user_id, comment) VALUES (?, ?, ?)");
    $stmt->execute([$sighting_id, $_SESSION['user_id'], $comment]);
    redirect("sighting.php?id=$sighting_id");
}
?>

<?php include 'includes/header.php'; ?>

<div class="row">
    <div class="col-md-8">
        <!-- Sighting Details -->
        <div class="card mb-4">
            <?php if ($sighting['photo_path']): ?>
                <img src="<?php echo $sighting['photo_path']; ?>" class="card-img-top" alt="Train photo">
            <?php else: ?>
                <img src="https://via.placeholder.com/800x400/4A90E2/FFFFFF?text=Train+Photo" class="card-img-top" alt="Train photo">
            <?php endif; ?>
            
            <div class="card-body">
                <h2 class="card-title"><?php echo htmlspecialchars($sighting['train_model']); ?></h2>
                <p class="card-text">
                    <strong>Train Number:</strong> <?php echo htmlspecialchars($sighting['train_number']); ?><br>
                    <strong>Location:</strong> <?php echo htmlspecialchars($sighting['location']); ?><br>
                    <strong>Date Spotted:</strong> <?php echo date('d.m.Y', strtotime($sighting['date'])); ?><br>
                    <strong>Spotted by:</strong> 
                    <a href="profile.php?id=<?php echo $sighting['user_id']; ?>">
                        <?php echo htmlspecialchars($sighting['username']); ?>
                    </a>
                </p>
                
                <?php if ($sighting['notes']): ?>
                    <div class="mt-3">
                        <h5>Additional Notes:</h5>
                        <p><?php echo nl2br(htmlspecialchars($sighting['notes'])); ?></p>
                    </div>
                <?php endif; ?>
                
                <div class="mt-3">
                    <button class="btn btn-outline-primary btn-sm">👍 Like (0)</button>
                    <button class="btn btn-outline-secondary btn-sm">💬 Comment</button>
                    <?php if (isAdmin()): ?>
                        <button class="btn btn-outline-warning btn-sm">⭐ Feature</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Comments Section -->
        <div class="card">
            <div class="card-header">
                <h5>Comments (<?php echo count($comments); ?>)</h5>
            </div>
            <div class="card-body">
                <?php if (isLoggedIn()): ?>
                    <form method="POST" class="mb-4">
                        <div class="mb-3">
                            <label for="comment" class="form-label">Add a comment</label>
                            <textarea class="form-control" id="comment" name="comment" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Post Comment</button>
                    </form>
                <?php else: ?>
                    <div class="alert alert-info">
                        <a href="login.php">Login</a> to post comments.
                    </div>
                <?php endif; ?>

                <?php if (empty($comments)): ?>
                    <p class="text-muted">No comments yet. Be the first to comment!</p>
                <?php else: ?>
                    <?php foreach ($comments as $comment): ?>
                        <div class="border-bottom pb-3 mb-3">
                            <div class="d-flex justify-content-between">
                                <strong><?php echo htmlspecialchars($comment['username']); ?></strong>
                                <small class="text-muted"><?php echo date('d.m.Y H:i', strtotime($comment['created_at'])); ?></small>
                            </div>
                            <p class="mb-0 mt-1"><?php echo nl2br(htmlspecialchars($comment['comment'])); ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Sidebar -->
        <div class="card">
            <div class="card-header">
                <h5>About this Train</h5>
            </div>
            <div class="card-body">
                <p>Additional train information would be displayed here.</p>
                <ul class="list-group">
                    <li class="list-group-item">Model: <?php echo htmlspecialchars($sighting['train_model']); ?></li>
                    <li class="list-group-item">Number: <?php echo htmlspecialchars($sighting['train_number']); ?></li>
                    <li class="list-group-item">Location: <?php echo htmlspecialchars($sighting['location']); ?></li>
                </ul>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h5>Similar Sightings</h5>
            </div>
            <div class="card-body">
                <p>Other sightings of the same train model would appear here.</p>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>