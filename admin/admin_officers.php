<?php
$title = "Dashboard";
// include "../assets/layout.php";
// require_once "../classes/supabase.php";
// $config = require __DIR__ . "esko/../api/supabase.php";
// $api = new Supabase($config);
// include "../classes/supa_officers.php";

require_once "../test.php";
// $offobj = new Officer($api);
$officers = $offobj->getOfficers();
include "../assets/layout.php";


?>

<style>
        .officer-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        margin-inline: 50px;
    }

    /* TITLE */
    .officer-header h2 {
        font-size: 22px;
        font-weight: 600;
        color: #222;
    }

    /* ADD BUTTON */
    #nav_addofficer {
        background: #1e293b;
        color: white;
        padding: 8px 14px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 500;
        transition: 0.2s;
    }

    #nav_addofficer:hover {
        background: #1565c0;
    }

        .modal {
        display: none;
        /* Hidden by default */
        position: fixed;
        /* Stay in place */
        z-index: 1;
        /* Sit on top */
        left: 0;
        top: 0;
        width: 100%;
        /* Full width */
        height: 100%;
        /* Full height */
        overflow: auto;
        /* Enable scroll if needed */
        background-color: rgb(0, 0, 0);
        /* Fallback color */
        background-color: rgba(0, 0, 0, 0.4);
        /* Black w/ opacity */
    }

    /* Modal Content/Box */
    .modal-content {
        background-color: #fefefe;
        margin: 15% auto;
        /* 15% from the top and centered */
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
        /* Could be more or less, depending on screen size */
    }

    /* The Close Button */
    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }
</style>

<div class="officer-header">
    <h2>Officer Accounts</h2>
    <a href="admin_officers_create.php" id="nav_addofficer">Add Officer</a>
</div>
<div class="table-container">
        <table class="table">
    <thead>
        <tr>
            <th>Id</th>
            <th>Full Name</th>
            <th>Barangay</th>
            <th>Contact</th>
            <th>Actions</th>
        </tr>
    </thead>

    <tbody>
        <?php foreach ($officers as $o): ?>
            <tr>
                <td><?= $o['username'] ?></td>
                <td><?= $o['first_name'] .' ' . $o['last_name'] ?></td>
                <td><?= $o['barangay'] ?></td>
                <td><?= $o['phonenumber'] ?></td>
                <td>
                    <!-- <a href="/esko/admin/admin_activities_view.php?Id=<?= $o['ar_id'] ?>">Edit</a> -->
                     <a href="/esko/admin/admin_officers_edit.php?officerId=<?= $o['user_id'] ?>">
                            Edit
                            </a>
                    <!-- <a href="/esko/admin/admin_officers_deactivate.php?officerId=<?= $o['user_id'] ?>">Delete</a> -->
                    <form method="POST" action="/esko/admin/admin_officers_deactivate.php" name="deleteForm">
                            <input type="hidden"
                                name="id"
                                value="<?= $o['user_id'] ?>">
                            <button type="button"
                                class="delete-btn"
                                data-name="<?= $o['last_name'] . ', ' . $o['first_name'] ?>"
                                data-barangay="<?= $o['barangay'] ?>"
                                data-officerno="<?= $o['username'] ?>">
                                Delete
                            </button>
                    </form>
                </td>

            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div id="myModal" class="modal">

    <!-- Modal content -->
    <div class="modal-content">
        <span class="close" id="closeModal">&times;</span>
        <p><strong>Action Confirmation</strong></p>
        <p>Are you sure you want to delete this account?</p>
        <p><span id="modalOfficerNo"></span></p>
        <p><span id="modalName"></span></p>
        <p><span id="modalBrgy"></span></p>
        <button id="confirmBtn">Confirm</button>
        <button id="cancelBtn">Cancel</button>
    </div>

</div>

<script>
    let currentForm;
    let modal = document.getElementById("myModal");
    let modalName = document.getElementById("modalName");
    let modalBrgy = document.getElementById("modalBrgy");
    let modalOfficerNo = document.getElementById("modalOfficerNo");
    let confirmBtn = document.getElementById("confirmBtn");
    let closeModal = document.getElementById("closeModal");
    let cancelBtn = document.getElementById("cancelBtn");

    document.addEventListener('click', function(ev) {
        if(ev.target.classList.contains('delete-btn')){
             ev.preventDefault();

        modalName.innerText = ev.target.dataset.name;
        modalBrgy.innerText = ev.target.dataset.barangay;
        modalOfficerNo.innerText = ev.target.dataset.officerno;

        currentForm = ev.target.closest("form");
        modal.style.display = "block";
        }    
        // reactivation logic
    });

    confirmBtn.addEventListener("click", function() {
        if (currentForm) {
            currentForm.submit();
        }
    });

    closeModal.onclick = function() {
        modal.style.display = "none";
    }

    cancelBtn.onclick = function() {
        modal.style.display = "none";
    }
</script>
<?php include "../assets/layout_end.php"; ?>