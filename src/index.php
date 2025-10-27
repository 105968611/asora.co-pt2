<?php
$page_title = "Home - Asora";
require './includes/header.inc.php';
?>

<body>

	<?php require('./includes/navbar.inc.php'); ?>

	<!--Page 1 First Section, Company Slogan Included-->
	<section class="page-1">
		<img class="bg1-page1" src="images/background/bg1.jpg" alt="bg1">
		<h1 style="font-size: 5em; font-weight: 500; color: white; width: 800px; position: absolute; bottom: 185px; left: 45px;"">
			Technology that Serves <!--Company Slogan-->
		</h1>
	</section>


	<!--Page 2, Company Mission and Insight of Company-->
	<section class=" page-2">
			<h2>Our Mission</h2>
			<p class="description-page2">
				At Asora, we are a team of problem-solvers dedicated to improving
				public services and tackling complex challenges. Every project we work
				on, from enhancing national security to boosting economic transparency,
				has a direct and meaningful impact on people's lives.
			</p>
	</section>


	<!--Page 3, First Visual Banner to Careers page with Quote.-->
	<section class="page-3">
		<h2 class="topic-page3">
			Be a part of something bigger.
		</h2>
		<a class="careers-button-p3" href="jobs.html">
			Explore Careers
		</a>
	</section>


	<!--Page 4, Why work with Asora? Insight of Company Culture-->
	<section class="page-4">
		<div class="media-page4">
			<img class="display-img-p4" src="images/background/bg11.png" alt="image_work_with_us">
		</div>

		<div class="page4-info">
			<h2 class="title-page4">
				Why Work with Asora?
			</h2>
			<div class="descrip-p4">
				<p>
					We're not just a company. We're a collective of passionate data scientists,
					analysts, and innovators who love what we do. We thrive on curiosity and
					collaboration, tackling problems with a cup of coffee and a great idea.
				</p>
				<p>
					Here at Asora, your work is more than just a job, it's a chance to make a
					difference alongside some of the brightest minds in the field.
				</p>

			</div>
		</div>
	</section>

	<!--Page 5, Life at Asora, Company Perks & Benefits-->
	<section class="page-5">
		<h2> Life at Asora </h2>
		<div class="overall-row">
			<div class="box-p5 box-1">
				<div class="box-text">
					<h3>Growth & Purpose</h3>
					<p>
						Engage in continuous learning
						and meaningful work that contributes
						to national security and public welfare.
					</p>
				</div>
			</div>

			<div class="box-p5 box-2">
				<div class="box-text">
					<h3>Work-Life Balance</h3>
					<p>
						Enjoy a collaborative and flexible
						culture with a year-end paid company
						holiday to recharge.
					</p>
				</div>
			</div>

			<div class="box-p5 box-3">
				<div class="box-text">
					<h3>Exclusive Perks</h3>
					<p>
						Get access to discounts and perks
						through our partners, plus breakfast,
						lunch, dinner on us. Not to mention
						snack-bars in all offices.
					</p>
				</div>
			</div>

		</div>
	</section>


	<!--Page 6, Final Visual Banner to forward to jobs.html-->
	<section class="page-6">
		<h2 class="topic-page6">
			Ready to take the next step?
		</h2>
		<a class="career-button-p6" href="apply.html">
			Quick Apply
		</a>
	</section>


	<!--Footer Creds/Made by Hannah-->
	<?php require './includes/footer.inc.php'; ?>


</body>

</html>