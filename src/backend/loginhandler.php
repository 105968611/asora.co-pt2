<?php 
session_start();
include_once './database/settings.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
  echo " Please fill in both username and password.";
  exit;
}

//prepare function prepares the sql cmd but doesn't run it
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");

// same thing here, we don't want to expose error details to client-side
if (!$stmt) {
  error_log("Statement preparation failed: " . $conn->error);
  die("Something went wrong. Please try again later.");
}

// binds the '?' in the sql cmd to the variable $input_username (s means string and its linked to ?)
$stmt->bind_param("s", $username);

// finally executing the sql cmd [execute() is a function]
$stmt->execute();

// result obtained as pointer
$result = $stmt->get_result();

// WE did this to prevent sql injection, by making sure user input for username is treated as data and not part of sql cmd 

$user = $result->fetch_assoc(); // readable array (associative array)

// If user found, log them in
if ($user && password_verify($password, $user['password_hash'])) {
    $_SESSION['username'] = $user['username'];
    header("Location: ./manage.php");
    exit();
} else {
    echo "Incorrect username or password.";
}

// Close connection
mysqli_close($conn);
}
