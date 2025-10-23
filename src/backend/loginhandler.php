<?php 
include_once './database/settings.php';



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($db_password);
        $stmt->fetch();

        if ($password === $db_password) {
            $message = "Login successful";
            

            session_start();
            $_SESSION['username'] = $username;
            header("Location: ./manage.php");
            exit();
        } else {
            $message = "Incorrect password";
            
        }
    } else {
        $message = "username not found";
        
    }

    $stmt->close();
    $conn->close();
}
?>