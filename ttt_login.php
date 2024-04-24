<?php
session_start();
include "db.php";


// Define variables and initialize them with empty values.
$firstnameErr = $lastnameErr = $userIDErr = $passwrdErr = "";
$firstname = $lastname = $userID = $passwrd = "";
$errors = false;

function test_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate firstname.
    if (empty(trim($_POST["firstname"]))) {
        $firstnameErr = "Firstname is required.";
        $errors = true;
    } else {
        $firstname = test_input($_POST["firstname"]);
        if (!preg_match("/^[a-zA-Z ]*$/", $firstname)) {
            $firstnameErr = "Only letters and white space allowed.";
            $errors = true;
        }
    }

    // Validate lastname.
    if (empty(trim($_POST["lastname"]))) {
        $lastnameErr = "Lastname is required.";
        $errors = true;
    } else {
        $lastname = test_input($_POST["lastname"]);
        if (!preg_match("/^[a-zA-Z ]*$/", $lastname)) {
            $lastnameErr = "Only letters and white space allowed.";
            $errors = true;
        }
    }

    // Validate userID.
    if (empty(trim($_POST["userID"]))) {
        $userIDErr = "Email is required.";
        $errors = true;
    } else {
        $userID = test_input($_POST["userID"]);
        if (!filter_var($userID, FILTER_VALIDATE_EMAIL)) {
            $userIDErr = "Invalid email format.";
            $errors = true;
        }
    }

    // Validate password.
    if (empty(trim($_POST["passwrd"]))) {
        $passwrdErr = "Password is required.";
        $errors = true;
    } else {
        $passwrd = test_input($_POST["passwrd"]);
        if (!preg_match("/^(?=.*[a-zA-Z]).{6,}$/", $passwrd)) {
            $passwrdErr = "Password must be at least 6 characters long.";
            $errors = true;
        }
    }

    // Proceed if there are no input errors.
    if (!$errors) {
        $sql = "SELECT id, passwrd FROM ttt_db WHERE userID = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $userID);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows == 1) { // User exists.
                $stmt->bind_result($id, $stored_password);
                $stmt->fetch();
                if ($passwrd == $stored_password) { // Password matches.
                    $_SESSION['user_id'] = $id;
                    header('Location: ttt_main_board.php');
                    exit;
                } else {
                    $passwrdErr = "The password you entered was not valid.";
                }
            } else { // User does not exist, create a new one.
                $sql = "INSERT INTO ttt_db (firstname, lastname, userID, passwrd, last_login) VALUES (?, ?, ?, ?, NOW())";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssss", $firstname, $lastname, $userID, $passwrd);
                if ($stmt->execute()) {
                    $_SESSION['user_id'] = $conn->insert_id;
                    header('Location: ttt_main_board.php');
                    exit;
                } else {
                    echo "Something went wrong. Please try again later.";
                }
            }
            $stmt->close();
        } else {
            echo "Error preparing statement: " . htmlspecialchars($conn->error);
        }
    }
    $conn->close();
}
?>



<!DOCTYPE html>
<html>
<head>
  <title>Tic Tac Toe Login Page</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header>
    <section class="top">
      <div><h1> Tic Tac Toe Login Panel </h1></div>
    </section>
  </header>

  <div id="container">
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <article>
          <section>
            <div class="placeholders">
              <label for="FirstName">First name *</label>
              <input type="text" id="FirstName" name="firstname" placeholder="Enter firstname" value="<?php echo htmlspecialchars($firstname); ?>">
              <span class="error-message"><?php echo $firstnameErr;?></span>
            </div>
            <div class="placeholders">
              <label for="LastName">Last name *</label>
              <input type="text" id="LastName" name="lastname" placeholder="Enter lastname" value="<?php echo htmlspecialchars($lastname); ?>">
              <span class="error-message"><?php echo $lastnameErr;?></span>
            </div>
            <div class="placeholders">
              <label for="UserID">UserID *</label>
              <input type="text" id="UserID" name="userID" placeholder="Enter email" value="<?php echo htmlspecialchars($userID); ?>">
              <span class="error-message"><?php echo $userIDErr;?></span>
            </div>
            <div class="placeholders">
              <label for="Passwrd">Password *</label>
              <input type="password" id="Passwrd" name="passwrd" placeholder="Enter password" value="<?php echo htmlspecialchars($passwrd); ?>">
              <span class="error-message"><?php echo $passwrdErr;?></span>
            </div>
            <div class="placeholders">
              <button type="submit" class="login">Submit</button>
            </div>
          </section>
        </article>
      </form>
  </div>
</body>
</html>
