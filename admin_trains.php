<?php
require_once 'includes/auth.php';
require_once 'includes/config.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('index.php');
}

$message = '';

// Add new train
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_train'])) {
    $model = $_POST['model'];
    $number = $_POST['number'];
    $info = $_POST['info'];
    
    $stmt = $pdo->prepare("INSERT INTO trains (model, number, info) VALUES (?, ?, ?)");
    if ($stmt->execute([$model, $number, $info])) {
        $message = '<div class="alert alert-success">Train added successfully!</div>';
    } else {
        $message = '<div class="alert alert-danger">Failed to add train!</div>';
    }
}

// Delete train
if (isset($_GET['delete'])) {
    $train_id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM trains WHERE id = ?");
    if ($stmt->execute([$train_id])) {
        $message = '<div class="alert alert-success">Train deleted successfully!</div>';
    } else {
        $message = '<div class="alert alert-danger">Failed to delete train!</div>';
    }
}

// Fetch all trains
$trains = $pdo->query("SELECT * FROM trains ORDER BY model")->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'includes/header.php'; ?>

<div class="container mt-4">
    <h1>🚆 Manage Trains</h1>
    <p class="lead">Add, edit or remove train models from the database</p>

    <?php echo $message; ?>

    <div class="row">
        <!-- Add Train Form -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5>Add New Train</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label for="model" class="form-label">Train Model *</label>
                            <input type="text" class="form-control" id="model" name="model" required>
                        </div>
                        <div class="mb-3">
                            <label for="number" class="form-label">Train Number</label>
                            <input type="text" class="form-control" id="number" name="number">
                        </div>
                        <div class="mb-3">
                            <label for="info" class="form-label">Information</label>
                            <textarea class="form-control" id="info" name="info" rows="3"></textarea>
                        </div>
                        <button type="submit" name="add_train" class="btn btn-primary w-100">Add Train</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Train List -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5>Existing Trains (<?php echo count($trains); ?>)</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($trains)): ?>
                        <div class="alert alert-info">No trains in database yet.</div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Model</th>
                                        <th>Number</th>
                                        <th>Information</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($trains as $train): ?>
                                    <tr>
                                        <td><?php echo $train['id']; ?></td>
                                        <td><strong><?php echo htmlspecialchars($train['model']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($train['number']); ?></td>
                                        <td><?php echo htmlspecialchars($train['info']); ?></td>
                                        <td>
                                            <a href="admin_trains.php?delete=<?php echo $train['id']; ?>" 
                                               class="btn btn-danger btn-sm"
                                               onclick="return confirm('Are you sure you want to delete this train?')">
                                               Delete
                                            </a>
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
    </div>
</div>

<?php include 'includes/footer.php'; ?>