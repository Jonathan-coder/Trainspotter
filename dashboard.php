<?php
require_once 'includes/auth.php';
require_once 'includes/config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

// Get user's sighting count
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM sightings WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$sighting_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
?>

<?php include 'includes/header.php'; ?>

<h1>Dashboard</h1>
<p class="lead">Welcome back, <?php echo $_SESSION['username']; ?>!</p>

<div class="row">
    <div class="col-md-4">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h5 class="card-title">My Spottings</h5>
                <h2 class="display-4"><?php echo $sighting_count; ?></h2>
                <a href="my_spottings.php" class="text-white">View all →</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h5 class="card-title">Submit New</h5>
                <p class="card-text">Log a new train sighting</p>
                <a href="submit_sighting.php" class="text-white">Submit now →</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-info">
            <div class="card-body">
                <h5 class="card-title">Browse Gallery</h5>
                <p class="card-text">See what others have spotted</p>
                <a href="gallery.php" class="text-white">Browse →</a>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5>Recent Activity</h5>
            </div>
            <div class="card-body">
                <p>Your recent train sightings will appear here.</p>
                <?php if ($sighting_count == 0): ?>
                    <div class="alert alert-info">
                        You haven't submitted any train sightings yet. 
                        <a href="submit_sighting.php" class="alert-link">Submit your first one now!</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>