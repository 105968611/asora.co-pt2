<?php include_once './database/settigs.php'; ?>


<?php require './includes/header.inc.php';?>


    <div class="header_spacer">
        <div class="pages_path">
            <p> <a href="index.html">Home</a> > <a href="jobs.html">Careers</a> > <a href="apply.html">Apply</a> </p>
        </div>

        <div class="page_title">
            <h1>Apply Now <img width="32" height="32" src="https://img.icons8.com/windows/32/expand-arrow--v1.png"
                    alt="expand-arrow--v1" /> </h1>
        </div>
    </div>

    <!--Basic Form apply.html-->
    <div class="form_wrapper">
        <form action="#" method="POST">
            <div class="form_container">

                <p>
                    <legend>Your role</legend>
                </p>

                <div class="box_form">
                    <hr>
                    <label for="job_reference">Select the role you are applying for</label>
                    <select name="job_reference" id="role" class="box_input" required>
                        <option value="">-- Choose your role --</option>
                        <option value="da1a2">Data Analyst</option>
                        <option value="sn9m3">Systems and Network Administrator</option>
                        <option value="ml3k8">Machine Learning Engineer</option>
                        <option value="ma5q1">Management Accountant</option>
                        <option value="sc7d4">Security Consultant</option>
                        <option value="do4v6">DevOps Engineer</option>
                        <option value="ux2l9">UI/UX Designer Lead</option>
                        <option value="cs805">Cyber Security Operations Lead</option>
                        <option value="de3f7">Data Engineer</option>
                        <option value="ba7k2">Business Analyst</option>
                        <option value="fs4n8">Full-Stack Software Engineer</option>
                        <option value="sa2h5">Solutions Architect</option>
                        <option value="cim8br">Cloud & Infrastructure Manager</option>
                        <option value="dse5p9">Data Security Engineer</option>
                        <option value="sya9c3">Systems Architect</option>
                        <option value="dpo3m6">Data Privacy Officer</option>
                        <option value="fe5a1">Data Privacy Officer</option>
                        <option value="gr1q9">Graduate (Software Developer) EOI</option>
                        <option value="dar7v2">Data Architect</option>
                        <option value="cso7b5">Cyber Security Operations Lead</option>
                    </select>
                </div>

                <p>
                    <legend>About you</legend>
                </p>
                <hr>

                <!--Fisrt name field-->
                <div class="box_form">
                    <label for="first_name" class="box_label">First name <span class="req">*</span></label>
                    <input type="text" id="first_name" class="box_input" name="first_name" required maxlength="20"
                        pattern="[a-zA-Z\s]+"
                        title="Name should be max 20 alpha characters long and contain only letters and spaces"
                        placeholder="Enter your full name">
                </div>

                <!--Last name field-->
                <div class="box_form">
                    <label for="last_name" class="box_label">Last name <span class="req">*</span></label>
                    <input type="text" id="last_name" class="box_input" name="last_name" required maxlength="20"
                        pattern="[a-zA-Z\s]+"
                        title="Lastname should max 20 alpha characters long and contain only letters and spaces"
                        placeholder="Enter your lastname">
                </div>

                <!--Date of birth field-->
                <div class="box_form">
                    <label for="date_birth" class="box_label">Date of Birth <span class="req">*</span></label>
                    <input type="text" name="date_birth" class="box_input" id="date_birth" required
                        pattern="[0-3][0-9]/[0-1][0-9]/[0-9]{4}" placeholder="dd/mm/yyyy"
                        title="Date of birth should be dd/mm/yyyy">

                </div>

                <!--Gender field-->
                <div class="box_form">
                    <fieldset style="width: 100%;">
                        <legend for="gender" class="box_label">Gender <span class="req">*</span></legend>
                        <div class="box_input">
                            <input type="radio" name="gender" id="apl_female" value="Female" required
                                title="Select one option">
                            <label for="apl_female">Female</label>
                            <input type="radio" name="gender" id="apl_male" value="Male">
                            <label for="apl_male">Male</label>
                        </div>
                    </fieldset>
                </div>

                <!--Street address field-->
                <div class="box_form">
                    <label for="street_address" class="box_label">Street Address <span class="req">*</span></label>
                    <input type="text" id="street_address" name="street_address" class="box_input" maxlength="40" required
                        placeholder="Enter your street address">
                </div>

                <!--Suburb Town-->
                <div class="box_form">
                    <label for="suburb" class="box_label">Suburb/Town <span class="req">*</span></label>
                    <input type="text" id="suburb" name="suburb" class="box_input" maxlength="40" requried
                        placeholder="Enter your suburb name">

                </div>

                <!--State field-->
                <div class="box_form">
                    <label for="state" class="box_label">State <span class="req">*</span></label>
                    <select name="state" id="state" class="box_input" required>
                        <option value="VIC">VIC</option>
                        <option value="NSW">NSW</option>
                        <option value="QLD">QLD</option>
                        <option value="NT">NT</option>
                        <option value="WA">WA</option>
                        <option value="SA">SA</option>
                        <option value="TAS">TAS</option>
                        <option value="ACT">ACT</option>
                    </select>
                </div>

                <!--Postcode field-->
                <div class="box_form">
                    <label for="postcode" class="box_label">Postcode <span class="req">*</span></label>
                    <input type="number" id="postcode" name="postcode" class="box_input" required pattern="\d{4}"
                        placeholder="Enter your postcode">
                </div>

                <!--Email field-->
                <div class="box_form">
                    <label for="email" class="box_label">Email <span class="req">*</span></label>
                    <input type="email" class="box_input" id="email" name="email" required
                        placeholder="Enter your email">
                </div>

                <!--Phone number field-->
                <div class="box_form">
                    <label for="phone" class="box_label">Phone number <span class="req">*</span></label>
                    <input type="number" id="phone" name="phone" class="box_input" required pattern="\d{8-12}"
                        placeholder="Enter your phone number">
                </div>

                <!--Skill list field-->
                <div class="box_form">
                    <fieldset class="vertical_check">
                        <legend for="apl_skill" class="box_label">Skill list<span class="req">*</span></legend>
                        <label for="skill1"><input type="checkbox" name="skill1" id="skill1" value="skill1" required>Data
                            Quality Principles</label>
                        <label for="skill2"><input type="checkbox" name="skill2" id="skill2" value="skill2">Data privacy
                            laws & compliance</label>
                        <label for="skill3"><input type="checkbox" name="skill3" id="skill3"
                                value="skill3">Problem-Solving & Critical Thinking</label>
                        <label for="skill4"><input type="checkbox" name="skill4" id="skill4" value="skill4">Leadership &
                            Management</label>
                    </fieldset>
                </div>

                <!--Other skills field-->
                <div class="box_form">
                    <label for="other_skill" class="box_label">Other Skills</label>
                    <textarea id="other_skills" name="other_skill" rows="4" class="box_input"
                        placeholder="Describe your skills"></textarea>

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
                        placeholder="Enter your portfolio URL">
                </div>

                <!--LinkedIn Section-->
                <div class="box_form">
                    <label for="linkedin" class="box_label">Link to your LinkedIn<img width="28" height="28"
                            src="https://img.icons8.com/windows/32/1d2628/linkedin-2.png" alt="linkedin-2" /></label>
                    <input type="url" id="linkedin" name="linkedin" class="box_input"
                        placeholder="Enter your Linkedin URL">
                </div>

                <!--GitHub Section-->
                <div class="box_form">
                    <label for="github" class="box_label">Link to your GitHub <img width="28" height="28"
                            src="https://img.icons8.com/windows/32/1d2628/github.png" alt="github_logo" /></label>
                    <input type="url" id="github" name="github" class="box_input"
                        placeholder="Enter your Linkedin URL">
                </div>

                <!--Form buttons RESET & SUBMIT-->
                <div class="buttons_form">
                    <button type="reset" class="reset_btn">Reset Application</button>
                    <button type="submit" class="submit_btn" name="submit">Submit Application</button>
                </div>
            </div>
        </form>
    </div>

    <!--Footer Creds/Made by Hannah-->
    <?php include './includes/footer.inc.php'; ?>
</body>

</html>

<?php

    if ($_POST['submit']) {
        $job_reference = $_POST['job_reference'];
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $date_birth = $_POST['date_birth'];
        $gender = $_POST['gender'];
        $street_address = $_POST['street_address'];
        $suburb = $_POST['suburb'];
        $state = $_POST['state'];
        $postcode = $_POST['postcode'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $skill_1 = $_POST['skill_1'];
        $skill_2 = $_POST['skill_2'];
        $skill_3 = $_POST['skill_3'];
        $skill_4 = $_POST['skill_4'];
        $other_skill = $_POST['other_skill'];
        $attachment = $_POST['attachment'];
        $portfolio = $_POST['portfolio'];
        $linkedin = $_POST['linkedin'];
        $github = $_POST['github'];

        $query = "INSERT INTO eio values('$job_reference','$first_name','$last_name','$date_birth','$gender','$street_address','$suburb','$state','$postcode','$email','$skill_1','$skill_2','$skill_3', '$skill_4', '$other_skill', '$attachment', '$portfolio', '$linkedin', '$github', '$status')";
        $data = mysqli_query($conn, $query);

        if ($data) {
            echo "Data inserted into Database";
        } else {
            echo "Failed to insert data into Database";
        }
    }

?>