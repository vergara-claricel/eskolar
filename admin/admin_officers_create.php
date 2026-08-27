<?php
$title = "Officer";
require_once "../test.php";
$adminId = $_SESSION['userid'];

$barangays = [
    "Atilano Ricardo",
    "Bagumbayan",
    "Banawang",
    "Binuangan",
    "Binukawan",
    "Ibis",
    "Ibaba",
    "Pag-asa",
    "Parang",
    "Paysawan",
    "Quinawan",
    "San Antonio",
    "Saysain",
    "Tabing-Ilog"
];
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $first_name = strtoupper(trim($_POST['first_name']));
    $last_name = strtoupper(trim($_POST['last_name']));
    $barangay = $_POST['barangay'];
    $role = $_POST['role'];
    $phone = '+63' . trim($_POST['phonenumber']);
    $email = $_POST['email'];
    $officerno = strtoupper(trim($_POST['officerno']));
    $password = $_POST['password'];

    try {

        $result = $offobj->createOfficer($first_name, $last_name, $barangay, $role, $phone, $email, $officerno, $password);
        if (!$result['success']) {
            if ($result['error'] === 'duplicate_officerno') {
                $error = "Officer number already exists.";
            } else if ($result['error'] === 'duplicate_representative') {
                $error = "Representative already exists.";
            } else {
                $error = "Unable to create officer.";
            }
        } else {
            header("location: /esko/admin/admin_officers.php");
            exit;
        }
    } catch (Exception $e) {
        //    $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }
}

include "../assets/layout.php";
?>
<style>

</style>

<div class="page-header">
    <h2>Create Officer Account</h2>
    <p>Fields with asterisk (*) are required.</p>
</div>
<?php if (!empty($error)): ?>
    <div class="error-box">
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>
<div id="user-form-container">
    <form method="POST" class="user-form">

        <h4>Personal Details</h4>

        <div class="form-row-2">
            <div class="form-group">
                <label for="officer_first_name">First Name*</label>
                <input type="text"
                    name="first_name"
                    id="officer_first_name" required>
            </div>

            <div class="form-group">
                <label for="officer_last_name">Last Name*</label>
                <input type="text"
                    name="last_name"
                    id="officer_last_name" required>
            </div>
        </div>

        <div class="form-row-2">
            <div class="form-group">
                <label for="role">Position*</label>

                <select name="role" id="role" required>
                    <option value="executive">Executive</option>
                    <option value="representative">Representative</option>
                </select>
            </div>

            <div class="form-group">
                <label for="officer_barangay">Barangay</label>

                <select name="barangay" id="officer_barangay">
                    <?php foreach ($barangays as $b): ?>
                        <option value="<?= htmlspecialchars($b) ?>">
                            <?= htmlspecialchars($b) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>


        <h4>Contact Details</h4>

        <div class="form-row-2">
            <div class="form-group">
                <label for="officer_phone">Phone Number*</label>

                <div class="phone-input">
                    <span class="phone-prefix">+63</span>
                    <input
                        type="text"
                        name="phonenumber"
                        id="officer_phone"
                        placeholder="9123456789"
                        maxlength="10"
                        required>
                </div>
            </div>

            <div class="form-group">
                <label for="officer_email">Email Address*</label>
                <input type="email"
                    name="email"
                    id="officer_email" required>
            </div>
        </div>


        <h4>Account Details</h4>

        <div class="form-row-2">
            <div class="form-group">
                <label for="officerno">Officer No.*</label>
                <input type="text"
                    name="officerno"
                    id="officerno" required>
            </div>

            <div class="form-group">
                <label for="officer_password">Password*</label>
                <input type="password"
                    name="password"
                    id="officer_password" required>
            </div>
        </div>

        <button type="submit" name="create_officer">
            Create Officer
        </button>

    </form>
</div>

<?php
include "../assets/layout_end.php";
?>