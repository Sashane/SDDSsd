<?php
// Database connection
$servername = "localhost";
$username = "root"; // Default XAMPP username
$password = ""; // Default XAMPP password (empty)
$dbname = "cafe_thaaya";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
$success_message = "";
$error_message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $branch = $_POST['branch'];
    $num_people = $_POST['people']; // Match form field name
    
    // Handle '5+' value for num_people
    if ($num_people === '5+') {
        $num_people = 5; // Convert '5+' to 5 for database storage
    }
    
    // Generate 8-character reservation code: 2 branch code + 4 random digits + 2 date digits
    $valid_branches = ['CM', 'GN', 'KN'];
    $branch_code = in_array($branch, $valid_branches) ? $branch : 'CM'; // Default to 'CM' if invalid
    $random_digits = sprintf("%04d", mt_rand(0, 9999)); // 4 random digits
    $date_parts = date_parse($date);
    $day = sprintf("%02d", $date_parts['day']); // 2-digit day of month
    $reservation_code = $branch_code . $random_digits . $day;
    
    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO reservations (name, date, time, branch, num_people, reservation_code, timestamp) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssssis", $name, $date, $time, $branch, $num_people, $reservation_code);
    
    if ($stmt->execute()) {
        $success_message = "Reservation successful! Your Reservation Code is: " . $reservation_code;
    } else {
        $error_message = "Error: " . $stmt->error;
    }
    
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Café Thaaya - Reservation</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Corinthia:wght@400;700&display=stylesheet">
</head>
<body class="body"> 
    <header>
        <h1 class="title">Café Thaaya</h1>
        <nav class="navbar navbar-dark">
            <ul>
                <li><a href="index.html" class="nav-link nav-item">Home</a></li>
                <li><a href="reservation.php" class="nav-link nav-item active">Reservation</a></li>
                <li><a href="menu.html" class="nav-link nav-item">Menu</a></li>
                <li><a href="contact.html" class="nav-link nav-item">Contact Us</a></li>
                <li><a href="cart.html" class="nav-link nav-item"><i class="fas fa-shopping-cart"></i><span>0</span></a></li>
            </ul>
        </nav>

        <div class="container-fluid">
            <div id="bg-hd" class="bg image">
                <div class="bg-hd">
                    <img class="bg-hd" src="img/bg-coffee-hd.jpg" alt="Image" id="img1">
                </div>    
            </div>
        </div>
    </header>

    <section class="reservation">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <h2>20% OFF</h2>
                    <h3>For Online Reservation</h3>
                    <p>Enjoy a warm welcome at Café Thaaya!<br>
                        Savor our freshly brewed coffee, desserts, and cozy ambiance.<br>
                        Reserve your spot today and indulge in a delightful experience.<br>
                        Open daily from 8 AM to 8 PM.</p>
                    <ul>
                        <li>✔ Freshly brewed coffee</li>
                        <li>✔ Cozy seating</li>
                        <li>✔ Free Wifi</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h2>Book Your Table</h2>
                    <form id="reservationForm" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <div class="form-group">
                            <label for="name">Name:</label>
                            <input type="text" id="name" name="name" placeholder="Enter your name" required>
                        </div>
                        <div class="form-group">
                            <label for="date">Date:</label>
                            <input type="date" id="date" name="date" placeholder="DD/MM/YYYY" required>
                        </div>
                        <div class="form-group">
                            <label for="time">Time:</label>
                            <input type="time" id="time" name="time" required>
                        </div>
                        <div class="form-group">
                            <label for="branch">Branch:</label>
                            <select id="branch" name="branch" required>
                                <option value="" disabled selected>Select a branch</option>
                                <option value="CM">Colombo</option>
                                <option value="GN">Galle</option>
                                <option value="KN">Kandy</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="people">No. of People:</label>
                            <select id="people" name="people" required>
                                <option value="" disabled selected>Select number of people</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5+">5+</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Book Now</button>
                        <?php if ($success_message): ?>
                            <div id="reservationCode">
                                <p id="codeDisplay"><?php echo htmlspecialchars($success_message); ?></p>
                            </div>
                        <?php elseif ($error_message): ?>
                            <p><?php echo htmlspecialchars($error_message); ?></p>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Footer -->
    <div class="container-fluid-footer sp2">
        <div class="container">
        </div>
    </div>        
        
    <footer class="footer">
        <div class="container footer-content">
            <div class="footer-section about">
                <h3>Café Thaaya</h3>
                <p>
                    Serving premium coffee and desserts across Sri Lanka.  
                    A place for community, creativity, and comfort.
                </p>
            </div>

            <div class="footer-section links">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="index.html">Home</a></li>
                    <li><a href="menu.html">Menu</a></li>
                    <li><a href="reservation.html">Reservation</a></li>
                    <li><a href="contact.html">Contact</a></li>
                    <li><a href="cart.html">Cart</a></li>
                </ul>
            </div>

            <div class="footer-section contact">
                <h4>Contact Us</h4>
                <p><i class="fas fa-map-marker-alt"></i> 123 Main St, Colombo</p>
                <p><i class="fas fa-phone"></i> +94 77 123 4567</p>
                <p><i class="fas fa-envelope"></i> info@cafethaaya.com</p>
            </div>

            <div class="footer-section social">
                <h4>Follow Us</h4>
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
            </div>
        </div>
        <hr>
        <p class="footer-bottom">&copy; 2025 Café Thaaya | All Rights Reserved
        <br>S.H.S.D.D.S | DIT/24/03/074
        </p>
    </footer>

    <!-- Back to Top button -->
    <button id="backToTopBtn" title="Go to top">↑</button>

    <!-- JavaScript for Back to Top functionality -->
    <script src="js/BK_top.js"></script>
    <!-- JavaScript for form validation -->
    <script>
    document.getElementById('reservationForm').addEventListener('submit', function(event) {
        // Validate name
        const nameInput = document.getElementById('name').value.trim();
        if (!nameInput) {
            alert('Please enter a valid name.');
            event.preventDefault();
            return;
        }

        // Validate date (not in the past)
        const dateInput = document.getElementById('date').value;
        const today = new Date().toISOString().split('T')[0];
        if (dateInput < today) {
            alert('Please select a date today or in the future.');
            event.preventDefault();
            return;
        }

        // Validate time (within open hours: 8:00 AM - 8:00 PM)
        const timeInput = document.getElementById('time').value;
        const timeInMinutes = parseInt(timeInput.split(':')[0]) * 60 + parseInt(timeInput.split(':')[1]);
        const minTime = 8 * 60; // 8:00 AM
        const maxTime = 20 * 60; // 8:00 PM
        if (timeInMinutes < minTime || timeInMinutes > maxTime) {
            alert('Please select a time between 8:00 AM and 8:00 PM.');
            event.preventDefault();
            return;
        }

        // Validate branch
        const branchInput = document.getElementById('branch').value;
        if (!branchInput) {
            alert('Please select a branch.');
            event.preventDefault();
            return;
        }

        // Validate number of people
        const peopleInput = document.getElementById('people').value;
        if (!peopleInput) {
            alert('Please select the number of people.');
            event.preventDefault();
            return;
        }
    });
    </script>
</body>
</html>