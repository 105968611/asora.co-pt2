<?php

session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include_once './database/settings.php';

$message = '';

// Delete handler for job based on reference string
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_job_reference'])) {
    $delete_job = $_POST['delete_job_reference'];
    if ($delete_job) {
        $stmt = $conn->prepare("DELETE FROM eoi WHERE job_reference = ?");
        $stmt->bind_param("s", $delete_job);
        $stmt->execute();
        $deleted_count = $stmt->affected_rows;

        // Redirect to manage page to prevent form resubmission
        header("Location: manage.php?deleted=$deleted_count&job_ref=" . urlencode($delete_job));
        exit();
    }
}

// Update handler for status dropdown in table
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $eoi_id = $_POST['eoi_id'];
    $new_status = $_POST['new_status'];

    $stmt = $conn->prepare("UPDATE eoi SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $eoi_id);
    $stmt->execute();

    // Redirect to manage page to prevent form resubmission
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

// Filters for eoi table
$filters = [];
$params = [];

//Create a join query to drag job title based on job reference
$sql = "
    SELECT 
        a.*, 
        j.job_title
    FROM  eoi a
    LEFT JOIN jobs j 
        ON a.job_reference = j.job_reference
    WHERE 1
";

// Filter by job reference (dropdown)
if (!empty($_GET['job_reference'])) {
    $sql .= " AND a.job_reference = ?";
    $filters[] = $_GET['job_reference'];
}

// Filter by status (dropdown)
if (!empty($_GET['status'])) {
    $sql .= " AND a.status = ?";
    $filters[] = $_GET['status'];
}

// Filter by gender (dropdown)
if (!empty($_GET['gender'])) {
    $sql .= " AND a.gender = ?";
    $filters[] = $_GET['gender'];
}
// Search by name, lastname or email
if (!empty($_GET['search'])) {
    $sql .= " AND (a.first_name LIKE ? OR a.last_name LIKE ? OR a.email LIKE ?)";
    $searchTerm = '%' . $_GET['search'] . '%';
    $filters[] = $searchTerm;
    $filters[] = $searchTerm;
    $filters[] = $searchTerm;
}

// Sort columns fuction
$allowed_sort_columns = ['id', 'eoi_number', 'first_name', 'last_name', 'email', 'job_reference', 'date_birth', 'status', 'gender', 'street_address', 'created_at'];
$sort_column = isset($_GET['sort']) && in_array($_GET['sort'], $allowed_sort_columns) ? $_GET['sort'] : 'created_at';
$sort_order = isset($_GET['order']) && $_GET['order'] === 'asc' ? 'ASC' : 'DESC';

//SQL Query for sorting
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

$page_title = "Manage - Asora";
require './includes/header.inc.php';

?>

<body style=" background: #f5f6f8;">
    <main>

        <?php include './includes/navbar_management.inc.php'; ?>

        <div style="font-family: Arial, sans-serif; padding: 20px; margin-top: 5rem">

            <h2 style="font-size: 2rem; color: var(--asora-onnyx-green); font-weight: 400;">Applicant Management Portal</h2>

            <?php if ($message): ?>
                <div style="color: #28a745; font-weight: bold; margin: 15px 0; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 5px;">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <section aria-label="Filter applicants">
                <form method="get" class="filter-bar">

                    <label for="job_reference" class="sr-only">Filter by job</label>
                    <select id="job_reference" name="job_reference" style="width: auto;"
                        class="box_input" aria-label="Filter by job>
                    <option value="">All Jobs</option>
                    <?php
                    $jobs = $conn->query("SELECT job_reference, job_title FROM jobs ORDER BY job_title ASC");
                    while ($job = $jobs->fetch_assoc()):
                    ?>
                        <option value=" <?= htmlspecialchars($job['job_reference']) ?>"
                        <?= (($_GET['job_reference'] ?? '') === $job['job_reference']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($job['job_reference'] . ' - ' . $job['job_title']) ?>
                        </option>
                    <?php endwhile; ?>
                    </select>
                    <label for="search" class="sr-only">Search by name or email</label>
                    <input type="text"
                        id="search"
                        name="search"
                        class="box_input"
                        placeholder="Search name, last name or both..."
                        aria-label="Search by name, last name, or email"
                        value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">

                    <label for="status" class="sr-only">Filter by status</label>
                    <select id="status" name="status"
                        class="box_input">
                        <option value="">All Status</option>
                        <option value="New" <?= (($_GET['status'] ?? '') === 'New') ? 'selected' : '' ?>>New</option>
                        <option value="Current" <?= (($_GET['status'] ?? '') === 'Current') ? 'selected' : '' ?>>Current</option>
                        <option value="Final" <?= (($_GET['status'] ?? '') === 'Final') ? 'selected' : '' ?>>Final</option>
                    </select>

                    <label for="gender" class="sr-only">Filter by gender</label>
                    <select id="gender" name="gender"
                        class="box_input">
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
            </section>


            <!--EOI TABLE-->
            <table class="eoi-table">
                <thead>
                    <tr>
                        <th>
                            <a href="?<?= http_build_query(array_merge($_GET, ['sort' => 'id', 'order' => ($sort_column === 'id' && $sort_order === 'ASC') ? 'desc' : 'asc'])) ?>" style="text-decoration: none; color: inherit;">
                                <span style="background-color:#eff4f8; padding: 0.102rem 0.5rem; border-radius: 5px; color:#3f4e61; font-weight:600; font-size: 12px">
                                    ID <?= $sort_column === 'id' ? ($sort_order === 'ASC' ? '<img width="8" height="8" src="https://img.icons8.com/fluency-systems-regular/48/collapse-arrow--v2.png" alt="collapse-arrow--v2"/>' : '<img width="8" height="8" src="https://img.icons8.com/fluency-systems-filled/48/expand-arrow.png" alt="expand-arrow"/>') : '' ?>
                                </span>
                            </a>
                        </th>
                        <th>
                            <a href="?<?= http_build_query(array_merge($_GET, ['sort' => 'eoi_number', 'order' => ($sort_column === 'eoi_number' && $sort_order === 'ASC') ? 'desc' : 'asc'])) ?>" style="text-decoration: none; color: inherit;">
                                <span style="background-color:#eff4f8; padding: 0.102rem 0.5rem; border-radius: 5px; color:#3f4e61; font-weight:600; font-size: 12px">
                                    EOI # <?= $sort_column === 'eoi_number' ? ($sort_order === 'ASC' ? '<img width="8" height="8" src="https://img.icons8.com/fluency-systems-regular/48/collapse-arrow--v2.png" alt="collapse-arrow--v2"/>' : '<img width="8" height="8" src="https://img.icons8.com/fluency-systems-filled/48/expand-arrow.png" alt="expand-arrow"/>') : '' ?>
                                </span>
                            </a>
                        </th>
                        <th>
                            <a href="?<?= http_build_query(array_merge($_GET, ['sort' => 'first_name', 'order' => ($sort_column === 'first_name' && $sort_order === 'ASC') ? 'desc' : 'asc'])) ?>" style="text-decoration: none; color: inherit;">
                                <span style="background-color:#eff4f8; padding: 0.102rem 0.5rem; border-radius: 5px; color:#3f4e61; font-weight:600; font-size: 12px">
                                    Name <?= $sort_column === 'first_name' ? ($sort_order === 'ASC' ? '<img width="8" height="8" src="https://img.icons8.com/fluency-systems-regular/48/collapse-arrow--v2.png" alt="collapse-arrow--v2"/>' : '<img width="8" height="8" src="https://img.icons8.com/fluency-systems-filled/48/expand-arrow.png" alt="expand-arrow"/>') : '' ?>
                                </span>
                            </a>
                        </th>
                        <th>
                            <a href="?<?= http_build_query(array_merge($_GET, ['sort' => 'email', 'order' => ($sort_column === 'email' && $sort_order === 'ASC') ? 'desc' : 'asc'])) ?>" style="text-decoration: none; color: inherit;">
                                <span style="background-color:#eff4f8; padding: 0.102rem 0.5rem; border-radius: 5px; color:#3f4e61; font-weight:600; font-size: 12px">
                                    Email <?= $sort_column === 'email' ? ($sort_order === 'ASC' ? '<img width="8" height="8" src="https://img.icons8.com/fluency-systems-regular/48/collapse-arrow--v2.png" alt="collapse-arrow--v2"/>' : '<img width="8" height="8" src="https://img.icons8.com/fluency-systems-filled/48/expand-arrow.png" alt="expand-arrow"/>') : '' ?>
                                </span>
                            </a>
                        </th>
                        <th>
                            <a href="?<?= http_build_query(array_merge($_GET, ['sort' => 'job_reference', 'order' => ($sort_column === 'job_reference' && $sort_order === 'ASC') ? 'desc' : 'asc'])) ?>" style="text-decoration: none; color: inherit;">
                                <span style="background-color:#eff4f8; padding: 0.102rem 0.5rem; border-radius: 5px; color:#3f4e61; font-weight:600; font-size: 12px">
                                    Job <?= $sort_column === 'job_reference' ? ($sort_order === 'ASC' ? '<img width="8" height="8" src="https://img.icons8.com/fluency-systems-regular/48/collapse-arrow--v2.png" alt="collapse-arrow--v2"/>' : '<img width="8" height="8" src="https://img.icons8.com/fluency-systems-filled/48/expand-arrow.png" alt="expand-arrow"/>') : '' ?>
                                </span>
                            </a>
                        </th>
                        <th>
                            <a href="?<?= http_build_query(array_merge($_GET, ['sort' => 'date_birth', 'order' => ($sort_column === 'date_birth' && $sort_order === 'ASC') ? 'desc' : 'asc'])) ?>" style="text-decoration: none; color: inherit;">
                                <span style="background-color:#eff4f8; padding: 0.102rem 0.5rem; border-radius: 5px; color:#3f4e61; font-weight:600; font-size: 12px">
                                    Date of Birth <?= $sort_column === 'date_birth' ? ($sort_order === 'ASC' ? '<img width="8" height="8" src="https://img.icons8.com/fluency-systems-regular/48/collapse-arrow--v2.png" alt="collapse-arrow--v2"/>' : '<img width="8" height="8" src="https://img.icons8.com/fluency-systems-filled/48/expand-arrow.png" alt="expand-arrow"/>') : '' ?>
                                </span>
                            </a>
                        </th>
                        <th>
                            <a href="?<?= http_build_query(array_merge($_GET, ['sort' => 'status', 'order' => ($sort_column === 'status' && $sort_order === 'ASC') ? 'desc' : 'asc'])) ?>" style="text-decoration: none; color: inherit;">
                                <span style="background-color:#eff4f8; padding: 0.102rem 0.5rem; border-radius: 5px; color:#3f4e61; font-weight:600; font-size: 12px">
                                    Status <?= $sort_column === 'status' ? ($sort_order === 'ASC' ? '<img width="8" height="8" src="https://img.icons8.com/fluency-systems-regular/48/collapse-arrow--v2.png" alt="collapse-arrow--v2"/>' : '<img width="8" height="8" src="https://img.icons8.com/fluency-systems-filled/48/expand-arrow.png" alt="expand-arrow"/>') : '' ?>
                                </span>
                            </a>
                        </th>
                        <th>
                            <a href="?<?= http_build_query(array_merge($_GET, ['sort' => 'gender', 'order' => ($sort_column === 'gender' && $sort_order === 'ASC') ? 'desc' : 'asc'])) ?>" style="text-decoration: none; color: inherit;">
                                <span style="background-color:#eff4f8; padding: 0.102rem 0.5rem; border-radius: 5px; color:#3f4e61; font-weight:600; font-size: 12px">
                                    Gender <?= $sort_column === 'gender' ? ($sort_order === 'ASC' ? '<img width="8" height="8" src="https://img.icons8.com/fluency-systems-regular/48/collapse-arrow--v2.png" alt="collapse-arrow--v2"/>' : '<img width="8" height="8" src="https://img.icons8.com/fluency-systems-filled/48/expand-arrow.png" alt="expand-arrow"/>') : '' ?>
                                </span>
                            </a>
                        </th>
                        <th>
                            <a href="?<?= http_build_query(array_merge($_GET, ['sort' => 'created_at', 'order' => ($sort_column === 'created_at' && $sort_order === 'ASC') ? 'desc' : 'asc'])) ?>" style="text-decoration: none; color: inherit;">
                                <span style="background-color:#eff4f8; padding: 0.102rem 0.5rem; border-radius: 5px; color:#3f4e61; font-weight:600; font-size: 12px; ">
                                    Applied On <?= $sort_column === 'created_at' ? ($sort_order === 'ASC' ? '<img width="8" height="8" src="https://img.icons8.com/fluency-systems-regular/48/collapse-arrow--v2.png" alt="collapse-arrow--v2"/>' : '<img width="8" height="8" src="https://img.icons8.com/fluency-systems-filled/48/expand-arrow.png" alt="expand-arrow"/>') : '' ?>
                                </span>
                            </a>
                        </th>
                        <th>
                            <span style="background-color:#eff4f8; padding: 0.102rem 0.5rem; border-radius: 5px; color:#3f4e61; font-weight:600; font-size: 12px">
                                Details
                            </span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr style="font-size: 12px;">
                                <td style="font-size: 12px;"><?= htmlspecialchars($row['id']) ?></td>
                                <td style="font-size: 12px;"><?= htmlspecialchars($row['eoi_number']) ?></td>
                                <td style="font-size: 12px;"><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                                <td style="font-size: 12px;"><?= htmlspecialchars($row['email']) ?></td>
                                <td style="font-size: 12px;"><?= htmlspecialchars($row['job_reference'] . ' | ' . $row['job_title'] ?? '—') ?></td>
                                <td style="font-size: 12px;"><?= htmlspecialchars($row['date_birth']) ?></td>
                                <td style="font-size: 12px;">
                                    <form method="post" class="status-update-form" style="margin: 0;">
                                        <input type="hidden" name="eoi_id" value="<?= $row['id'] ?>">
                                        <input type="hidden" name="update_status" value="1">
                                        <label for="status-<?= $row['id'] ?>" class="sr-only">-</label>
                                        <select id="status-<?= $row['id'] ?>"
                                            name="new_status"
                                            class="status <?= htmlspecialchars($row['status']) ?>"
                                            style="border: none; background: transparent; cursor: pointer; font-weight: 500;">
                                            <option style="font-size: 12px;" value="New" <?= $row['status'] === 'New' ? 'selected' : '' ?>>New</option>
                                            <option style="font-size: 12px;" value="Current" <?= $row['status'] === 'Current' ? 'selected' : '' ?>>Current</option>
                                            <option style="font-size: 12px;" value="Final" <?= $row['status'] === 'Final' ? 'selected' : '' ?>>Final</option>
                                        </select>
                                    </form>
                                </td>
                                <td style="font-size: 12px;"><?= htmlspecialchars($row['gender']) ?></td>

                                <td style="font-size: 12px;"><?= htmlspecialchars($row['created_at']) ?></td>
                                <td>
                                    <a href="#modal-<?= $row['id'] ?>"
                                        style="background-color: var(--asora-onyx-green); color: white; border: none; padding: .3rem; border-radius: 5px; cursor: pointer; font-size: 12px; text-decoration: none; display: inline-block;">
                                        View details
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" style="text-align:center;">No eoi found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <?php
            // Reset result pointer to loop again
            $stmt->execute();
            $result = $stmt->get_result();
            ?>

            <?php while ($row = $result->fetch_assoc()): ?>
                <div id="modal-<?= $row['id'] ?>" class="modal-overlay">
                    <div class="modal-box">
                        <a href="#" class="modal-close">&times;</a>
                        <h1 style="color: var(--asora-onnyx-green); margin-bottom: 20px;">Applicant Details - <?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></h1>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div>
                                <h2 style="font-weight: 600; margin-bottom: 10px; color: #3f4e61;">Skills</h2>
                                <p><?= htmlspecialchars($row['skill'] ?? 'N/A') ?></p>
                            </div>

                            <div>
                                <h2 style="font-weight: 600; margin-bottom: 10px; color: #3f4e61;">Other Skills</h2>
                                <p><?= htmlspecialchars($row['other_skill'] ?? 'N/A') ?></p>
                            </div>

                            <div>
                                <h2 style="font-weight: 600; margin-bottom: 10px; color: #3f4e61;">Phone</h2>
                                <?php if (!empty($row['phone'])): ?>
                                    <a href="<?= htmlspecialchars($row['phone']) ?>" target="_blank" style="color: var(--asora-onyx-green); word-break: break-all;">
                                        <?= htmlspecialchars($row['phone']) ?>
                                    </a>
                                <?php else: ?>
                                    <p>N/A</p>
                                <?php endif; ?>
                            </div>

                            <div>
                                <h2 style="font-weight: 600; margin-bottom: 10px; color: #3f4e61;">LinkedIn</h2>
                                <?php if (!empty($row['street_address'] . ', ' . $row['suburb'] . ' ' . $row['state'] . ' ' . $row['postcode'])): ?>
                                    <a href="<?= htmlspecialchars($row['street_address'] . ', ' . $row['suburb'] . ' ' . $row['state'] . ' ' . $row['postcode']) ?>" target="_blank" style="color: var(--asora-onyx-green); word-break: break-all;">
                                        <?= htmlspecialchars($row['street_address'] . ', ' . $row['suburb'] . ' ' . $row['state'] . ' ' . $row['postcode']) ?>
                                    </a>
                                <?php else: ?>
                                    <p>N/A</p>
                                <?php endif; ?>
                            </div>

                            <div>
                                <h2 style="font-weight: 600; margin-bottom: 10px; color: #3f4e61;">Portfolio</h2>
                                <?php if (!empty($row['portfolio'])): ?>
                                    <a href="<?= htmlspecialchars($row['portfolio']) ?>" target="_blank" style="color: var(--asora-onyx-green); word-break: break-all;">
                                        <?= htmlspecialchars($row['portfolio']) ?>
                                    </a>
                                <?php else: ?>
                                    <p>N/A</p>
                                <?php endif; ?>
                            </div>

                            <div>
                                <h2 style="font-weight: 600; margin-bottom: 10px; color: #3f4e61;">LinkedIn</h2>
                                <?php if (!empty($row['linkedin'])): ?>
                                    <a href="<?= htmlspecialchars($row['linkedin']) ?>" target="_blank" style="color: var(--asora-onyx-green); word-break: break-all;">
                                        <?= htmlspecialchars($row['linkedin']) ?>
                                    </a>
                                <?php else: ?>
                                    <p>N/A</p>
                                <?php endif; ?>
                            </div>

                            <div>
                                <h2 style="font-weight: 600; margin-bottom: 10px; color: #3f4e61;">GitHub</h2>
                                <?php if (!empty($row['githubl'])): ?>
                                    <a href="<?= htmlspecialchars($row['githubl']) ?>" target="_blank" style="color: var(--asora-onyx-green); word-break: break-all;">
                                        <?= htmlspecialchars($row['githubl']) ?>
                                    </a>
                                <?php else: ?>
                                    <p>N/A</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>


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

    </main>
</body>

</html>