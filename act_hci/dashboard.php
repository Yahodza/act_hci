<?php
include 'db.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$_SESSION['message'] = $_SESSION['message'] ?? '';

try {
    // Pagination Variables
    $limit = 5; // Number of records per page
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $start = ($page - 1) * $limit;

     // Search handling
     $searchTerm = isset($_GET['searchTerm']) ? $_GET['searchTerm'] : '';

     if ($searchTerm) {
         // SQL query when search term is provided
         $stmt = $conn->prepare("SELECT id, firstname, middlename, lastname, username, email, sex, phonenumber, suffix, status, created_at 
                                 FROM users 
                                 WHERE firstname LIKE :firstname OR lastname LIKE :lastname 
                                 LIMIT :start, :limit");
         $stmt->bindValue(':firstname', '%' . $searchTerm . '%', PDO::PARAM_STR);
         $stmt->bindValue(':lastname', '%' . $searchTerm . '%', PDO::PARAM_STR);
     } else {
         // SQL query when no search term is provided
         $stmt = $conn->prepare("SELECT id, firstname, middlename, lastname, username, email, sex, phonenumber, suffix, status, created_at 
                                 FROM users 
                                 LIMIT :start, :limit");
     }
 
     $stmt->bindValue(':start', $start, PDO::PARAM_INT);
     $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
     $stmt->execute();
     $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Change Password
if (isset($_POST['changePassword'])) {
    $id = $_POST['id'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($newPassword === $confirmPassword) {
        // Hash the new password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Update password in the database
        $updatePasswordStmt = $conn->prepare("UPDATE users SET password = :password WHERE id = :id");
        $updatePasswordStmt->execute([':password' => $hashedPassword, ':id' => $id]);

        $_SESSION['message'] = "Password changed successfully!";
        header("Location: dashboard.php");
        exit();
    } else {
        $_SESSION['message'] = "Passwords do not match!";
        header("Location: dashboard.php");
        exit();
    }
}



    // Total number of users (for pagination)
    $countStmt = $conn->prepare("SELECT COUNT(id) AS total_users FROM users");
    $countStmt->execute();
    $totalUsers = $countStmt->fetch(PDO::FETCH_ASSOC)['total_users'];
    $totalPages = ceil($totalUsers / $limit);

    // Delete user
    if (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $deleteStmt = $conn->prepare("DELETE FROM users WHERE id = :id");
        $deleteStmt->execute([':id' => $id]);
        $_SESSION['message'] = "User deleted successfully!";
        header("Location: dashboard.php");
        exit();
    }

    // Save edited user
    if (isset($_POST['save'])) {
        $id = $_POST['id'];
        $firstname = $_POST['firstname'];
        $middlename = $_POST['middlename'];
        $lastname = $_POST['lastname'];
        $username = $_POST['username'];
        $email = $_POST['email'];
        $sex = $_POST['sex'];
        $phonenumber = $_POST['phonenumber'];
        $suffix = $_POST['suffix'];
        $status = $_POST['status'];
    
        // Prepare the update statement without password fields
        $updateStmt = $conn->prepare("UPDATE users 
                                      SET firstname = :firstname, 
                                          middlename = :middlename, 
                                          lastname = :lastname, 
                                          username = :username, 
                                          email = :email, 
                                          sex = :sex, 
                                          phonenumber = :phonenumber, 
                                          suffix = :suffix, 
                                          status = :status 
                                      WHERE id = :id");
    
        // Bind the parameters
        $updateData = [
            ':firstname' => $firstname,
            ':middlename' => $middlename,
            ':lastname' => $lastname,
            ':username' => $username,
            ':email' => $email,
            ':sex' => $sex,
            ':phonenumber' => $phonenumber,
            ':suffix' => $suffix,
            ':status' => $status,
            ':id' => $id
        ];
    
        // Execute the update
        if ($updateStmt->execute($updateData)) {
            $_SESSION['message'] = "User edited successfully!";
            header("Location: dashboard.php");
            exit();
        } else {
            echo "<script>alert('Error updating user.');</script>";
        }
    }
    
    // Add new user
    if (isset($_POST['addUser'])) {
        $firstname = $_POST['firstname'];
        $middlename = $_POST['middlename'];
        $lastname = $_POST['lastname'];
        $username = $_POST['username'];
        $email = $_POST['email'];
        $sex = $_POST['sex'];
        $phonenumber = $_POST['phonenumber'];
        $suffix = $_POST['suffix'];
        $status = $_POST['status'];
        $password = $_POST['password'];

        // Hash the password before storing it
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $insertStmt = $conn->prepare("INSERT INTO users (firstname, middlename, lastname, username, email, sex, phonenumber, suffix, status, password) 
                                     VALUES (:firstname, :middlename, :lastname, :username, :email, :sex, :phonenumber, :suffix, :status, :password)");
        $insertStmt->execute([ 
            ':firstname' => $firstname,
            ':middlename' => $middlename,
            ':lastname' => $lastname,
            ':username' => $username,
            ':email' => $email,
            ':sex' => $sex,
            ':phonenumber' => $phonenumber,
            ':suffix' => $suffix,
            ':status' => $status,
            ':password' => $hashedPassword
        ]);

        $_SESSION['message'] = "New user added successfully!";
        header("Location: dashboard.php");
        exit();
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="images/ndkc.ico" type="image/ico">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styletwo.css">
    <link rel="stylesheet" href="form.css">

    <title>User Management</title>
   

</head>
<body>
<div id="sidebar">
    <ul>
        <li><a href="#">Dashboard</a></li>
        <li><h1>Welcome, <?php echo htmlspecialchars(ucfirst($_SESSION['username'])) . '!'; ?></h1></li>
        <li><a href="#">Profile</a></li>
        
        

        <!-- Add New User button -->
        <button type="button" class="btn btn-success" onclick="openAddUserModal()">Add New User</button>

        <!-- Logout link -->
        <li><a href="logout.php" class="btn btn-danger">Logout</a></li>
    </ul>
</div>


  <div class="container">
       
        <h2>Manage Users</h2>

        <?php if ($_SESSION['message']): ?>
        <div class="alert alert-success" role="alert">
            <?php echo $_SESSION['message']; ?>
        </div>
        <?php $_SESSION['message'] = ''; ?>
        <?php endif; ?>

        

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Middle Name</th>
                    <th>Last Name</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Sex</th>
                    <th>Phone Number</th>
                    <th>Suffix</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Action</th>
                </tr>
            </thead>
           


            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                    <td><?php echo htmlspecialchars($user['firstname']); ?></td>
                    <td><?php echo htmlspecialchars($user['middlename']); ?></td>
                    <td><?php echo htmlspecialchars($user['lastname']); ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($user['sex']); ?></td>
                    <td><?php echo htmlspecialchars($user['phonenumber']); ?></td>
                    <td><?php echo htmlspecialchars($user['suffix']); ?></td>
                    <td><?php echo htmlspecialchars($user['status']); ?></td>
                    <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                    <td>
                        <form action="dashboard.php" method="POST" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($user['id']); ?>">
                            <button type="button" class="btn btn-edit" onclick="openEditUserModal(<?php echo htmlspecialchars($user['id']); ?>)">Edit</button>
                            <button type="submit" name="delete" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this user?');">Delete</button>
                            <button type="button" class="btn btn-change-password" onclick="openChangePasswordModal(<?php echo htmlspecialchars($user['id']); ?>)">Change Password</button>
                        </form>
                    </td>

                </tr>
                <?php endforeach; ?>

                <!-- Search Form -->
        <form method="GET" action="dashboard.php">
            <div class="form-group">
                <input type="text" name="searchTerm" class="form-control" placeholder="Search by Name" value="<?php echo htmlspecialchars($searchTerm); ?>">
            </div>
            <button type="submit" name="search" class="btn btn-primary">Search</button>
        </form>
                 <!--pagination-->
            <link rel="stylesheet" href="pagination.css">

            <nav aria-label="User pagination">
            <ul class="pagination justify-content-center">
                <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a>
                </li>
                <?php else: ?>
                <li class="page-item disabled">
                    <a class="page-link">Previous</a>
                </li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a>
                </li>
                <?php else: ?>
                <li class="page-item disabled">
                    <a class="page-link">Next</a>
                </li>
                <?php endif; ?>
            </ul>
            </nav>
            </tbody>
        </table>
    </div>

    <!-- Change pass modal -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="dashboard.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="changePasswordUserId">
                    <div class="form-group">
                        <label for="newPassword">New Password</label>
                        <input type="password" name="new_password" id="newPassword" class="form-control" required>
                        <input type="checkbox" onclick="togglePasswordVisibility('newPassword')"> Show Password
                    </div>
                    <div class="form-group">
                        <label for="confirmPassword">Confirm Password</label>
                        <input type="password" name="confirm_password" id="confirmPassword" class="form-control" required>
                        <input type="checkbox" onclick="togglePasswordVisibility('confirmPassword')"> Show Password
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" name="changePassword" class="btn btn-warning">Change Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

   <!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="dashboard.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="firstname">First Name</label>
                        <input type="text" name="firstname" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="middlename">Middle Name</label>
                        <input type="text" name="middlename" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="lastname">Last Name</label>
                        <input type="text" name="lastname" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="sex">Sex</label>
                        <select name="sex" class="form-control" required>
                            <option value="M">Male</option>
                            <option value="F">Female</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="phonenumber">Phone Number</label>
                        <input type="text" name="phonenumber" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="suffix">Suffix</label>
                        <input type="text" name="suffix" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select name="status" class="form-control" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="addPassword" class="form-control" required>
                        <input type="checkbox" onclick="togglePasswordVisibility('addPassword')"> Show Password
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" name="addUser" class="btn btn-success">Add User</button>
                </div>
            </form>
        </div>
    </div>
</div>

    <!-- Edit User Modal -->

        <div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="dashboard.php" method="POST">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="id" id="editUserId">
                            <div class="form-group">
                                <label for="editFirstName">First Name</label>
                                <input type="text" name="firstname" id="editFirstName" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="editMiddleName">Middle Name</label>
                                <input type="text" name="middlename" id="editMiddleName" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="editLastName">Last Name</label>
                                <input type="text" name="lastname" id="editLastName" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="editUsername">Username</label>
                                <input type="text" name="username" id="editUsername" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="editEmail">Email</label>
                                <input type="email" name="email" id="editEmail" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="editSex">Sex</label>
                                <select name="sex" id="editSex" class="form-control" required>
                                    <option value="M">Male</option>
                                    <option value="F">Female</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="editPhoneNumber">Phone Number</label>
                                <input type="text" name="phonenumber" id="editPhoneNumber" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="editSuffix">Suffix</label>
                                <input type="text" name="suffix" id="editSuffix" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="editStatus">Status</label>
                                <select name="status" id="editStatus" class="form-control" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" name="save" class="btn btn-success">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

            



    <!-- Scripts -->
    <script src="pagination.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
             function openChangePasswordModal(id) {
             document.getElementById('changePasswordUserId').value = id;
             $('#changePasswordModal').modal('show');

            }
    </script>


    <script>
        function openAddUserModal() {
            $('#addUserModal').modal('show');
        }

        function openEditUserModal(id) {
            // You will need to fetch user data by ID and populate the fields
            const userData = <?php echo json_encode($users); ?>;
            const user = userData.find(user => user.id == id);

            if (user) {
                document.getElementById('editUserId').value = user.id;
                document.getElementById('editFirstName').value = user.firstname;
                document.getElementById('editMiddleName').value = user.middlename;
                document.getElementById('editLastName').value = user.lastname;
                document.getElementById('editUsername').value = user.username;
                document.getElementById('editEmail').value = user.email;
                document.getElementById('editSex').value = user.sex;
                document.getElementById('editPhoneNumber').value = user.phonenumber;
                document.getElementById('editSuffix').value = user.suffix;
                document.getElementById('editStatus').value = user.status;

                $('#editUserModal').modal('show');
            }
        }
    </script>
   
   <script>
            function togglePasswordVisibility(passwordFieldId) {
            var passwordField = document.getElementById(passwordFieldId);
            if (passwordField.type === "password") {
             passwordField.type = "text";
                } else {
                passwordField.type = "password";
                }
                }
   </script>

</body>
</html>
