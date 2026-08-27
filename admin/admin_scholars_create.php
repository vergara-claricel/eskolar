<?php

$title = "Scholars";
require_once "../test.php";
$adminId = $_SESSION['userid'];
$activeSem = $semobj->getActiveSemester();
$semId = $activeSem['sem_id'];

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

$nextScholarNo = $schoobj->getNextScholarNumber();

if (isset($_POST['create_scholar'])) {

    $first_name = strtoupper(trim($_POST['first_name']));
    $last_name = strtoupper(trim($_POST['last_name']));
    $barangay = $_POST['barangay'];
    $phone = $_POST['phonenumber'];
    $email = $_POST['email'];
    $iskolarno = strtoupper(trim($_POST['iskolarno']));
    $password = $_POST['password'];

    try {
        $error = "";
        $result = $schoobj->addScholar($first_name, $last_name, $barangay, $phone, $email, $iskolarno, $password);

        if (is_array($result) && $result['success'] === false) {
            $error = $result['message'];
        } else {
            header("Location: /esko/admin/admin_scholars_view.php?semid=$semId&scholarid=$result");
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

<?php if (!empty($error)): ?>
    <div class="error-box">
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<div class="page-header">
    <h2>Create Scholar Account</h2>
    <p>Please fill out all fields.</p>
</div>

<div id="user-form-container">
    <form method="POST" class="user-form">

        <h4>Personal Details</h4>

        <div class="form-row-2">
            <div class="form-group">
                <label for="first_name">First Name*</label>
                <input type="text"
                    name="first_name"
                    id="first_name"
                    value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="last_name">Last Name*</label>
                <input type="text"
                    name="last_name"
                    id="last_name"
                    value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label for="barangay">Barangay*</label>

            <select name="barangay" id="barangay" required>
                <?php foreach ($barangays as $b): ?>
                    <option value="<?= htmlspecialchars($b) ?>"
                        <?= (($_POST['barangay'] ?? '') == $b) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($b) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>


        <h4>Contact Details</h4>

        <div class="form-row-2">
            <div class="form-group">
                <label for="phonenumber">Phone Number*</label>
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
                <label for="email">Email Address*</label>
                <input type="email"
                    name="email"
                    id="email"
                    value="" required>
            </div>
        </div>


        <h4>Account Details</h4>

        <div class="form-row-2">
            <div class="form-group">
                <label for="iskolarno">
                    Iskolar No.*
                    <span class="suggested">
                        Suggested: <?= htmlspecialchars($_POST['iskolarno'] ?? $nextScholarNo) ?>
                    </span>
                </label>

                <input type="text"
                    name="iskolarno"
                    id="iskolarno"
                    value="<?= htmlspecialchars($_POST['iskolarno'] ?? 'INB-') ?>"
                    maxlength="8"
                    pattern="INB-\d{4}" required>
            </div>

            <div class="form-group">
                <label for="password">Password*</label>
                <input type="password"
                    name="password"
                    id="password" required>
            </div>
        </div>

        <button type="submit" name="create_scholar">
            Add Scholar
        </button>

    </form>
</div>


<?php
include "../assets/layout_end.php";
?>