### Database Structure
Create a database named `student_system` and a table named `users` with the following structure:

```sql
CREATE DATABASE student_system;

USE student_system;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fname VARCHAR(30) NOT NULL,
    lname VARCHAR(30) NOT NULL,
    dob DATE NOT NULL,
    email VARCHAR(50) NOT NULL UNIQUE,
    mobile VARCHAR(10) NOT NULL,
    gender ENUM('Male', 'Female') NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Insert example user
INSERT INTO users (fname, lname, dob, email, mobile, gender, username, password) 
VALUES ('John', 'Doe', '2000-01-01', 'johndoe@example.com', '1234567890', 'Male', 'testuser', MD5('password123'));
```

### PHP Web Application Code

#### `register.php`
```php
<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root"; // Replace with your DB username
$password = ""; // Replace with your DB password
$dbname = "student_system";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $dob = $_POST['year'] . '-' . $_POST['month'] . '-' . $_POST['day'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $gender = $_POST['gender'];
    $username = $_POST['username'];
    $password = md5($_POST['password']);

    $sql = "INSERT INTO users (fname, lname, dob, email, mobile, gender, username, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssssssss', $fname, $lname, $dob, $email, $mobile, $gender, $username, $password);

    if ($stmt->execute()) {
        echo "User registered successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register User</title>
</head>
<body>
    <h2>Register User</h2>
    <form method="POST" action="">
        <label for="fname">First Name:</label>
        <input type="text" name="fname" maxlength="30" required><br><br>

        <label for="lname">Last Name:</label>
        <input type="text" name="lname" maxlength="30" required><br><br>

        <label>Date of Birth:</label>
        <select name="day" required>
            <option value="">Day</option>
            <?php for ($i = 1; $i <= 31; $i++) echo "<option value='$i'>$i</option>"; ?>
        </select>
        <select name="month" required>
            <option value="">Month</option>
            <?php for ($i = 1; $i <= 12; $i++) echo "<option value='$i'>$i</option>"; ?>
        </select>
        <select name="year" required>
            <option value="">Year</option>
            <?php for ($i = 1980; $i <= 2025; $i++) echo "<option value='$i'>$i</option>"; ?>
        </select><br><br>

        <label>Email:</label>
        <input type="email" name="email" required><br><br>

        <label>Mobile Number:</label>
        <input type="text" name="mobile" maxlength="10" required><br><br>

        <label>Gender:</label>
        <label>Male</label><input type="radio" name="gender" value="Male" required>
        <label>Female</label><input type="radio" name="gender" value="Female" required><br><br>

        <label for="username">Username:</label>
        <input type="text" name="username" maxlength="50" required><br><br>

        <label for="password">Password:</label>
        <input type="password" name="password" required><br><br>

        <button type="submit">Register</button>
    </form>
</body>
</html>
```

#### `login.php`
```php
<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "student_system";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = md5($_POST['password']);

    $sql = "SELECT * FROM users WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        setcookie("username", $username, time() + (86400 * 30), "/"); // Cookie expires in 30 days
        header("Location: display.php");
        exit;
    } else {
        $error = "Invalid username or password!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
    <h2>Login Page</h2>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="POST" action="">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>

        <button type="submit">Login</button>
    </form>
</body>
</html>
```

#### `display.php`
```php
<?php
if (!isset($_COOKIE['username'])) {
    header("Location: login.php");
    exit;
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "student_system";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_COOKIE['username'];
$sql = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "No user found.";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Information</title>
</head>
<body>
    <h2>Welcome, <?php echo htmlspecialchars($user['fname'] . ' ' . $user['lname']); ?>!</h2>
    <h3>Your Information</h3>
    <p><strong>First Name:</strong> <?php echo htmlspecialchars($user['fname']); ?></p>
    <p><strong>Last Name:</strong> <?php echo htmlspecialchars($user['lname']); ?></p>
    <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars($user['dob']); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
    <p><strong>Mobile:</strong> <?php echo htmlspecialchars($user['mobile']); ?></p>
    <p><strong>Gender:</strong> <?php echo htmlspecialchars($user['gender']); ?></p>
</body>
</html>
