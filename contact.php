<?php
// Database configuration
$host = 'localhost'; // or your database host
$db = 'contact_db'; // your database name
$user = 'root'; // your database username
$pass = ''; // your database password

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$name = $email = $phone = $message = "";
$successMessage = $errorMessage = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $message = $_POST['message'];

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO contacts (name, email, phone, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $phone, $message);

    // Execute the statement
    if ($stmt->execute()) {
        $successMessage = "Form submission successful!";
    } else {
        $errorMessage = "Error sending message: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Contact Form</title>
    <link rel="stylesheet" href="style.css" />
    <style>
        /* General Styles */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    line-height: 1.6;
    color: #333;
    background-color: #f5f5f5;
    margin: 0;
    padding: 0;
}

main {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

section {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
    padding: 2rem;
}

div {
    max-width: 600px;
    margin: 0 auto;
}

h1 {
    color: #2c3e50;
    font-size: 2.5rem;
    margin-bottom: 0.5rem;
}

p {
    color: #7f8c8d;
    margin-bottom: 2rem;
    font-size: 1.1rem;
}

/* Form Styles */
#contactForm {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

input, textarea {
    padding: 1rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 1rem;
    transition: border-color 0.3s ease;
}

input:focus, textarea:focus {
    outline: none;
    border-color: #3498db;
}

textarea {
    min-height: 150px;
    resize: vertical;
}

button {
    background-color: #3498db;
    color: white;
    padding: 1rem 2rem;
    border: none;
    border-radius: 4px;
    font-size: 1rem;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

button:hover {
    background-color: #2980b9;
}

/* Alert Messages */
.alert {
    padding: 1rem;
    margin-bottom: 1.5rem;
    border-radius: 4px;
    font-weight: 500;
}

.alert-success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-danger {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

/* Responsive Design */
@media (max-width: 768px) {
    section {
        padding: 1rem;
    }
    
    h1 {
        font-size: 2rem;
    }
}
    </style>
</head>
<body>
    <main>
        <section>
            <div>
                <h1>Get in touch</h1>
                <p>Let's work together!</p>
                <?php if ($successMessage): ?>
                    <div class="alert alert-success"><?php echo $successMessage; ?></div>
                <?php endif; ?>
                <?php if ($errorMessage): ?>
                    <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
                <?php endif; ?>
                <form id="contactForm" method="POST" action="">
                    <input type="text" name="name" placeholder="Enter your name..." required />
                    <input type="email" name="email" placeholder="name@example.com" required />
                    <input type="tel" name="phone" placeholder="017********        " required />
                    <textarea name="message" placeholder="Enter your message here..." required></textarea>
                    <button type="submit">Submit</button>
                </form>
            </div>
        </section>
    </main>
</body>
</html>