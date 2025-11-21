<?php
require_once 'includes/auth.php';
require_once 'includes/config.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('index.php');
}

// Statistics
$users_count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$sightings_count = $pdo->query("SELECT COUNT(*) FROM sightings")->fetchColumn();
$trains_count = $pdo->query("SELECT COUNT(*) FROM trains")->fetchColumn();
$recent_sightings = $pdo->query("SELECT COUNT(*) FROM sightings WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn();
?>

<?php include 'includes/header.php'; ?>

<h1>Admin Panel</h1>
<p class="lead">Platform Management Dashboard</p>

<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h5 class="card-title">Total Users</h5>
                <h2 class="display-4"><?php echo $users_count; ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h5 class="card-title">Total Sightings</h5>
                <h2 class="display-4"><?php echo $sightings_count; ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-info">
            <div class="card-body">
                <h5 class="card-title">Train Models</h5>
                <h2 class="display-4"><?php echo $trains_count; ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <h5 class="card-title">This Week</h5>
                <h2 class="display-4"><?php echo $recent_sightings; ?></h2>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="admin_trains.php" class="btn btn-outline-primary">Manage Trains</a>
                    <a href="admin_moderate.php" class="btn btn-outline-warning">Content Moderation</a>
                    <a href="gallery.php" class="btn btn-outline-info">View Gallery</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Recent Activity</h5>
            </div>
            <div class="card-body">
                <p>Admin-specific activity log will appear here.</p>
                <ul class="list-group">
                    <li class="list-group-item">No recent admin actions</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>