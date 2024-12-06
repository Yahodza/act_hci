<?php
include 'db.php';
session_start(); // Start the session

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve values from the form, including the new fields
    $firstname = $_POST['firstname'];
    $middlename = $_POST['middlename'];
    $lastname = $_POST['lastname'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // Optional fields
    $sex = $_POST['sex'];
    $phonenumber = $_POST['phonenumber'];
    $suffix = $_POST['suffix'];

    // Check for required fields
    if (empty($username) || empty($email) || empty($_POST['password'])) {
        echo "<script>
                alert('Username, Email, and Password are required fields.');
                window.location.href = 'index.html'; // Redirect to index.html
              </script>";
        exit();
    }

    try {
        // Check if username or email already exists
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username OR email = :email");
        $stmt->execute([':username' => $username, ':email' => $email]);

        if ($stmt->rowCount() > 0) {
            // Username or Email already exists
            echo "<script>
                    alert('Username or Email already exists! Please try again.');
                    window.location.href = 'index.html'; // Redirect to index.html
                  </script>";
            exit();
        } else {
            // Insert new user into the database with firstname, middlename, lastname, and username
            $stmt = $conn->prepare("INSERT INTO users (firstname, middlename, lastname, username, email, password, sex, phonenumber, suffix) 
                                    VALUES (:firstname, :middlename, :lastname, :username, :email, :password, :sex, :phonenumber, :suffix)");
            $stmt->execute([ 
                ':firstname' => $firstname, 
                ':middlename' => $middlename, 
                ':lastname' => $lastname, 
                ':username' => $username, 
                ':email' => $email, 
                ':password' => $password, 
                ':sex' => $sex, 
                ':phonenumber' => $phonenumber, 
                ':suffix' => $suffix
            ]);

            // Registration successful, set session variable to log in the user automatically
            $_SESSION['username'] = $username;

            echo "<script>
                    alert('Registration successful! You are now logged in.');
                    window.location.href = 'dashboard.php'; // Redirect to the dashboard
                  </script>";
            exit();
        }
    } catch (PDOException $e) {
        // Handle any errors during registration
        echo "<script>
                alert('Error: " . htmlspecialchars($e->getMessage()) . "');
                window.location.href = 'index.html'; // Redirect to index.html
              </script>";
        exit();
    }
}
?>

<!-- HTML Form with the Phone Number Input -->
<form action="index.php" method="POST">
    <label for="firstname">First Name:</label>
    <input type="text" name="firstname" required><br>

    <label for="middlename">Middle Name:</label>
    <input type="text" name="middlename"><br>

    <label for="lastname">Last Name:</label>
    <input type="text" name="lastname" required><br>

    <label for="username">Username:</label>
    <input type="text" name="username" required><br>

    <label for="email">Email:</label>
    <input type="email" name="email" required><br>

    <label for="password">Password:</label>
    <input type="password" name="password" required><br>

    <label for="sex">Sex:</label>
    <input type="text" name="sex"><br>

    <label for="phonenumber">Phone Number:</label>
    <input type="text" name="phonenumber" id="phonenumber" required onkeypress="return onlyNumbers(event)"><br>

    <label for="suffix">Suffix:</label>
    <input type="text" name="suffix"><br>

    <button type="submit">Register</button>
</form>

<script>
    // Function to allow only numbers in the phone number input
    function onlyNumbers(event) {
        // Allow numbers, backspace, and delete
        var charCode = (event.which) ? event.which : event.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            event.preventDefault(); // Prevent input if it's not a number
        }
    }
</script>
