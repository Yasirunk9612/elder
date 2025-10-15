<?php $pageTitle = 'ElderCare Platform'; include 'includes/header.php'; ?>
<div>
	<section class="hero fade-in">
		<div class="hero-inner">
			<h1>Compassionate Elder Care Management</h1>
			<p class="lead">A lightweight platform connecting administrators, doctors, and elderly users to keep health records organized, accessible, and secure.</p>
			<div class="cta-group">
				<a href="login.php" class="btn btn-success btn-pill">Sign In</a>
				<a href="register.php" class="btn btn-primary btn-pill">Create Account</a>
			</div>
			<div style="margin-top:2.5rem; display:grid; gap:1rem; grid-template-columns:repeat(auto-fit,minmax(180px,1fr));">
				<div class="card" style="padding:1rem 1rem 1.1rem;">
					<h3 style="margin:.2rem 0 .6rem; font-size:1rem; text-align:left;">For Admins</h3>
					<p style="font-size:.75rem; color:var(--color-text-light); margin:0;">Manage users and oversee platform activity effortlessly.</p>
				</div>
				<div class="card" style="padding:1rem 1rem 1.1rem;">
					<h3 style="margin:.2rem 0 .6rem; font-size:1rem; text-align:left;">For Doctors</h3>
					<p style="font-size:.75rem; color:var(--color-text-light); margin:0;">Record and monitor patient health metrics in real time.</p>
				</div>
				<div class="card" style="padding:1rem 1rem 1.1rem;">
					<h3 style="margin:.2rem 0 .6rem; font-size:1rem; text-align:left;">For Elders</h3>
					<p style="font-size:.75rem; color:var(--color-text-light); margin:0;">View personal health records securely anytime.</p>
				</div>
			</div>
		</div>
	</section>
<?php include 'includes/footer.php'; ?>
