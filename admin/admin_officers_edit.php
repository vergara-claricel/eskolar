<?php
$title = "Edit";
include "../assets/layout.php";
require_once "../classes/supabase.php";
$config = require __DIR__ . "esko/../api/supabase.php";
$api = new Supabase($config);
include "../classes/supa_officers.php";

$officerId = $_GET['officerId'] ?? null;
$offobj = new Officer($api);
$officer = $offobj->getOfficerInfo($officerId);

// handle update
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_officer'])) {

    $officer_id = $officerId;
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $barangay = $_POST['barangay'];
    $phonenumber = $_POST['phonenumber'];
    $email = $_POST['email'];
    $officerno = $_POST['officerno'];
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
?>
<style>
    /* FORM GRID */

/* Container adjusted for a neat, compact 480px block */
#editscholarform {
    max-width: 480px; 
    margin: 12px auto;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 14px 20px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    font-family: system-ui, -apple-system, sans-serif;
}


/* Low-profile section headers */
.scholar-form h4 {
    font-size: 0.75rem;
    font-weight: 600;
    color: #1e88e5;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin: 12px 0 6px 0;
}

.scholar-form br { display: none; }

/* The Grid System: Forces fields side-by-side to save height */
.form-row-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    margin-bottom: 4px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 3px;
}

.scholar-form label {
    font-size: 0.75rem;
    font-weight: 500;
    color: #64748b;
}

/* Reduced height inputs */
.scholar-form input[type="text"],
.scholar-form input[type="email"],
.scholar-form input[type="password"],
.scholar-form select {
    width: 100%;
    padding: 6px 10px; /* Tight padding */
    font-size: 0.85rem;
    color: #1e293b;
    background-color: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 5px;
    box-sizing: border-box;
}

.scholar-form input:focus,
.scholar-form select:focus {
    outline: none;
    border-color: #2563eb;
    background-color: #ffffff;
}

.scholar-form input::placeholder {
    font-size: 0.75rem;
    color: #94a3b8;
}

/* Small, inline-feeling button */
.scholar-form button[type="submit"] {
    width: 100%;
    padding: 8px;
    margin-top: 14px;
    background-color: #0f172a;
    color: #ffffff;
    border: none;
    border-radius: 5px;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
}

.scholar-form button[type="submit"]:hover {
    background-color: #1e293b;
}
</style>


<h2>Edit Officer</h2>
<div id="editscholarform">
    <form method="POST" class="scholar-form">

    <input type="hidden" name="officer_id" value="<?= $officer['user_id'] ?>">

    <h4>Personal Details</h4>

    <div class="name-row">
        <div>
            <label>First Name</label>
            <input type="text" name="first_name"
                   value="<?= htmlspecialchars($officer['first_name']) ?>">
        </div>

        <div>
            <label>Last Name</label>
            <input type="text" name="last_name"
                   value="<?= htmlspecialchars($officer['last_name']) ?>">
        </div>
    </div>

    <label>Barangay</label>
    <select name="barangay">
        <?php
        $barangays = [
            "Atilano Ricardo","Bagumbayan","Banawang","Binuangan","Binukawan",
            "Ibis","Ibaba","Pag-asa","Parang","Paysawan","Quinawan",
            "San Antonio","Saysain","Tabing-Ilog"
        ];

        foreach ($barangays as $b): ?>
            <option value="<?= $b ?>"
                <?= ($officer['barangay'] == $b) ? 'selected' : '' ?>>
                <?= $b ?>
            </option>
        <?php endforeach; ?>
    </select>

    <h4>Contact Details</h4>
            <br>
    <label>Phone Number</label>
    <input type="text" name="phonenumber"
           value="<?= htmlspecialchars($officer['phonenumber']) ?>">

    <label>Email</label>
    <input type="email" name="email"
           value="<?= htmlspecialchars($officer['email']) ?>">

    <h4>Account Details</h4>
<br>
    <label>Officer No.</label>
    <input type="text" name="officerno"
           value="<?= htmlspecialchars($officer['username']) ?>">

    <label>Password</label>
    <input type="password" name="password"
           placeholder="Leave blank if unchanged">

    <button type="submit" name="update_officer">
        Update Officer
    </button>

    </form>
</div>


<?php include "../assets/layout_end.php"; ?>