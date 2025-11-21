<?php
require_once 'includes/auth.php';
require_once 'includes/config.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('index.php');
}

$message = '';

// Delete sighting
if (isset($_GET['delete_sighting'])) {
    $sighting_id = $_GET['delete_sighting'];
    $stmt = $pdo->prepare("DELETE FROM sightings WHERE id = ?");
    if ($stmt->execute([$sighting_id])) {
        $message = '<div class="alert alert-success">Sighting deleted successfully!</div>';
    } else {
        $message = '<div class="alert alert-danger">Failed to delete sighting!</div>';
    }
}

// Delete comment
if (isset($_GET['delete_comment'])) {
    $comment_id = $_GET['delete_comment'];
    $stmt = $pdo->prepare("DELETE FROM comments WHERE id = ?");
    if ($stmt->execute([$comment_id])) {
        $message = '<div class="alert alert-success">Comment deleted successfully!</div>';
    } else {
        $message = '<div class="alert alert-danger">Failed to delete comment!</div>';
    }
}

// Fetch all sightings with user info
$sightings = $pdo->query("
    SELECT s.*, u.username, t.model as train_model 
    FROM sightings s 
    LEFT JOIN users u ON s.user_id = u.id 
    LEFT JOIN trains t ON s.train_id = t.id 
    ORDER BY s.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Fetch all comments with user info
$comments = $pdo->query("
    SELECT c.*, u.username as comment_author, s.id as sighting_id, t.model as train_model
    FROM comments c 
    LEFT JOIN users u ON c.user_id = u.id 
    LEFT JOIN sightings s ON c.sighting_id = s.id 
    LEFT JOIN trains t ON s.train_id = t.id 
    ORDER BY c.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'includes/header.php'; ?>

<div class="container mt-4">
    <h1>🛡️ Content Moderation</h1>
    <p class="lead">Manage user content and remove inappropriate posts</p>

    <?php echo $message; ?>

    <!-- Sightings Moderation -->
    <div class="card mb-4">
        <div class="card-header bg-warning text-dark">
            <h5>Manage Sightings (<?php echo count($sightings); ?>)</h5>
        </div>
        <div class="card-body">
            <?php if (empty($sightings)): ?>
                <div class="alert alert-info">No sightings to moderate.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Train</th>
                                <th>Location</th>
                                <th>User</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sightings as $sighting): ?>
                            <tr>
                                <td><?php echo $sighting['id']; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($sighting['train_model']); ?></strong>
                                    <?php if ($sighting['photo_path']): ?>
                                        <br><small class="text-muted">📷 Has photo</small>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($sighting['location']); ?></td>
                                <td><?php echo htmlspecialchars($sighting['username']); ?></td>
                                <td><?php echo date('d.m.Y', strtotime($sighting['date'])); ?></td>
                                <td>
                                    <a href="sighting.php?id=<?php echo $sighting['id']; ?>" 
                                       class="btn btn-info btn-sm" target="_blank">View</a>
                                    <a href="admin_moderate.php?delete_sighting=<?php echo $sighting['id']; ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Are you sure you want to delete this sighting?')">Delete</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Comments Moderation -->
    <div class="card">
        <div class="card-header bg-danger text-white">
            <h5>Manage Comments (<?php echo count($comments); ?>)</h5>
        </div>
        <div class="card-body">
            <?php if (empty($comments)): ?>
                <div class="alert alert-info">No comments to moderate.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Comment</th>
                                <th>Author</th>
                                <th>Sighting</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($comments as $comment): ?>
                            <tr>
                                <td><?php echo $comment['id']; ?></td>
                                <td>
                                    <div style="max-width: 300px; word-wrap: break-word;">
                                        <?php echo htmlspecialchars($comment['comment']); ?>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($comment['comment_author']); ?></td>
                                <td>
                                    <?php if ($comment['train_model']): ?>
                                        <?php echo htmlspecialchars($comment['train_model']); ?>
                                    <?php else: ?>
                                        <em>Sighting deleted</em>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('d.m.Y H:i', strtotime($comment['created_at'])); ?></td>
                                <td>
                                    <?php if ($comment['sighting_id']): ?>
                                        <a href="sighting.php?id=<?php echo $comment['sighting_id']; ?>#comment-<?php echo $comment['id']; ?>" 
                                           class="btn btn-info btn-sm" target="_blank">View</a>
                                    <?php endif; ?>
                                    <a href="admin_moderate.php?delete_comment=<?php echo $comment['id']; ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Are you sure you want to delete this comment?')">Delete</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>