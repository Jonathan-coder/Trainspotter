<?php
require_once 'includes/auth.php';
require_once 'includes/config.php';

$search_term = "";
$date_from = "";
$date_to = "";
$results = [];

// Check if form was submitted
$is_search_submitted = isset($_GET['search_term']) || isset($_GET['date_from']) || isset($_GET['date_to']);

if($is_search_submitted) {
    $search_term = $_GET['search_term'] ?? "";
    $date_from = $_GET['date_from'] ?? "";
    $date_to = $_GET['date_to'] ?? "";
    
    try {
        // Build SQL query dynamically based on filters
        $sql = "
            SELECT s.*, u.username, t.model as train_model, t.number as train_number
            FROM sightings s 
            LEFT JOIN users u ON s.user_id = u.id 
            LEFT JOIN trains t ON s.train_id = t.id 
            WHERE 1=1
        ";
        
        $params = [];
        
        // Search term filter
        if(!empty($search_term)) {
            $sql .= " AND (s.location LIKE :search 
                      OR t.model LIKE :search 
                      OR t.number LIKE :search
                      OR u.username LIKE :search
                      OR s.notes LIKE :search)";
            $params[':search'] = "%" . $search_term . "%";
        }
        
        // Date from filter
        if(!empty($date_from)) {
            $sql .= " AND s.date >= :date_from";
            $params[':date_from'] = $date_from;
        }
        
        // Date to filter
        if(!empty($date_to)) {
            $sql .= " AND s.date <= :date_to";
            $params[':date_to'] = $date_to;
        }
        
        $sql .= " ORDER BY s.created_at DESC LIMIT 24";
        
        $stmt = $pdo->prepare($sql);
        
        // Bind all parameters
        foreach($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch(PDOException $e) {
        die("Search error: " . $e->getMessage());
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="container mt-4">
    <h1>🔍 Advanced Search</h1>
    <p class="text-muted">Search train sightings with filters</p>

    <!-- Search Form with Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="search.php" class="row g-3">
                <!-- Search Term -->
                <div class="col-md-12">
                    <label for="search_term" class="form-label search-form-label">Search Term</label>
                    <input type="text" 
                           name="search_term" 
                           id="search_term"
                           class="form-control"
                           placeholder="Train model, location, number, or username..."
                           value="<?php echo htmlspecialchars($search_term); ?>">
                </div>
                
                <!-- Date Filters -->
                <div class="col-md-6">
                    <label for="date_from" class="form-label search-form-label">Date From</label>
                    <input type="date" 
                           name="date_from" 
                           id="date_from"
                           class="form-control"
                           value="<?php echo htmlspecialchars($date_from); ?>">
                    <div class="form-text">Earliest spotting date</div>
                </div>
                
                <div class="col-md-6">
                    <label for="date_to" class="form-label search-form-label">Date To</label>
                    <input type="date" 
                           name="date_to" 
                           id="date_to"
                           class="form-control"
                           value="<?php echo htmlspecialchars($date_to); ?>">
                    <div class="form-text">Latest spotting date</div>
                </div>
                
                <!-- Form Actions -->
                <div class="col-12">
                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary">
                            🔍 Search
                        </button>
                        
                        <?php if($is_search_submitted): ?>
                            <a href="search.php" class="btn btn-outline-secondary">
                                Clear Filters
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Active Filters Display -->
    <?php if($is_search_submitted): ?>
        <div class="mb-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="mb-0">
                    <?php echo count($results); ?> Result<?php echo count($results) != 1 ? 's' : ''; ?> Found
                </h5>
                
                <?php if(!empty($search_term) || !empty($date_from) || !empty($date_to)): ?>
                    <div class="active-filters">
                        <span class="text-muted">Active filters:</span>
                        <?php if(!empty($search_term)): ?>
                            <span class="badge bg-info filter-badge">
                                Search: "<?php echo htmlspecialchars($search_term); ?>"
                            </span>
                        <?php endif; ?>
                        <?php if(!empty($date_from)): ?>
                            <span class="badge bg-secondary filter-badge">
                                From: <?php echo date('d.m.Y', strtotime($date_from)); ?>
                            </span>
                        <?php endif; ?>
                        <?php if(!empty($date_to)): ?>
                            <span class="badge bg-secondary filter-badge">
                                To: <?php echo date('d.m.Y', strtotime($date_to)); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Search Results -->
    <?php if($is_search_submitted): ?>
        <?php if(!empty($results)): ?>
            <div class="row">
                <?php foreach ($results as $sighting): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm search-card">
                        <!-- Featured Badge -->
                        <?php if($sighting['is_featured'] ?? false): ?>
                            <div class="position-absolute top-0 start-0 m-2">
                                <span class="badge bg-warning text-dark filter-badge">
                                    ⭐ Featured
                                </span>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Image -->
                        <div class="search-image-container">
                            <img src="<?php echo $sighting['photo_path'] ?: 'https://via.placeholder.com/400x250/3498db/FFFFFF?text=' . urlencode($sighting['train_model']); ?>" 
                                 class="card-img-top w-100 h-100 object-fit-cover"
                                 alt="<?php echo htmlspecialchars($sighting['train_model']); ?>">
                            
                            <!-- Date Badge -->
                            <div class="search-date-badge">
                                📅 <?php echo date('d.m.Y', strtotime($sighting['date'])); ?>
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <h5 class="card-title">
                                <?php echo htmlspecialchars($sighting['train_model']); ?>
                                <?php if(!empty($sighting['train_number'])): ?>
                                    <small class="text-muted">(#<?php echo htmlspecialchars($sighting['train_number']); ?>)</small>
                                <?php endif; ?>
                            </h5>
                            
                            <p class="card-text">
                                📍 <?php echo htmlspecialchars($sighting['location']); ?>
                            </p>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($sighting['username']); ?>&background=4A90E2&color=fff" 
                                         class="rounded-circle me-2" 
                                         width="30" 
                                         height="30" 
                                         alt="<?php echo htmlspecialchars($sighting['username']); ?>">
                                    <small class="text-muted">
                                        👤 By <?php echo htmlspecialchars($sighting['username']); ?>
                                    </small>
                                </div>
                                
                                <a href="sighting.php?id=<?php echo $sighting['id']; ?>" 
                                   class="btn btn-sm btn-outline-primary">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <!-- No Results -->
            <div class="no-results-container">
                <div class="no-results-icon">🔍❌</div>
                <h4 class="text-muted mt-3">No results found</h4>
                <p class="text-muted mb-3">
                    <?php if(!empty($search_term) || !empty($date_from) || !empty($date_to)): ?>
                        No sightings match your search criteria.
                    <?php else: ?>
                        Try entering a search term or selecting date filters.
                    <?php endif; ?>
                </p>
                <div class="mt-3">
                    <a href="gallery.php" class="btn btn-outline-primary me-2">View All Sightings</a>
                    <button type="button" onclick="document.getElementById('search_term').focus()" class="btn btn-primary">
                        Modify Search
                    </button>
                </div>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <!-- Search Instructions -->
        <div class="search-instructions">
            <div class="no-results-icon">🔍</div>
            <h4 class="text-primary mt-3">Start Your Search</h4>
            <p class="text-muted mb-4">
                Use the search form above to find train sightings.<br>
                You can search by train model, location, username, or filter by date.
            </p>
            
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card border-0 bg-transparent">
                        <div class="card-body">
                            <div class="instruction-icon">📅</div>
                            <h6>Date Filtering</h6>
                            <p class="small text-muted">
                                Find sightings from specific time periods
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 bg-transparent">
                        <div class="card-body">
                            <div class="instruction-icon">🔍</div>
                            <h6>Text Search</h6>
                            <p class="small text-muted">
                                Search train models, locations, or usernames
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 bg-transparent">
                        <div class="card-body">
                            <div class="instruction-icon">🚆</div>
                            <h6>All Results</h6>
                            <p class="small text-muted">
                                <a href="gallery.php" class="text-decoration-none">Browse all sightings</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    // Set default date for "Date To" to today
    document.addEventListener('DOMContentLoaded', function() {
        const dateToField = document.getElementById('date_to');
        if(dateToField && !dateToField.value) {
            const today = new Date().toISOString().split('T')[0];
            dateToField.value = today;
        }
        
        // Set max date to today for both fields
        const dateFromField = document.getElementById('date_from');
        if(dateFromField) {
            dateFromField.max = today;
        }
        if(dateToField) {
            dateToField.max = today;
        }
    });
</script>

<?php include 'includes/footer.php'; ?>