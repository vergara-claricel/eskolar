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
        $error= "";
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
    #scholarform {
        max-width: 480px;
        margin: 12px auto;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 14px 20px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        font-family: system-ui, -apple-system, sans-serif;
    }

    /* Low-profile section headers */
    .scholar-form h4 {
        font-size: 0.75rem;
        font-weight: 600;
        color: #4C7742;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 12px 0 6px 0;
    }

    .scholar-form br {
        display: none;
    }

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
    .scholar-form input[type="number"],
    .scholar-form select {
        width: 100%;
        padding: 6px 10px;
        /* Tight padding */
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
        background-color: #FFC857;
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

    .error-box {
    background: #fdecea;
    color: #b71c1c;
    border: 1px solid #f5c6cb;
    padding: 10px 14px;
    border-radius: 6px;
    margin-bottom: 15px;
    font-size: 14px;
}

.success-box {
    background: #e8f5e9;
    color: #2e7d32;
    border: 1px solid #a5d6a7;
    padding: 10px 14px;
    border-radius: 6px;
    margin-bottom: 15px;
    font-size: 14px;
}
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
                <input type="text"
                    name="phonenumber"
                    id="phonenumber"
                    value="<?= htmlspecialchars($_POST['phonenumber'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email Address*</label>
                <input type="email"
                    name="email"
                    id="email"
                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
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