<?php
$title = "Edit";
// include "../assets/layout.php";
// require_once "../classes/supabase.php";
// $config = require __DIR__ . "esko/../api/supabase.php";
// $api = new Supabase($config);
// include "../classes/supa_officers.php";

require_once "../test.php";
$adminId = $_SESSION['userid'];
$officerId = $_GET['officerId'] ?? null;
// $offobj = new Officer($api);
$officer = $offobj->getOfficerInfo($officerId);

// handle update
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_officer'])) {

    $officer_id = $officerId;
    $first_name = strtoupper(trim($_POST['first_name']));
    $last_name = strtoupper(trim($_POST['last_name']));
    $barangay = $_POST['barangay'];
    $phonenumber = $_POST['phonenumber'];
    $email = $_POST['email'];
    $officerno = strtoupper(trim($_POST['officerno']));
    $password = $_POST['password'] ?? null;

    try {
        $updateOfficer = $offobj->updateOfficer(
            $officer_id,
            $first_name,
            $last_name,
            $barangay,
            $phonenumber,
            $email,
            $officerno,
            $password
        );

        if ($updateOfficer) {
            header("Location: admin_officers.php?officerId=$officerId");
            echo "<script>alert('Updated!');</script>";
            exit;
    
        } else {
            echo "Update failed.";
        }

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

    include "../assets/layout.php";
?>


<div class="page-header">
    <h2>Edit Officer</h2>
    <p> Edit officer information and account details.</p>
</div>

<div id="user-form-container">
    <form method="POST" class="user-form">

        <input type="hidden"
               name="officer_id"
               value="<?= $officer['user_id'] ?>">

        <h4>Personal Details</h4>

        <div class="form-row-2">

            <div class="form-group">
                <label>First Name</label>
                <input type="text"
                       name="first_name"
                       value="<?= htmlspecialchars($officer['first_name']) ?>">
            </div>

            <div class="form-group">
                <label>Last Name</label>
                <input type="text"
                       name="last_name"
                       value="<?= htmlspecialchars($officer['last_name']) ?>">
            </div>

        </div>

        <div class="form-group">
            <label>Barangay</label>

            <select name="barangay">
                <?php
                $barangays = [
                    "Atilano Ricardo","Bagumbayan","Banawang","Binuangan",
                    "Binukawan","Ibis","Ibaba","Pag-asa","Parang","Paysawan",
                    "Quinawan","San Antonio","Saysain","Tabing-Ilog"
                ];

                foreach ($barangays as $b): ?>
                    <option value="<?= htmlspecialchars($b) ?>"
                        <?= ($officer['barangay'] == $b) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($b) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <h4>Contact Details</h4>

        <div class="form-row-2">

            <div class="form-group">
                <label>Phone Number</label>
                <input type="text"
                       name="phonenumber"
                       value="<?= htmlspecialchars($officer['phonenumber']) ?>">
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email"
                       name="email"
                       value="<?= htmlspecialchars($officer['email']) ?>">
            </div>

        </div>

        <h4>Account Details</h4>

        <div class="form-row-2">

            <div class="form-group">
                <label>Officer No.</label>
                <input type="text"
                       name="officerno"
                       value="<?= htmlspecialchars($officer['username']) ?>">
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password"
                       name="password"
                       placeholder="Leave blank if unchanged">
            </div>

        </div>

        <button type="submit" name="update_officer">
            Update Officer
        </button>

    </form>
</div>


<?php include "../assets/layout_end.php"; ?>