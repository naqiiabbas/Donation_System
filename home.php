<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donation Management System</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
    <style>
        /* Basic styles for layout */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #333;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        header a {
            color: white;
            text-decoration: none;
            margin: 0 10px;
        }
        .hero-banner {
            background: url('hero-banner.jpg') no-repeat center center/cover;
            height: 400px;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            text-align: center;
        }
        .hero-banner h1 {
            font-size: 3rem;
        }
        .slider {
            margin: 20px auto;
            width: 80%;
            overflow: hidden;
            position: relative;
        }
        .slider-container {
            display: flex;
            transition: transform 0.5s ease-in-out;
        }
        .slider-item {
            min-width: 100%;
            box-sizing: border-box;
            text-align: center;
            padding: 20px;
            background: #f4f4f4;
            border: 1px solid #ddd;
        }
        .slider-controls {
            position: absolute;
            top: 50%;
            width: 100%;
            display: flex;
            justify-content: space-between;
            transform: translateY(-50%);
        }
        .slider-controls button {
            background: rgba(0, 0, 0, 0.5);
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <header>
        <h1>Donation Management System</h1>
        <div>
            <a href="login.php">Login</a>
            <a href="signup.php">Signup</a>
        </div>
    </header>

    <div class="hero-banner">
        <h1>Make a Difference Today</h1>
        <p>Support NGOs and help those in need</p>
    </div>

    <div class="slider">
        <div class="slider-container" id="slider-container">
            <div class="slider-item">NGO 1: Helping Hands</div>
            <div class="slider-item">NGO 2: Save the Children</div>
            <div class="slider-item">NGO 3: Green Earth</div>
            <div class="slider-item">NGO 4: Food for All</div>
        </div>
        <div class="slider-controls">
            <button onclick="prevSlide()">&#10094;</button>
            <button onclick="nextSlide()">&#10095;</button>
        </div>
    </div>

    <script>
        let currentIndex = 0;

        function showSlide(index) {
            const sliderContainer = document.getElementById('slider-container');
            const totalSlides = sliderContainer.children.length;
            if (index >= totalSlides) currentIndex = 0;
            else if (index < 0) currentIndex = totalSlides - 1;
            else currentIndex = index;

            sliderContainer.style.transform = `translateX(-${currentIndex * 100}%)`;
        }

        function nextSlide() {
            showSlide(currentIndex + 1);
        }

        function prevSlide() {
            showSlide(currentIndex - 1);
        }
    </script>
</body>
</html>