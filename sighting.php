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
$has_liked = false; // Standard: nicht geliked
if (isLoggedIn()) {
    $like_check_stmt = $pdo->prepare("SELECT id FROM likes WHERE sighting_id = ? AND user_id = ?");
    $like_check_stmt->execute([$sighting_id, $_SESSION['user_id']]);
    $has_liked = $like_check_stmt->fetch() !== false; // true wenn gefunden
}

// 2. Like-Anzahl für diesen Post holen
$like_count_stmt = $pdo->prepare("SELECT COUNT(*) as like_count FROM likes WHERE sighting_id = ?");
$like_count_stmt->execute([$sighting_id]);
$like_count = $like_count_stmt->fetch(PDO::FETCH_ASSOC)['like_count'];

// 3. Like-Button wurde geklickt - Verarbeiten
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isLoggedIn() && isset($_POST['toggle_like'])) {
    if ($has_liked) {
        // Fall 1: User hat schon geliked -> LIKE ENTFERNEN
        $delete_stmt = $pdo->prepare("DELETE FROM likes WHERE sighting_id = ? AND user_id = ?");
        $delete_stmt->execute([$sighting_id, $_SESSION['user_id']]);
    } else {
        // Fall 2: User hat noch nicht geliked -> LIKE HINZUFÜGEN
        $insert_stmt = $pdo->prepare("INSERT INTO likes (sighting_id, user_id) VALUES (?, ?)");
        $insert_stmt->execute([$sighting_id, $_SESSION['user_id']]);
    }
    
    // Seite neu laden um Änderungen anzuzeigen
    redirect("sighting.php?id=$sighting_id");
}
$is_featured = $sighting['is_featured'] ?? false;

// Handle feature/unfeature action (nur für Admins)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isAdmin() && isset($_POST['toggle_feature'])) {
    $new_featured_status = $is_featured ? 0 : 1;
    
    $stmt = $pdo->prepare("UPDATE sightings SET is_featured = ? WHERE id = ?");
    $stmt->execute([$new_featured_status, $sighting_id]);
    
    redirect("sighting.php?id=$sighting_id");
}
?>

<?php include 'includes/header.php'; ?>

<div class="row">
    <div class="col-md-8">
        <!-- Sighting Details -->
<div class="card mb-4 
    <?php if ($is_featured): ?>
        border-warning border-3 
        rounded-top-0  <!-- Obere Ecken gerade -->
    <?php endif; ?>">
    
    <!-- Featured Header -->
    <?php if ($is_featured): ?>
        <div class="card-header bg-warning text-dark rounded-0 py-2">
            ⭐ FEATURED POST
        </div>
    <?php endif; ?>
    
    <!-- Photo mit geraden oberen Ecken bei featured -->
    <?php if ($sighting['photo_path']): ?>
        <img src="<?php echo $sighting['photo_path']; ?>" 
             class="card-img-top <?php echo $is_featured ? 'rounded-top-0' : ''; ?>" 
             alt="Train photo"
             style="<?php echo $is_featured ? 'border-top: 3px solid #ffc107 !important;' : ''; ?>">
    <?php else: ?>
        <img src="https://via.placeholder.com/800x400/4A90E2/FFFFFF?text=Train+Photo" 
             class="card-img-top <?php echo $is_featured ? 'rounded-top-0' : ''; ?>" 
             alt="Train photo"
             style="<?php echo $is_featured ? 'border-top: 3px solid #ffc107 !important;' : ''; ?>">
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
    <!-- EINGELOGGT-->
    <?php if (isLoggedIn()): ?>
        <form method="POST" class="d-inline">
            <button type="submit" name="toggle_like" value="1" 
                    class="btn <?php echo $has_liked ? 'btn-primary' : 'btn-outline-primary'; ?> btn-sm">
                👍 Like (<?php echo $like_count; ?>)
            </button>
        </form>
    <?php else: ?>
        <!-- NICHT-EINGELOGGTE -->
        <a href="login.php" class="btn btn-outline-primary btn-sm">
            👍 Like (<?php echo $like_count; ?>)
        </a>
    <?php endif; ?>
    
    <!-- COMMENT BUTTON  -->
    <button class="btn btn-outline-secondary btn-sm" onclick="document.getElementById('comment').focus()">
        💬 Comment
    </button>
    
    <?php if (isAdmin()): ?>
    <form method="POST" class="d-inline">
        <button type="submit" name="toggle_feature" value="1" 
                class="btn <?php echo $is_featured ? 'btn-warning' : 'btn-outline-warning'; ?> btn-sm">
            ⭐ <?php echo $is_featured ? 'Featured' : 'Feature'; ?>
        </button>
    </form>
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