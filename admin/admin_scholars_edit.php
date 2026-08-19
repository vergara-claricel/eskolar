<?php
$title = "Edit";
// include "../assets/layout.php";
// include "../classes/supa_scholar.php";
// require_once "../classes/supabase.php";
// $config = require __DIR__ . "esko/../api/supabase.php";

// $api = new Supabase($config);
// $schoobj = new Scholar($api);
require_once "../test.php";
$scholarId = $_GET['scholarid'] ?? null;
$scholar = $schoobj->getScholarInfo($scholarId);
// handle update
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_scholar'])) {

    $user_id = $_POST['user_id'];
    $first_name = strtoupper(trim($_POST['first_name']));
    $last_name = strtoupper(trim($_POST['last_name']));
    $barangay = $_POST['barangay'];
    $phonenumber = $_POST['phonenumber'];
    $email = $_POST['email'];
    $iskolarno = strtoupper(trim($_POST['iskolarno']));
    $password = $_POST['password'] ?? null;

    try {
        $updateScholar = $schoobj->updateScholar(
            $user_id,
            $first_name,
            $last_name,
            $barangay,
            $phonenumber,
            $email,
            $iskolarno,
            $password
        );

        if ($updateScholar) {
            header("Location: admin_scholars_view.php?scholarid=$scholarId");
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
<style>
</style>

<div class="page-header">
    <h2>Edit Scholar</h2>
    <p> Edit scholar information and account details.</p>
</div>

<div id="edituserform">
    <form method="POST" class="user-form">

        <input type="hidden" name="user_id" value="<?= $scholar['user_id'] ?>">

        <h4>Personal Details</h4>

        <div class="form-row-2">
            <div class="form-group">
                <label>First Name</label>
                <input type="text"
                       name="first_name"
                       value="<?= htmlspecialchars($scholar['users']['first_name']) ?>">
            </div>

            <div class="form-group">
                <label>Last Name</label>
                <input type="text"
                       name="last_name"
                       value="<?= htmlspecialchars($scholar['users']['last_name']) ?>">
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
                        <?= ($scholar['barangay'] == $b) ? 'selected' : '' ?>>
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
                       value="<?= htmlspecialchars($scholar['phonenumber']) ?>">
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email"
                       name="email"
                       value="<?= htmlspecialchars($scholar['email']) ?>">
            </div>
        </div>

        <h4>Account Details</h4>

        <div class="form-row-2">
            <div class="form-group">
                <label>Iskolar No.</label>
                <input type="text"
                       name="iskolarno"
                       value="<?= htmlspecialchars($scholar['username']) ?>">
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password"
                       name="password"
                       placeholder="Leave blank if unchanged">
            </div>
        </div>

        <button type="submit" name="update_scholar">
            Update Scholar
        </button>

    </form>
</div>


<?php include "../assets/layout_end.php"; ?>