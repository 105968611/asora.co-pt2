<?php
include_once '../database/settings.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    //Crete table in DB if it doesn't exist
    $createTableSQL = "
        CREATE TABLE IF NOT EXISTS jobs (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `job_reference` VARCHAR(100) NOT NULL,
            `job_title` VARCHAR(150) NOT NULL,
            `job_summary` TEXT NOT NULL,
            `job_city` VARCHAR(80) NOT NULL,
            `job_mode` ENUM('On-site','Hybrid','Remote') NOT NULL,
            `job_type` ENUM('Full-time','Part-time','Casual','Contract') NOT NULL,
            `job_salary` VARCHAR(50) DEFAULT NULL,
            `job_manager` VARCHAR(160) DEFAULT NULL,
            `job_department` VARCHAR(120) DEFAULT NULL,
            `job_responsibilities` LONGTEXT DEFAULT NULL,
            `job_essential` LONGTEXT DEFAULT NULL,
            `job_preferable` LONGTEXT DEFAULT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;
    ";
    
    $conn->query($createTableSQL);

    //Prepare data to be insergested into the jobs table in the DB
    if ($action === 'create') {
        $reference = trim($_POST['reference']);
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $location = trim($_POST['location']);

        $stmt = $conn->prepare("INSERT INTO jobs (reference, title, description, location) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $reference, $title, $description, $location);


        //Error handler for job post creation
        if ($stmt->execute()) {
            echo "Job post created successfully.";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }

    //Delete a job post from the DB
    if ($action === 'delete') {
        $id = intval($_POST['id']);
        $conn->query("DELETE FROM jobs WHERE id = $id");
    }

    if ($action === 'update') {
        $id = intval($_POST['id']); //<-- Returns the id int value
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $location = trim($_POST['location']);

        $stmt = $conn->prepare("UPDATE jobs SET title=?, description=?, location=? WHERE id=?");
        $stmt->bind_param("sssi", $title, $description, $location, $id);
        $stmt->execute();
        $stmt->close();
    }
}
