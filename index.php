<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>United NGOs Donation Platform</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header>
    <nav class="glass-navbar">
      <div class="logo">DonateUnited</div>
      <ul>
        <li><a href="#">Home</a></li>
        <li><a href="#ngos">NGOs</a></li>
        <li><a href="#login">Login</a></li>
        <li><a href="#signup">Signup</a></li>
      </ul>
    </nav>
    <section class="hero">
      <h1>One Platform, Thousands Helped</h1>
      <p>Choose a cause. Make a difference.</p>
    </section>
  </header>

  <main>
    <!-- Login and Signup Forms -->
    <section id="login">
      <form action="login.php" method="post">
        <h2>User Login</h2>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
      </form>
    </section>

    <section id="signup">
      <form action="signup.php" method="post">
        <h2>User Signup</h2>
        <input type="text" name="name" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Signup</button>
      </form>
    </section>

    <!-- NGO Registration -->
    <section id="ngos">
      <form action="ngo_register.php" method="post">
        <h2>NGO Registration</h2>
        <input type="text" name="ngo_name" placeholder="NGO Name" required>
        <input type="email" name="ngo_email" placeholder="Email" required>
        <input type="text" name="cause" placeholder="Cause (e.g. Education, Health)" required>
        <input type="text" name="account_details" placeholder="Bank Account Details" required>
        <button type="submit">Register NGO</button>
      </form>
    </section>
  </main>

  <script src="script.js"></script>
</body>
</html>
