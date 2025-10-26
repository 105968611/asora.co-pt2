<?php

session_start();

require './database/settings.php';

if (!isset($_SESSION['errors']) && !isset($_GET['job_reference'])) {
    unset($_SESSION['form_data']);
}

// Clear form data if explicitly requested
if (isset($_GET['clear_form'])) {
    unset($_SESSION['form_data']);
}


$job_reference = $_GET['job_reference'] ?? $_GET['job_reference'] ?? '';
$job_title = $_GET['job_title'] ?? '';

// If job_reference exists but no job_title, fetch it from database
if ($job_reference && !$job_title) {
    $stmt = $conn->prepare("SELECT job_title FROM jobs WHERE job_reference = ?");
    $stmt->bind_param("s", $job_reference);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $job_title = $row['job_title'];
    }
    $stmt->close();
}

require './includes/header.inc.php';


//Store values on session
$show_success_modal = isset($_SESSION['success_eoi']);
$eoi_number = $_SESSION['success_eoi'] ?? '';
$applicant_name = $_SESSION['success_name'] ?? '';
$job_ref = $_SESSION['success_job'] ?? '';
$success_email = $_SESSION['success_email'] ?? '';

$form_data =$_SESSION['form_data'] ?? [];

// Clear session variables
if ($show_success_modal) {
    unset($_SESSION['success_eoi']);
    unset($_SESSION['success_name']);
    unset($_SESSION['success_job']);
    unset($_SESSION['success_email']);
}

// Display error messages if there is information missing or incorrect values
if (isset($_SESSION['errors'])) {
    echo '<div style="background-color: #f8d7da; color: #721c24; padding: 1rem; margin: 1rem; border-radius: 5px; border: 1px solid #f5c6cb;">';
    echo '<h3 style="margin-top: 0;">Please correct the following errors:</h3>';
    echo '<ul style="margin: 0; padding-left: 1.5rem;">';
    foreach ($_SESSION['errors'] as $error) {
        echo '<li>' . htmlspecialchars($error) . '</li>';
    }
    echo '</ul>';
    echo '</div>';
    unset($_SESSION['errors']);
}
?>
<body>

<?php require('./includes/navbar.inc.php'); ?>
<!--Form page header-->
<div class="header_spacer">
    <div class="pages_path">
        <p> <a href="index.html">Home</a> > <a href="jobs.html">Careers</a> > <a href="apply.html">Apply</a> </p>
    </div>

    <div class="page_title">
        <h1 style="font-weight: 500;">Apply Now <img width="32" height="32" src="https://img.icons8.com/windows/32/expand-arrow--v1.png"
                alt="expand-arrow--v1" /> </h1>
        <h3 style="font-weight: 300;">for 
        <span style="background-color:#eff4f8; padding: 0.5rem; border-radius: 10px; color:#3f4e61; font-weight:500;">
            <?= htmlspecialchars($job_title)?> <!--Uses job title and display it on top. Synch with filter an 'apply now' button from jobs card-->
        </span></h3>
    </div>
</div>

<!--Basic Form apply.html-->
<div class="form_wrapper">

    <form action="./backend/process_eoi.php" method="POST">
        <div class="form_container">
            <input type="hidden" name="job_reference" value="<?= htmlspecialchars($job_reference) ?>">

            <p>
                <legend>Your role</legend>
            </p>
            <select name="job_reference" id="job_reference" class="box_input" onchange="window.location.href='apply.php?job_reference=' + this.value;">
                <option value="">-- Choose your role --</option>
                <!--Dynamically changes role title in apply form if using filter-->
                <?php
                $jobs_result = $conn->query("SELECT job_reference, job_title FROM jobs ORDER BY created_at DESC");
                while ($job = $jobs_result->fetch_assoc()):
                ?>
                    <option value="<?= htmlspecialchars($job['job_reference']) ?>"
                        <?= ($job['job_reference'] === $job_reference || ($form_data['job_reference'] ?? '') === $job['job_reference']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($job['job_title']) ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <p>
                <legend>About you</legend>
            </p>
            <hr>

            <!--Fisrt name field-->
            <div class="box_form">
                <label for="first_name" class="box_label">First name <span class="req">*</span></label>
                <input type="text" id="first_name" class="box_input" name="first_name"
                    title="Name should be max 20 alpha characters long and contain only letters and spaces" placeholder="Enter your full name" value="<?= htmlspecialchars($form_data['first_name'] ?? '') ?>">
            </div>

            <!--Last name field-->
            <div class="box_form">
                <label for="last_name" class="box_label">Last name <span class="req">*</span></label>
                <input type="text" id="last_name" class="box_input" name="last_name" title="Lastname should max 20 alpha characters long and contain only letters and spaces" placeholder="Enter your lastname"
                    value="<?= htmlspecialchars($form_data['last_name'] ?? '') ?>">
            </div>

            <!--Date of birth field-->
            <div class="box_form">
                <label for="date_birth" class="box_label">Date of Birth <span class="req">*</span></label>
                <input type="text" name="date_birth" class="box_input" id="date_birth" placeholder="dd/mm/yyyy" value="<?= htmlspecialchars($form_data['date_birth'] ?? '') ?>">

            </div>

            <!--Gender field-->
            <div class="box_form">
                <fieldset style="width: 100%;">
                    <legend for="gender" class="box_label">Gender <span class="req">*</span></legend>
                    <div class="box_input">
                        <input type="radio" name="gender" id="apl_female" value="Female"
                        <?= ($form_data['gender'] ?? '') === 'Female' ? 'checked' : '' ?>>
                        <label for="apl_female">Female</label>
                        <input type="radio" name="gender" id="apl_male" value="Male"
                        <?= ($form_data['gender'] ?? '') === 'Male' ? 'checked' : '' ?>>
                        <label for="apl_male">Male</label>
                    </div>
                </fieldset>
            </div>

            <!--Street address field-->
            <div class="box_form">
                <label for="street_address" class="box_label">Street Address <span class="req">*</span></label>
                <input type="text" id="street_address" name="street_address" class="box_input" 
                    placeholder="Enter your street address" value="<?= htmlspecialchars($form_data['street_address'] ?? '') ?>">
            </div>

            <!--Suburb Town-->
            <div class="box_form">
                <label for="suburb" class="box_label">Suburb/Town <span class="req">*</span></label>
                <input type="text" id="suburb" name="suburb" class="box_input"
                    placeholder="Enter your suburb name" value="<?= htmlspecialchars($form_data['street_suburb'] ?? '') ?>">

            </div>

            <!--State field-->
            <div class="box_form">
                <label for="state" class="box_label">State <span class="req">*</span></label>
                <select name="state" id="state" class="box_input"  >
                    <option value="VIC"<?= ($form_data['state'] ?? '') === 'VIC' ? 'selected' : '' ?>>VIC</option>
                    <option value="NSW"<?= ($form_data['state'] ?? '') === 'NSW' ? 'selected' : '' ?>>NSW</option>
                    <option value="QLD"<?= ($form_data['state'] ?? '') === 'QLD' ? 'selected' : '' ?>>QLD</option>
                    <option value="NT"<?= ($form_data['state'] ?? '') === 'NT' ? 'selected' : '' ?>>NT</option>
                    <option value="WA"<?= ($form_data['state'] ?? '') === 'WA' ? 'selected' : '' ?>>WA</option>
                    <option value="SA"<?= ($form_data['state'] ?? '') === 'SA' ? 'selected' : '' ?>>SA</option>
                    <option value="TAS"<?= ($form_data['state'] ?? '') === 'TAS' ? 'selected' : '' ?>>TAS</option>
                    <option value="ACT"<?= ($form_data['state'] ?? '') === 'ACT' ? 'selected' : '' ?>>ACT</option>
                </select>
            </div>

            <!--Postcode field-->
            <div class="box_form">
                <label for="postcode" class="box_label">Postcode <span class="req">*</span></label>
                <input type="number" id="postcode" name="postcode" class="box_input"
                    placeholder="Enter your postcode" value="<?= htmlspecialchars($form_data['postcode'] ?? '') ?>">
            </div>

            <!--Email field-->
            <div class="box_form">
                <label for="email" class="box_label">Email <span class="req">*</span></label>
                <input type="email" class="box_input" id="email" name="email"  
                    placeholder="Enter your email" value="<?= htmlspecialchars($form_data['email'] ?? '') ?>">
            </div>

            <!--Phone number field-->
            <div class="box_form">
                <label for="phone" class="box_label">Phone number <span class="req">*</span></label>
                <input type="number" id="phone" name="phone" class="box_input" 
                    placeholder="Enter your phone number" value="<?= htmlspecialchars($form_data['phone'] ?? '') ?>">
            </div>

            <!--Skill list field-->
            <div class="box_form">
                <fieldset class="vertical_check">
                    <legend class="box_label">Skill list<span class="req">*</span></legend>
                    <label><input type="checkbox" name="skill[]" value="Data Quality Principles" <?= in_array('Data Quality Principles', $form_data['skill'] ?? []) ? 'checked' : '' ?>> Data Quality Principles</label>
                    <label><input type="checkbox" name="skill[]" value="Data privacy laws & compliance" <?= in_array('Data privacy laws & compliance', $form_data['skill'] ?? []) ? 'checked' : '' ?>> Data privacy laws & compliance</label>
                    <label><input type="checkbox" name="skill[]" value="Problem-Solving & Critical Thinking" <?= in_array('Problem-Solving & Critical Thinking', $form_data['skill'] ?? []) ? 'checked' : '' ?>> Problem-Solving & Critical Thinking</label>
                    <label><input type="checkbox" name="skill[]" value="Leadership & Management" <?= in_array('Leadership & Management', $form_data['skill'] ?? []) ? 'checked' : '' ?>> Leadership & Management</label>
                </fieldset>
            </div>


            <!--Other skills field-->
            <div class="box_form">
                <label for="other_skill" class="box_label">Other Skills</label>
                <textarea id="other_skills" name="other_skill" rows="4" class="box_input"placeholder="Describe your skills"><?= htmlspecialchars($form_data['other_skill'] ?? '') ?></textarea>

            </div>

            <hr>

            <!--Attachment section-->
            <div class="box_form">
                <label for="attachment" class="box_label">Attach your CV</label>
                <input type="file" id="cv" name="attachment" accept=".pdf,.doc,.docx" class="box_attach">
            </div>

            <!--Potfolio Section-->
            <div class="box_form">
                <label for="portfolio" class="box_label">Link to your Portfolio<img width="28" height="28"
                        src="https://img.icons8.com/windows/32/1d2628/person-male.png" alt="person-male" /></label>
                <input type="url" id="portfolio" name="portfolio" class="box_input"
                    placeholder="Enter your portfolio URL" value="<?= htmlspecialchars($form_data['portfolio'] ?? '') ?>">
            </div>

            <!--LinkedIn Section-->
            <div class="box_form">
                <label for="linkedin" class="box_label">Link to your LinkedIn<img width="28" height="28"
                        src="https://img.icons8.com/windows/32/1d2628/linkedin-2.png" alt="linkedin-2" /></label>
                <input type="url" id="linkedin" name="linkedin" class="box_input"
                    placeholder="Enter your Linkedin URL" value="<?= htmlspecialchars($form_data['linkedin'] ?? '') ?>">
            </div>

            <!--GitHub Section-->
            <div class="box_form">
                <label for="github" class="box_label">Link to your GitHub <img width="28" height="28"
                        src="https://img.icons8.com/windows/32/1d2628/github.png" alt="github_logo" /></label>
                <input type="url" id="github" name="github" class="box_input"
                    placeholder="Enter your Linkedin URL" value="<?= htmlspecialchars($form_data['github'] ?? '') ?>">
            </div>

            <!--Form buttons RESET & SUBMIT-->
            <div class="buttons_form">
                <button type="reset" class="reset_btn">Reset Application</button>
                <button type="submit" class="submit_btn" name="submit">Submit Application</button>
            </div>
        </div>
    </form>
</div>

<!-- Success Modal -->
<!--If form is submitted succesfully show modal with confirmation message-->
<?php if (isset($show_success_modal) && $show_success_modal): ?>

<div id="success-modal" class="modal" style="visibility: visible; opacity: 1;">
    <div class="modalcontent" style="max-width: 600px;">
        <div class="modal-body" style="text-align: center; padding: 2rem;">
            <div>
                <span class="modal-style">✓</span>
            </div>

            <h2 style="font-weight:500; color: var(--asora-stormy-sea); margin-bottom: 1rem;">Application Submitted Successfully!</h2>
            
            <!--Applicant's detail section using apply from values-->
            <div class="app-details">
                <p><strong>Applicant:</strong> <?= htmlspecialchars($applicant_name) ?></p>
                <p><strong>Job Reference:</strong> <?= htmlspecialchars($job_ref) ?></p>
                <hr style="margin: 1rem 0; border: none; border-top: 1px solid #dee2e6;">
                <p style="margin: 0.7rem 0; font-size: 1.1rem; color: var(--asora-onyx-green); font-weight: 600;">Your EOI Number:</p>
                <p style="font-size: 1.7rem; font-weight: bold; color: var(--asora-stormmy-sea); margin: 0.5rem 0; letter-spacing: 3px; font-family: monospace;">
                    <?= htmlspecialchars($eoi_number) ?>
                </p>
            </div>
            
            <p style="color: #666; font-size: 0.95rem;">
                Please save this EOI number for your records.<br>
                We will contact you at <strong><?= htmlspecialchars($success_email) ?></strong>
            </p>
        </div>

        <!--Modal footer with buttons-->
        <div class="succ-modal-footer" >
            <a class="submit_btn" href="jobs.php" style="font-weight: 400">View Other Positions</a>
            <a href="apply.php" class="modalfor">Close</a>
        </div>
    </div>
</div>
<?php endif; ?>

<!--Footer Creds/Made by Hannah-->
<?php include './includes/footer.inc.php'; ?>
</body>

</html>