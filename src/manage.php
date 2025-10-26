<?php
include_once './database/settings.php';

$message = '';

// Handle delete request with POST-REDIRECT-GET pattern
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_job_reference'])) {
    $delete_job = $_POST['delete_job_reference'];
    if ($delete_job) {
        $stmt = $conn->prepare("DELETE FROM eoi WHERE job_reference = ?");
        $stmt->bind_param("s", $delete_job);
        $stmt->execute();
        $deleted_count = $stmt->affected_rows;
        
        // Redirect to prevent form resubmission
        header("Location: manage.php?deleted=$deleted_count&job_ref=" . urlencode($delete_job));
        exit();
    }
}

// Handle status update via AJAX or POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $eoi_id = $_POST['eoi_id'];
    $new_status = $_POST['new_status'];
    
    $stmt = $conn->prepare("UPDATE eoi SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $eoi_id);
    $stmt->execute();
    
    // For AJAX requests
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        echo json_encode(['success' => true]);
        exit();
    }
    
    // For regular POST, redirect
    header("Location: manage.php?status_updated=1");
    exit();
}

// Display delete message from redirect
if (isset($_GET['deleted'])) {
    $message = $_GET['deleted'] . " EOIs deleted for job reference " . htmlspecialchars($_GET['job_ref']) . ".";
}

if (isset($_GET['status_updated'])) {
    $message = "Status updated successfully.";
}

// --- Prepare filters ---
$filters = [];
$params = [];
$sql = "
    SELECT 
        a.*, 
        j.job_title
    FROM  eoi a
    LEFT JOIN jobs j 
        ON a.job_reference = j.job_reference
    WHERE 1
";

// Apply filters
if (!empty($_GET['job_reference'])) {
    $sql .= " AND a.job_reference = ?";
    $filters[] = $_GET['job_reference'];
}
if (!empty($_GET['status'])) {
    $sql .= " AND a.status = ?";
    $filters[] = $_GET['status'];
}
if (!empty($_GET['gender'])) {
    $sql .= " AND a.gender = ?";
    $filters[] = $_GET['gender'];
}
if (!empty($_GET['search'])) {
    $sql .= " AND (a.first_name LIKE ? OR a.last_name LIKE ? OR a.email LIKE ?)";
    $searchTerm = '%' . $_GET['search'] . '%';
    $filters[] = $searchTerm;
    $filters[] = $searchTerm;
    $filters[] = $searchTerm;
}

// Add sorting
$allowed_sort_columns = ['id', 'eoi_number', 'first_name', 'last_name', 'email', 'job_reference', 'status', 'gender', 'created_at'];
$sort_column = isset($_GET['sort']) && in_array($_GET['sort'], $allowed_sort_columns) ? $_GET['sort'] : 'created_at';
$sort_order = isset($_GET['order']) && $_GET['order'] === 'asc' ? 'ASC' : 'DESC';

$sql .= " ORDER BY a.$sort_column $sort_order";

$stmt = $conn->prepare($sql);

if ($filters) {
    $types = str_repeat("s", count($filters));
    $stmt->bind_param($types, ...$filters);
}

$stmt->execute();
$result = $stmt->get_result();


$page_title = "Applicant Management Portal";
include './includes/header.inc.php';

?>

<body style=" background: #f5f6f8;">

    <?php include './includes/navbar_management.inc.php'; ?>

    <div style="font-family: Arial, sans-serif; padding: 20px; margin-top: 5rem">

        <h2 style="font-size: 2rem; color: var(--asora-onnyx-green); font-weight: 400;">Applicant Management Portal</h2>

        <?php if ($message): ?>
            <div style="color: #28a745; font-weight: bold; margin: 15px 0; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 5px;">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <div>
            <form method="get" class="filter-bar">

                <select name="job_reference" style="width: auto;"
                    class="box_input" onchange="this.form.submit()">
                    <option value="">All Jobs</option>
                    <?php
                    $jobs = $conn->query("SELECT job_reference, job_title FROM jobs ORDER BY job_title ASC");
                    while ($job = $jobs->fetch_assoc()):
                    ?>
                        <option value="<?= htmlspecialchars($job['job_reference']) ?>"
                            <?= (($_GET['job_reference'] ?? '') === $job['job_reference']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($job['job_reference'] . ' - ' . $job['job_title']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <input type="text"
                    name="search"
                    class="box_input"
                    placeholder="Search name, last name or both..."
                    value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">


                <select name="status"
                    class="box_input" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="New" <?= (($_GET['status'] ?? '') === 'New') ? 'selected' : '' ?>>New</option>
                    <option value="Current" <?= (($_GET['status'] ?? '') === 'Current') ? 'selected' : '' ?>>Current</option>
                    <option value="Final" <?= (($_GET['status'] ?? '') === 'Final') ? 'selected' : '' ?>>Final</option>
                </select>

                <select name="gender"
                    class="box_input" onchange="this.form.submit()">
                    <option value="">All Genders</option>
                    <option value="Male" <?= (($_GET['gender'] ?? '') === 'Male') ? 'selected' : '' ?>>Male</option>
                    <option value="Female" <?= (($_GET['gender'] ?? '') === 'Female') ? 'selected' : '' ?>>Female</option>
                    <option value="Other" <?= (($_GET['gender'] ?? '') === 'Other') ? 'selected' : '' ?>>Other</option>
                </select>

                <!-- Hidden fields to preserve sort parameters -->
                <?php if (isset($_GET['sort'])): ?>
                    <input type="hidden" name="sort" value="<?= htmlspecialchars($_GET['sort']) ?>">
                <?php endif; ?>
                <?php if (isset($_GET['order'])): ?>
                    <input type="hidden" name="order" value="<?= htmlspecialchars($_GET['order']) ?>">
                <?php endif; ?>

                <button type="submit" class="box_input" style="font-weight: 300; color:white; background-color: var(--asora-onyx-green);">Filter</button>
                <a href="manage.php" class="box_input" style="text-align:center; font-weight: 300; color:white; background-color: var(--asora-onyx-green);">Clear all filters</a>
            </form>
        </div>
   

        <!-- FILTER BAR -->


        <!-- TABLE -->
        <table class="eoi-table">
            <thead>
                <tr>
                    <th>
                        <a href="?<?= http_build_query(array_merge($_GET, ['sort' => 'id', 'order' => ($sort_column === 'id' && $sort_order === 'ASC') ? 'desc' : 'asc'])) ?>" style="text-decoration: none; color: inherit;">
                            <span style="background-color:#eff4f8; padding: 0.102rem 0.5rem; border-radius: 5px; color:#3f4e61; font-weight:600;">
                                ID <?= $sort_column === 'id' ? ($sort_order === 'ASC' ? '▲' : '▼') : '' ?>
                            </span>
                        </a>
                    </th>
                    <th>
                        <a href="?<?= http_build_query(array_merge($_GET, ['sort' => 'eoi_number', 'order' => ($sort_column === 'eoi_number' && $sort_order === 'ASC') ? 'desc' : 'asc'])) ?>" style="text-decoration: none; color: inherit;">
                            <span style="background-color:#eff4f8; padding: 0.102rem 0.5rem; border-radius: 5px; color:#3f4e61; font-weight:600;">
                                EOI # <?= $sort_column === 'eoi_number' ? ($sort_order === 'ASC' ? '▲' : '▼') : '' ?>
                            </span>
                        </a>
                    </th>
                    <th>
                        <a href="?<?= http_build_query(array_merge($_GET, ['sort' => 'first_name', 'order' => ($sort_column === 'first_name' && $sort_order === 'ASC') ? 'desc' : 'asc'])) ?>" style="text-decoration: none; color: inherit;">
                            <span style="background-color:#eff4f8; padding: 0.102rem 0.5rem; border-radius: 5px; color:#3f4e61; font-weight:600;">
                                Name <?= $sort_column === 'first_name' ? ($sort_order === 'ASC' ? '▲' : '▼') : '' ?>
                            </span>
                        </a>
                    </th>
                    <th>
                        <a href="?<?= http_build_query(array_merge($_GET, ['sort' => 'email', 'order' => ($sort_column === 'email' && $sort_order === 'ASC') ? 'desc' : 'asc'])) ?>" style="text-decoration: none; color: inherit;">
                            <span style="background-color:#eff4f8; padding: 0.102rem 0.5rem; border-radius: 5px; color:#3f4e61; font-weight:600;">
                                Email <?= $sort_column === 'email' ? ($sort_order === 'ASC' ? '▲' : '▼') : '' ?>
                            </span>
                        </a>
                    </th>
                    <th>
                        <a href="?<?= http_build_query(array_merge($_GET, ['sort' => 'job_reference', 'order' => ($sort_column === 'job_reference' && $sort_order === 'ASC') ? 'desc' : 'asc'])) ?>" style="text-decoration: none; color: inherit;">
                            <span style="background-color:#eff4f8; padding: 0.102rem 0.5rem; border-radius: 5px; color:#3f4e61; font-weight:600;">
                                Job <?= $sort_column === 'job_reference' ? ($sort_order === 'ASC' ? '▲' : '▼') : '' ?>
                            </span>
                        </a>
                    </th>
                    <th>
                        <a href="?<?= http_build_query(array_merge($_GET, ['sort' => 'status', 'order' => ($sort_column === 'status' && $sort_order === 'ASC') ? 'desc' : 'asc'])) ?>" style="text-decoration: none; color: inherit;">
                            <span style="background-color:#eff4f8; padding: 0.102rem 0.5rem; border-radius: 5px; color:#3f4e61; font-weight:600;">
                                Status <?= $sort_column === 'status' ? ($sort_order === 'ASC' ? '▲' : '▼') : '' ?>
                            </span>
                        </a>
                    </th>
                    <th>
                        <a href="?<?= http_build_query(array_merge($_GET, ['sort' => 'gender', 'order' => ($sort_column === 'gender' && $sort_order === 'ASC') ? 'desc' : 'asc'])) ?>" style="text-decoration: none; color: inherit;">
                            <span style="background-color:#eff4f8; padding: 0.102rem 0.5rem; border-radius: 5px; color:#3f4e61; font-weight:600;">
                                Gender <?= $sort_column === 'gender' ? ($sort_order === 'ASC' ? '▲' : '▼') : '' ?>
                            </span>
                        </a>
                    </th>
                    <th>
                        <a href="?<?= http_build_query(array_merge($_GET, ['sort' => 'created_at', 'order' => ($sort_column === 'created_at' && $sort_order === 'ASC') ? 'desc' : 'asc'])) ?>" style="text-decoration: none; color: inherit;">
                            <span style="background-color:#eff4f8; padding: 0.102rem 0.5rem; border-radius: 5px; color:#3f4e61; font-weight:600;">
                                Applied On <?= $sort_column === 'created_at' ? ($sort_order === 'ASC' ? '<img width="12" height="12" src="https://img.icons8.com/fluency-systems-regular/48/collapse-arrow--v2.png" alt="collapse-arrow--v2"/>' : '<img width="12" height="12" src="https://img.icons8.com/fluency-systems-filled/48/expand-arrow.png" alt="expand-arrow"/>') : '' ?>
                            </span>
                        </a>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['id']) ?></td>
                            <td><?= htmlspecialchars($row['eoi_number']) ?></td>
                            <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['job_title'] ?? '—') ?></td>
                            <td>
                                <form method="post" class="status-update-form" style="margin: 0;">
                                    <input type="hidden" name="eoi_id" value="<?= $row['id'] ?>">
                                    <input type="hidden" name="update_status" value="1">
                                    <select name="new_status" 
                                            class="status <?= htmlspecialchars($row['status']) ?>"
                                            onchange="this.form.submit()"
                                            style="border: none; background: transparent; cursor: pointer; font-weight: 500;">
                                        <option value="New" <?= $row['status'] === 'New' ? 'selected' : '' ?>>New</option>
                                        <option value="Current" <?= $row['status'] === 'Current' ? 'selected' : '' ?>>Current</option>
                                        <option value="Final" <?= $row['status'] === 'Final' ? 'selected' : '' ?>>Final</option>
                                    </select>
                                </form>
                            </td>
                            <td><?= htmlspecialchars($row['gender']) ?></td>
                            <td><?= htmlspecialchars($row['created_at']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="text-align:center;">No eoi found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- DELETE SECTION -->
        <div style="display: flex; justify-content: flex-start; margin-top: 20px;">

            <form method="post"
                style="display: flex; 
                    flex-direction: column;
                    align-items: flex-start; 
                    gap: 8px; 
                    width: 100%;">

                <!-- Label -->
                <label for="delete_job_reference" class="box_label" style="font-weight: bold; font-size: 0.85rem;">
                    Delete EOIs:
                </label>

                <!-- Dropdown and Button Container -->
                <div style="display: flex; gap: 10px; align-items: center;">
                    <!-- Dropdown -->
                    <select name="delete_job_reference" id="delete_job_reference" class="box_input"
                        style="padding: 8px 12px; font-size: 0.85rem; min-width: 250px;">
                        <option value="">-- Select Job --</option>
                        <?php
                        $jobs = $conn->query("SELECT job_reference, job_title FROM jobs ORDER BY job_title ASC");
                        while ($job = $jobs->fetch_assoc()):
                        ?>
                            <option value="<?= htmlspecialchars($job['job_reference']) ?>">
                                <?= htmlspecialchars($job['job_reference'] . ' - ' . $job['job_title']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>

                    <!-- Submit Button -->
                    <button type="submit"
                        onclick="return confirm('Are you sure you want to delete all EOIs for this job?');"
                        style="background: none; border: none; cursor: pointer; padding: 0;">
                        <img width="32" height="32" src="https://img.icons8.com/?size=100&id=14237&format=png&color=A10909" alt="trash" />
                    </button>
                </div>

            </form>

        </div>

    </div>
</body>

</html>