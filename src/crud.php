<?php require_once './database/settigs.php'; ?>

<?php require './includes/header.inc.php'; ?>


<div class="header_spacer">
    <div class="page_title">
        <h1 style="font-size: 3rem; font-weight: 200;">Job Post</h1>
    </div>
</div>

<div class="form_wrapper" ">
    <form action=" save_job.php" method="POST">
    <div class="form_container" style="width: 52rem;">
        <div class="box_form">
            <hr>
            <label for="job_reference" class="box_label">Job Reference <span class="req">*</span></label>
            <input type="text" class="box_input" name="job_reference" required>
        </div>
        <div class="box_form">
            <label for="title" class="box_label">Job Title <span class="req">*</span></label>
            <input type="text" class="box_input" name="title" required>
        </div>
        <div class="box_form">
            <label for="site" class="box_label">Job Location <span class="req">*</span></label>
            <select id="state" class="box_input" name="site"  required>
                <option value="melbourne">Melbourne</option>
                <option value="sydney">Sydney</option>
                <option value="brisbane">Brisbane</option>
                <option value="perth">Perth</option>
                <option value="adelaide">Adelaide</option>
                <option value="camberra">Camberra</option>
                <option value="hobart">Hobart</option>
            </select>
        </div>
        <div class="box_form">
            <label for="salary">Salary</label>
            <input type="text" class="box_input" name="salary">
        </div>
        <div class="box_form">
            <label for="manager">Manager</label>
            <input type="text" class="box_input" name="manager">
        </div>

        <div class="box_form">
            <label for="description">Description</label>
            <textarea type="text" class="box_input"name="description" style="height: 15rem;"></textarea>
        </div>

        <div class="box_form">
            <label for="requirements">Requirements</label>
            <textarea type="text" class="box_input" name="requirements" style="height: 15rem;"></textarea>
        </div>
        <div class="box_form">
            <label for="responsibilities">Responsibilities</label>
            <textarea type="text" class="box_input" name="responsibilities" style="height: 15rem;"></textarea>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center;">
            <button type="submit" class="reset_btn" style="background-color: red; color: white;">Delete Post</button>

            <div style="display: flex; gap: 2em;">
                <button type="cancel" class="submit_btn">Cancel</button>
                <button type="submit" class="submit_btn">Post Job</button>
            </div>
        </div>

    </div>
    </form>
</div>

</body>