<?php

//Redirects the user to apply URL the form is not submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['submit'])) {
    header('Location: ../apply.php');
    exit();
}

include_once '../database/settings.php';



//Collect all inputs from apply form
$job_reference = trim($_POST['job_reference'] ?? '');
$first_name = trim($_POST['first_name'] ?? '');
$last_name = trim($_POST['last_name'] ?? '');
$date_birth = date('Y-m-d', strtotime($_POST['date_birth']) ?? '');
$gender = $_POST['gender'] ?? '';
$street_address = trim($_POST['street_address'] ?? '');
$suburb = trim($_POST['suburb'] ?? '');
$state = trim($_POST['state'] ?? '');
$postcode = trim($_POST['postcode'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$skill = $_POST['skill'] ?? []; //<--Store selected skills into array
$other_skill = trim($_POST['other_skill'] ?? '');
$attachment = trim($_POST['attachment'] ?? '');
$portfolio = trim($_POST['portfolio'] ?? '');
$linkedin = trim($_POST['linkedin'] ?? '');
$github = trim($_POST['github'] ?? '');
$skills_str = implode(', ', $skill); //<--Combine all skill selected into a single string


//Validator server side
$require = [];

    // Job Reference validation
    if (empty($job_reference)) {
        $require[] = "Job reference is required.";
    }

    // First name validation
    if (empty($first_name)) { //<--Evaluates the content
        $require[] = "First name is required.";
    } elseif (strlen($first_name) > 20) {
        $require[] = "First name must be 20 characters or less.";
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $first_name)) {  //<--Evaluates the pattern
        $require[] = "First name should contain only letters and spaces.";//<--'Error' message if it is not valid
    }

    // Last name validation
    if (empty($last_name)) {
        $require[] = "Last name is required.";
    } elseif (strlen($last_name) > 20) {
        $require[] = "Last name must be 20 characters or less.";
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $last_name)) {
        $require[] = "Last name should contain only letters and spaces.";
    }

    
    // Gender validation
    if (empty($gender)) {
        $require[] = "Gender is required.";
    } elseif (!in_array($gender, ['Female', 'Male'])) {
        $require[] = "Please select a valid gender option.";
    }

    // Street address validation
    if (empty($street_address)) {
        $require[] = "Street address is required.";
    } elseif (strlen($street_address) > 40) {
        $require[] = "Street address must be 40 characters or less.";
    }

    // Suburb validation
    if (empty($suburb)) {
        $require[] = "Suburb/Town is required.";
    } elseif (strlen($suburb) > 40) {
        $require[] = "Suburb/Town must be 40 characters or less.";
    }

    // State validation
    $valid_states = ['VIC', 'NSW', 'QLD', 'NT', 'WA', 'SA', 'TAS', 'ACT'];
    if (empty($state)) {
        $require[] = "State is required.";
    } elseif (!in_array($state, $valid_states)) {
        $require[] = "Please select a valid state.";
    }

    // Postcode validation
    if (empty($postcode)) {
        $require[] = "Postcode is required.";
    } elseif (!preg_match("/^[0-9]{4}$/", $postcode)) {
        $require[] = "Postcode must be exactly 4 digits.";
    }

    // Email validation
    if (empty($email)) {
        $require[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $require[] = "Please enter a valid email address.";
    }

    // Phone validation
    if (empty($phone)) {
        $require[] = "Phone number is required.";
    } elseif (!preg_match("/^[0-9]{8,12}$/", $phone)) {
        $require[] = "Phone number must be between 8 and 12 digits.";
    }

    // Skills validation
    if (empty($skill)) {
        $require[] = "Please select at least one skill.";
    }


    if (!empty($require)) {
        // Store errors in session or redirect back with error message
        session_start();
        $_SESSION['errors'] = $require;
        $_SESSION['form_data'] = $_POST; // Preserve form data
        header('Location: ../apply.php?job_reference=' . urlencode($job_reference));
        exit();
    }


//Create table in DB if it doesn't exist
$createEoiTable = "
        CREATE TABLE IF NOT EXISTS eoi(
            id INT AUTO_INCREMENT PRIMARY KEY,
            eoi_number VARCHAR(50) UNIQUE NOT NULL,
            job_reference VARCHAR(100),
            first_name VARCHAR(100),
            last_name VARCHAR(100),
            date_birth DATE,
            gender VARCHAR(20),
            street_address VARCHAR(255),
            suburb VARCHAR(100),
            state VARCHAR(100),
            postcode VARCHAR(10),
            email VARCHAR(150),
            phone VARCHAR(30),
            skill TEXT,
            other_skill TEXT,
            attachment VARCHAR(255),
            portfolio VARCHAR(255),
            linkedin VARCHAR(255),
            github VARCHAR(255),
            status ENUM('New', 'Current', 'Final') DEFAULT 'New',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;";

$conn->query($createEoiTable);

//Create EOINumber 

$eoi_number = 'EOI' . date('Ym') . strtoupper(substr(md5(uniqid(rand(), true)), 0, 6)); //<--Concat the following data to create a unique EOINumber
$check_stmt = $conn->prepare("SELECT eoi_number FROM eoi WHERE eoi_number = ?");
$check_stmt->bind_param("s", $eoi_number);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

while ($check_result->num_rows > 0) { //<--Loop and check the existiance of data
    $eoi_number = 'EOI' . date('Ym') . strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));
    $check_stmt->bind_param("s", $eoi_number);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
}
$check_stmt->close();

//Prepare and Bind protects inputs from SQL ingections: user input can’t modify the SQL structure
$stmt = $conn->prepare("
        INSERT INTO eoi (
            eoi_number, job_reference, first_name, last_name, date_birth, gender,
            street_address, suburb, state, postcode, email, phone,
            skill, other_skill, attachment, portfolio, linkedin, github
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}


$stmt->bind_param(
    "ssssssssssssssssss",
    $eoi_number,
    $job_reference,
    $first_name,
    $last_name,
    $date_birth,
    $gender,
    $street_address,
    $suburb,
    $state,
    $postcode,
    $email,
    $phone,
    $skills_str,
    $other_skill,
    $attachment,
    $portfolio,
    $linkedin,
    $github
);

if ($stmt->execute()) {
    // Redirect to success modal with EOI number and applicants details
    session_start();
    $_SESSION['success_eoi'] = $eoi_number;
    $_SESSION['success_name'] = $first_name . ' ' . $last_name;
    $_SESSION['success_job'] = $job_reference;
    $_SESSION['success_email'] = $email;
    //Clears applicant data on success
    unset($_SESSION['form_data']);
    header('Location: ../apply.php?success=1');

    exit();
} else {
    // Handle erros by redirecting back
    session_start();
    $_SESSION['errors'] = ["Could not submit application: " . $stmt->error];
    $_SESSION['form_data'] = $_POST;
    header('Location: ../apply.php?job_reference=' . urlencode($job_reference));
    exit();
}


$stmt->close();
$conn->close();
