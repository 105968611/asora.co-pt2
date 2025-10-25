<?php require './database/settings.php';


//lookup for table in DB
$sql = "SELECT * FROM jobs ORDER BY created_at DESC";
$result = $conn->query($sql);

//general header for all pages 
require './includes/header.inc.php'; ?>

<main>

  <!--JOBS Banner-->
  <div class="header_jobs">
    <img src="./images/background/bg2.png" class="banner-bg" alt="asora-jobs-banner">

    <h1 class="banner-title fade-in">Your future starts here</h1>

    <img src="./images/logos/asoratext_white.png" class="banner-logo" alt="Asora text logo">
  </div>

  <!--Job post cards loop: fetch all data inside jobs table in DB-->
  <?php while ($job = $result->fetch_assoc()): ?>
    <div class="role_card" id="<?= htmlspecialchars($job['job_reference']) ?>">

      <div class="role_content">
        <aside class="role_aside">
          <p>Reference #<?= htmlspecialchars($job['job_reference']) ?></p> <!--Call the value expected from the table selected-->
        </aside>
        <h2><?= htmlspecialchars($job['job_title']) ?>
        </h2>
        <p><img width="28" height="28" src="https://img.icons8.com/windows/32/place-marker.png" alt="place-marker" /><?= htmlspecialchars($job['job_city']) ?>(<?= htmlspecialchars($job['job_mode']) ?>)</p>
        <p><img width="28" height="28" src="https://img.icons8.com/windows/32/clock--v1.png" alt="clock--v1" /><?= htmlspecialchars($job['job_type']) ?></p>
        <p><img width="28" height="28" src="https://img.icons8.com/windows/32/money.png" alt="money" /><?= htmlspecialchars($job['job_salary']) ?></p>
        <p><img width="28" height="28" src="https://img.icons8.com/windows/32/manager.png" alt="manager" /><?= htmlspecialchars($job['job_manager']) ?></p>
        <p><img width="28" height="28" src="https://img.icons8.com/windows/32/collaborating-in-circle.png" alt="department-logo" /><?= htmlspecialchars($job['job_department']) ?></p>

        <div>
          <div class="wrapper">
            <!--Modal toggle using job reference to get details connected to each role-->
            <a href="#<?= htmlspecialchars($job['job_reference']) ?>" class="role_details" aria-label="Open modal for job details">Job details</a>
            <a class="apply_now_button" href="apply.php" aria-label="Apply now for the Data Analyst position">Apply Now</a>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal for each job post -->
    <div id="<?= htmlspecialchars($job['job_reference']) ?>" class="modal">
      <div class="modalcontent">
        <div class="modal-body">
          <h2><?= htmlspecialchars($job['job_title']) ?></h2>
          <h3>Reference number: <span><?= htmlspecialchars($job['job_reference']) ?></span>
          </h3>
          <p><?= htmlspecialchars($job['job_summary']) ?></p>

          <!--Open conditional for upcoming list query -->
          <?php if (!empty($job['job_responsibilities'])): ?>
            <div>
              <h3>Key Responsibilities</h3>
              <ol>
                <?php
                //Goes trhough each 'responsibility' based on line break format
                $responsibilities = preg_split("/\r\n|\n|\r/", $job['job_responsibilities']);
                foreach ($responsibilities as $resp) {
                  $resp = trim($resp);
                  if (!empty($resp)) {
                    echo "<li>" . htmlspecialchars($resp) . "</li>";
                  }
                }
                ?>
              </ol>
            </div>
          <?php endif; ?>

          <!--Conditional lookup for essential & preferable for upcoming list query-->
          <?php if (!empty($job['job_essential']) || !empty($job['job_preferable'])): ?>
            <div>
              <h3>Requirements</h3>
              <?php if (!empty($job['job_essential'])): ?>
                <h4>Essential</h4>
                <ul>
                  <?php
                  //Goes trhough each 'essential' based on line break format
                  $essentials = preg_split("/\r\n|\n|\r/", $job['job_essential']);
                  foreach ($essentials as $esse) {
                    $esse = trim($esse);
                    if (!empty($esse)) {
                      echo "<li>" . htmlspecialchars($esse) . "</li>";
                    }
                  }
                  ?>
                </ul>
              <?php endif; ?>


                  
              <?php if (!empty($job['job_preferable'])): ?>
                <h4>Preferable</h4>
                <ul>
                  <?php
                  //Goes trhough each 'essential' based on line break format
                  $preferables = preg_split("/\r\n|\n|\r/", $job['job_preferable']);
                  foreach ($preferables as $pref) {
                    $pref = trim($pref);
                    if (!empty($pref)) {
                      echo "<li>" . htmlspecialchars($pref) . "</li>";
                    }
                  }
                  ?>
                </ul>
              <?php endif; ?>
            </div>
          <?php endif; ?>
        </div>

        <div class="modal-footer">
          <a class="apply_now_button" href="apply.php">Apply Now</a>
        </div>

        <a href="#1234" class="modalclose">&times;</a>
      </div>
    </div>

    </div>
  <?php endwhile; ?>
  <?php include './includes/footer.inc.php'; ?>
</main>
</body>

</html>