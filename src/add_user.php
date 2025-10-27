<?php
session_start();
include_once './database/settings.php';

// Only allow admin users ro add users.Non-admin users will be redirected to manage page
if (!isset($_SESSION['username'])) {
    header("Location: ./login.php");
    exit();
}
//Statement and query to call data from DB 
$username = $_SESSION['username'];
$stmt = $conn->prepare("SELECT role FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Redirect non-admin users
if (!$user || $user['role'] !== 'admin') {
    header("Location: ./manage.php"); 
    exit();
}

//Success or fail message 
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_username = trim($_POST['new_username'] ?? '');
    $new_password = trim($_POST['new_password'] ?? '');
    $role = $_POST['role'] ?? 'user';

    if (empty($new_username) || empty($new_password)) {
        $message = "Please fill in both username and password.";
    } else {
        // Hash password with salt
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);

        // Insert new user
        $stmt = $conn->prepare("INSERT INTO users (username, password_hash, role) VALUES (?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("sss", $new_username, $hashed, $role);
            if ($stmt->execute()) {
                $message = "User '$new_username' added successfully.";
            } else {
                $message = "Username already exists or error occurred.";
            }
            $stmt->close();
        } else {
            $message = "Database error. Try again later.";
        }
    }
}

// Fetch all users for table displauy
$users_result = $conn->query("SELECT id, username, role FROM users");
?>

<?php 
$page_title = "Add Users - Asora";
include './includes/header.inc.php'; ?>


<body>
<main>
    <?php include './includes/navbar_management.inc.php'; ?>


    <div class="user-management-wrapper" >
        <!-- Add users form to the left -->
        <div class="add-user-section" >
            <h1 style="font-weight: 400; margin-bottom: 1.5rem;">Add New User</h1>
            
            <!--Succes message when as user is added to DB-->
            <?php if (!empty($message)): ?>
                <p style="padding: 15px; margin-bottom: 20px; border-radius: 8px; background: #e8f5e9; color: #2e7d32;">
                    <?php echo htmlspecialchars($message); ?>
                </p>
            <?php endif; ?>

            <form method="POST" style="display:flex; flex-direction:column; gap:1em; border:none;">
                <label for="new_username" class="sr-only">New username</label>
                <input id="new_username" type="text" class="box_input" name="new_username" placeholder="New Username" required>
                <label for="new_password" class="sr-only">New Password</label>
                <input id="new_password" type="password" class="box_input" name="new_password" placeholder="New Password" required>
                <label for="role" class="sr-only">Role</label>
                <select id="role" name="role" class="box_input" required> <!--Role level permission-->
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
                <button type="submit" class="submit_btn">Create User</button>
            </form>
        </div>

        <!-- Table with users infromation -->
        <div class="users-table-section">
            <h2 style="font-weight: 400; margin-bottom: 1.5rem;">All Users</h2>
            
            <!--Conditional statement to check existence of data-->
            <?php if ($users_result && $users_result->num_rows > 0): ?>
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Role</th>
                        </tr>
                    </thead>
                    <tbody>
                         <!--Once data is checked it fetches all records through a loop-->
                        <?php while ($user = $users_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['id']); ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td>
                                    <span class="role-badge role-<?php echo htmlspecialchars($user['role']); ?>">
                                        <?php echo htmlspecialchars(ucfirst($user['role'])); ?>
                                    </span>
                                </td>
                                <td>
                                
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                        <!--Error handling, if there is not data the message will be display-->
            <?php else: ?>
                <p style="color: #666; text-align: center; padding: 2rem;">No users found.</p>
            <?php endif; ?>
        </div>
    </div>
</main>
</body>

</html>