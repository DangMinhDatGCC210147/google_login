<?php
include_once 'header.php';
require_once 'connect.php';
require 'google-api/vendor/autoload.php';

// Creating new google client instance
$client = new Google_Client();

// Enter your Client ID
$client->setClientId('783267183450-kpm3kuqe8bhl16nh31u6hbkbf53pcc1t.apps.googleusercontent.com');
// Enter your Client Secrect
$client->setClientSecret('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');
// Enter the Redirect URL
$client->setRedirectUri('https://wonderkidworld-cb34182facb3.herokuapp.com/login_register.php');

// Adding those scopes which we want to get (email & profile Information)
$client->addScope("email");
$client->addScope("profile");

function login($email, $password)
{
    $c = new Connect();
    $dblink = $c->connectToPDO();

    $sql = "SELECT * FROM users WHERE u_email = ? and u_password = ?";
    $stmt = $dblink->prepare($sql);
    $re = $stmt->execute(array("$email", "$password"));
    $numrow = $stmt->rowCount();
    $row = $stmt->fetch(PDO::FETCH_BOTH);
    if ($numrow == 1) {
        $_SESSION['user_id'] = $row['u_id'];
        $_SESSION['user_name'] = $row['u_firstName'];
        $_SESSION['user_role'] = $row['u_role'];
        $_SESSION['user_lastName'] = $row['u_lastName'];
        // Verify password
        if ($row['u_role'] == 0) {
            // Admin login
            $_SESSION['user_role'] = 'admin';
            echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "Success",
                        title: "Login successfully!",
                        text: "Hello admin",
                        showConfirmButton: false,
                        timer: 1500
                      }).then(() => {
                        window.location.href = "index.php";
                    });
                    </script>';
        } elseif ($row['u_role'] == 1) {
            // Manager login
            $_SESSION['user_role'] = 'manager';
            echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "Success",
                        title: "Login successfully!",
                        text: "Hello manager",
                        showConfirmButton: false,
                        timer: 1500
                      }).then(() => {
                        window.location.href = "index.php";
                    });
                    </script>';
        } elseif ($row['u_role'] == 2) {
            // User login
            $_SESSION['user_role'] = 'user';
            echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "Success",
                        title: "Login successfully!",
                        showConfirmButton: false,
                        timer: 1500
                      }).then(() => {
                        window.location.href = "index.php";
                    });
                    </script>';
        }
        exit();
    } else {
        echo '<script>
        Swal.fire({
            icon: "error",
            title: "Oops...",
            text: "Wrong password. Please enter again",
            showConfirmButton: false,
            timer: 1500
        });
    </script>';
    }
}

if (isset($_POST['btnLogin'])) {
    if (isset($_POST['txtEmail']) && isset($_POST['txtPass'])) {
        $email = $_POST['txtEmail'];
        $password = $_POST['txtPass'];

        login($email, $password);
    }
}
// End Login

// Register
if (isset($_POST['btnRegister'])) {
    $pwd2 = $_POST['txtPass2'];
    $re_pwd = $_POST['txtConfirmPass'];
    $fname = $_POST['txtFirstName'];
    $lname = $_POST['txtLastName'];
    $email2 = $_POST['txtEmail2'];
    $phone = $_POST['txtPhone'];
    $add = $_POST['txtAddress'];
    $dateBirthday = date('Y-m-d', strtotime($_POST['txtDate']));

    $username = isset($_POST['txtEmail2']) ? trim($_POST['txtEmail2']) : '';
    $password = isset($_POST['txtPass']) ? trim($_POST['txtPass']) : '';
    $passConfirm = isset($_POST['txtPassConfirm']) ? trim($_POST['txtPassConfirm']) : '';

    if ($pwd2 != $re_pwd) {
        echo '<script>
        Swal.fire({
            icon: "error",
            title: "Oops...",
            text: "The password and confirm password are not match!",
            showConfirmButton: false,
            timer: 1500
        });
        </script>';
    } elseif (strlen($pwd2) < 5) {
        echo '<script>
        Swal.fire({
            icon: "error",
            title: "Oops...",
            text: "Your password must be greater than 5",
            showConfirmButton: false,
            timer: 1500
        });
    </script>';
    } elseif (strlen($fname) >= 30) {
        echo '<script>
        Swal.fire({
            icon: "error",
            title: "Oops...",
            text: "Your First Name must be less than 30.",
            showConfirmButton: false,
            timer: 1500
        });
    </script>';
    } elseif ($pwd2 == $re_pwd) {
        $c = new Connect();
        $dblink2 = $c->connectToPDO();
        $sql = "INSERT INTO `users`(`u_email`, `u_firstName`, `u_lastName`, `u_address`, `u_password`, `u_role`, `u_phone`, `u_birthday`) VALUES (?,?,?,?,?,?,?,?)";
        $re2 = $dblink2->prepare($sql);
        $stmt2 = $re2->execute(array("$email2", "$fname", $lname, "$add", "$pwd2", 2, "$phone", "$dateBirthday"));
        
        if ($stmt2) {
            header("Location: login_register.php");
        }
    } else {
        echo '<script>
        Swal.fire({
            icon: "error",
            title: "Oops...",
            text: "Registration failed.",
            showConfirmButton: false,
            timer: 2000
        });
    </script>' . $stmt2;
    }
}

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    if (!isset($token["error"])) {
        $client->setAccessToken($token['access_token']);

        // getting profile information
        $google_oauth = new Google_Service_Oauth2($client);
        $google_account_info = $google_oauth->userinfo->get();

        $email = $google_account_info->email;
        $name = $google_account_info->name;
        $first_name = $google_account_info->givenName;
        $last_name = $google_account_info->familyName;
        $google_id = $google_account_info->id;
        $picture = $google_account_info->picture;

        $_SESSION['email'] = $email;
        $_SESSION['user_name'] = $name;
        $_SESSION['isLoggedIn'] = true;

        $c = new Connect();
        $dblink3 = $c->connectToPDO();
        $sql = "INSERT INTO `users`(`u_email`, `u_firstName`, `u_lastName`, `u_role`, `google_id`) VALUES (?,?,?,?,?)";
        $re3 = $dblink3->prepare($sql);
        $stmt3 = $re3->execute(array("$email", "$first_name", $last_name, 2, "$google_id"));
        
        if ($stmt3) {
            header("Location: index.php");
            // if (!isset($_SESSION['isLoggedIn']) || $_SESSION['isLoggedIn'] !== true) {
            //     // User is not logged in, redirect to login page or display an error message
            //     header("Location: index.php");
            // }
        }
    } else {
        header('Location: login_register.php');
        exit;
    }
}
?>
<link rel="stylesheet" href="./assets/css/login.css">
<!-- End Register -->
<!-- Page Section Start -->
<div class="page-section section section-padding">
    <div class="container">
        <div class="row mbn-40">

            <div class="col-lg-4 col-12 mb-40">
                <div class="login-register-form-wrap">
                    <h3>Login</h3>
                    <form method="POST" class="mb-30">
                        <div class="row">
                            <div class="col-12 mb-15"><input name="txtEmail" type="text" placeholder="Email"></div>
                            <div class="col-12 mb-15"><input name="txtPass" type="password" placeholder="Password"></div>
                            <div class="col-12"><input type="submit" name="btnLogin" value="Login"></div>
                        </div>
                    </form>
                    <div>
                        <a href="<?php echo $client->createAuthUrl(); ?>">
                        <button class="button">
                            <svg xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid" viewBox="0 0 256 262">
                            <path fill="#4285F4" d="M255.878 133.451c0-10.734-.871-18.567-2.756-26.69H130.55v48.448h71.947c-1.45 12.04-9.283 30.172-26.69 42.356l-.244 1.622 38.755 30.023 2.685.268c24.659-22.774 38.875-56.282 38.875-96.027"></path>
                            <path fill="#34A853" d="M130.55 261.1c35.248 0 64.839-11.605 86.453-31.622l-41.196-31.913c-11.024 7.688-25.82 13.055-45.257 13.055-34.523 0-63.824-22.773-74.269-54.25l-1.531.13-40.298 31.187-.527 1.465C35.393 231.798 79.49 261.1 130.55 261.1"></path>
                            <path fill="#FBBC05" d="M56.281 156.37c-2.756-8.123-4.351-16.827-4.351-25.82 0-8.994 1.595-17.697 4.206-25.82l-.073-1.73L15.26 71.312l-1.335.635C5.077 89.644 0 109.517 0 130.55s5.077 40.905 13.925 58.602l42.356-32.782"></path>
                            <path fill="#EB4335" d="M130.55 50.479c24.514 0 41.05 10.589 50.479 19.438l36.844-35.974C195.245 12.91 165.798 0 130.55 0 79.49 0 35.393 29.301 13.925 71.947l42.211 32.783c10.59-31.477 39.891-54.251 74.414-54.251"></path>
                            </svg>
                            Continue with Google
                        </button>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-2 col-12 mb-40 text-center d-none d-lg-block">
                <span class="login-register-separator"></span>
            </div>

            <div class="col-lg-6 col-12 mb-40 ms-auto">
                <div class="login-register-form-wrap">
                    <h3>Register</h3>
                    <form action="" method="POST">
                        <div class="row">
                            <div class="col-md-6 col-12 mb-15"><input name="txtFirstName" type="text" placeholder="Your First Name" required></div>
                            <div class="col-md-6 col-12 mb-15"><input name="txtLastName" type="text" placeholder="Your Last Name" required></div>
                            <div class="col-md-6 col-12 mb-15"><input name="txtEmail2" type="email" placeholder="Your Email" required></div>
                            <div class="col-md-6 col-12 mb-15"><input name="txtAddress" type="text" placeholder="Your Address" required></div>
                            <div class="col-md-6 col-12 mb-15"><input name="txtPhone" type="text" placeholder="Your Phone Number" required></div>
                            <div class="col-md-6 col-12 mb-15"><input name="txtDate" type="date" placeholder="Your Date of Birth" required></div>
                            <div class="col-md-6 col-12 mb-15"><input name="txtPass2" type="password" placeholder="Password" required></div>
                            <div class="col-md-6 col-12 mb-15"><input name="txtConfirmPass" type="password" placeholder="Confirm Password" required></div>
                            <div class="col-md-6 col-12"><input type="submit" name="btnRegister" value="Register"></div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div><!-- Page Section End -->

<!-- Brand Section Start -->
<div class="brand-section section section-padding pt-0">
    <div class="container-fluid">
        <div class="row">
            <div class="brand-slider">

                <div class="brand-item col">
                    <img src="./assets/images/brands/brand-1.png" alt="">
                </div>

                <div class="brand-item col">
                    <img src="./assets/images/brands/brand-2.png" alt="">
                </div>

                <div class="brand-item col">
                    <img src="./assets/images/brands/brand-3.png" alt="">
                </div>

                <div class="brand-item col">
                    <img src="./assets/images/brands/brand-4.png" alt="">
                </div>

                <div class="brand-item col">
                    <img src="./assets/images/brands/brand-5.png" alt="">
                </div>

                <div class="brand-item col">
                    <img src="./assets/images/brands/brand-6.png" alt="">
                </div>

            </div>
        </div>
    </div>
</div><!-- Brand Section End -->

<!-- JS
============================================ -->
<!-- <script src="../js/my.js"></script> -->
<!-- jQuery JS -->
<script src="./assets/js/vendor/jquery-3.6.0.min.js"></script>
<!-- Migrate JS -->
<script src="./assets/js/vendor/jquery-migrate-3.3.2.min.js"></script>
<!-- Bootstrap JS -->
<script src="./assets/js/bootstrap.bundle.min.js"></script>
<!-- Plugins JS -->
<script src="./assets/js/plugins.js"></script>
<!-- Main JS -->
<script src="./assets/js/main.js"></script>
<?php
include_once 'footer.php';
?>