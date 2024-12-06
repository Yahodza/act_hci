<?php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if fields are empty
    if (empty($username) || empty($password)) {
        echo "<script>
                alert('Both fields are required. Please fill in your username and password.');
                window.history.back();
              </script>";
        exit();
    }

    try {
        // Fetch user using PDO
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute([':username' => $username]);

        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, $user['password'])) {
                $_SESSION['username'] = $user['username'];

                // Show success message and redirect
                echo "<script>
                        alert('Login successful! Welcome, " . htmlspecialchars($user['username']) . "');
                        window.location.href = 'dashboard.php';
                      </script>";
                exit();
            } else {
                // Show error message for invalid password and redirect to index.html
                echo "<script>
                        alert('Invalid password! Please try again.');
                        window.location.href = 'index.html'; // Redirect to index.html
                      </script>";
                exit();
            }
        } else {
            // Show error message for user not found and redirect to index.html
            echo "<script>
                    alert('No user found with that username. Please try again.');
                    window.location.href = 'index.html'; // Redirect to index.html
                  </script>";
            exit();
        }
    } catch (PDOException $e) {
        echo "<script>
                alert('Database error: " . htmlspecialchars($e->getMessage()) . "');
                window.location.href = 'index.html'; // Redirect to index.html
              </script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <form method="POST" action="login.php">
        <div>
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit">Sign In</button>
    </form>
</body>
</html>


