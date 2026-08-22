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
        color: #4C7742;
    }

    .officer-header p{
        font-size:14px;
        color: #666;

    }

    /* ADD BUTTON */
    #nav_addofficer {
        background: #FFC857;
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
        position: fixed;
        inset: 0;
        z-index: 1000;
        background: rgba(0, 0, 0, 0.45);
        justify-content: center;
        align-items: center;
    }

    /* SHOW WITH JS */
    .modal.show {
        display: flex;
    }

    /* MODAL BOX */
    .modal-content {
        background: #fff;
        width: 400px;
        max-width: 90%;
        border-radius: 10px;
        padding: 24px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, .2);
        animation: fadeIn .2s ease;
        position: relative;
    }

    /* TITLE */
    .modal-content strong {
        display: block;
        font-size: 18px;
        padding-bottom: 12px;
    margin-bottom: 16px;
    border-bottom: 1px solid #e5e5e5;
        color: #d32f2f;
    }

    /* DETAILS */
    .modal-content p {
        margin: 8px 0;
        color: black;
        font-size: 14px;
    }

    .modal-info {
    background: #f8f9fa;
    border: 1px solid #e3e6ea;
    border-radius: 8px;
    padding: 14px 16px;
    margin: 18px 0;
}

.modal-info p {
    margin: 8px 0;
    font-size: 14px;
    color: #444;
    display: flex;
    align-items: center;
}

.modal-info p:not(:last-child) {
    border-bottom: 1px solid #ececec;
    padding-bottom: 8px;
}

.modal-info span {
    font-weight: 600;
    color: #222;
}

    /* CLOSE BUTTON */
    .close {
        position: absolute;
        top: 12px;
        right: 16px;
        font-size: 24px;
        color: #777;
        cursor: pointer;
        transition: .2s;
    }

    .close:hover {
        color: #000;
    }

    /* BUTTONS */
    .modal-buttons {
        display: flex;
        justify-content: center;
        gap: 12px;
        margin-top: 24px;
        padding-top: 16px;
        border-top: 1px solid #e5e5e5;
    }

    #confirmBtn,
    #cancelBtn {
        border: none;
        padding: 9px 18px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
    }

    #confirmBtn {
        background: #d32f2f;
        color: white;
    }

    #confirmBtn:hover {
        background: #b71c1c;
    }

    #cancelBtn {
        background: #e0e0e0;
        color: #333;
    }

    #cancelBtn:hover {
        background: #cfcfcf;
    }

    /* ANIMATION */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-15px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .action-group {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .action-group form {
        margin: 0;
    }

    .action-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 5px;
        background: none;
        border: none;
        color: #555;
        text-decoration: none;
        cursor: pointer;
        font-size: 16px;
    }

    .action-icon:hover {
        color: #1e88e5;
    }

    .action-btn {
        background: none;
        border: none;
    }

    .notice{

    }
</style>

<div class="officer-header">
    
<div class="officer-title">
    <h2>Officer Accounts</h2>
    <p>Manage officers user accounts.</p>
</div>

    <a href="admin_officers_create.php" id="nav_addofficer"> &plus; Add Officer</a>
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
                <td><?= $o['last_name'] .', ' . $o['first_name'] ?></td>
                <td><?= $o['barangay'] ?></td>
                <td><?= $o['phonenumber'] ?></td>
                <td>
                     <div class="action-group">
                        <a href="/esko/admin/admin_officers_edit.php?officerId=<?= $o['user_id'] ?>"
                                class="action-icon"
                                title="Edit Officer Details">
                                <i class="fa-solid fa-pen-to-square"></i></a>
                    <form method="POST" action="/esko/admin/admin_officers_deactivate.php" name="deleteForm">
                            <input type="hidden"
                                name="id"
                                value="<?= $o['user_id'] ?>">
                            <button type="button"
                                class="action-icon action-btn officer-action-btn"
                                data-name="<?= $o['last_name'] . ', ' . $o['first_name'] ?>"
                                data-barangay="<?= $o['barangay'] ?>"
                                data-officerno="<?= $o['username'] ?>"
                                 title="<?= $o['is_active'] ? 'Deactivate User' : 'Reactivate User' ?>">
                                <i class="fa-solid fa-trash"></i></button>
                    </form>
                     </div>
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
        // if(ev.target.classList.contains('delete-btn')){
        //      ev.preventDefault();

        // modalName.innerText = ev.target.dataset.name;
        // modalBrgy.innerText = ev.target.dataset.barangay;
        // modalOfficerNo.innerText = ev.target.dataset.officerno;

        // currentForm = ev.target.closest("form");
        // modal.style.display = "block";
        // } 

        const btn = ev.target.closest('.officer-action-btn');
        if (btn) {
            ev.preventDefault();

            modalName.innerText = btn.dataset.name;
            modalBrgy.innerText = btn.dataset.barangay;
            modalOfficerNo.innerText = btn.dataset.officer;

            currentForm = btn.closest('form');
            modal.style.display = 'flex';
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