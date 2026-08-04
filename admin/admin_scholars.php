<?php
// require_once "../classes/supabase.php";
// $config = require __DIR__ . "esko/../api/supabase.php";

// $api = new Supabase($config);
// include "../classes/supa_scholar.php";
// $schoobj = new Scholar($api);

$title = "Scholars";
require_once "../test.php";
include "../assets/layout.php";


$allscho = $schoobj->getAllActiveScholars();
// print_r($allscho);
$activeSem = $semobj->getActiveSemester();
// include "../assets/layout.php";

?>

<style>
    .scholar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        margin-inline: 50px;
    }

    /* TITLE */
    .scholar-header h2 {
        font-size: 22px;
        font-weight: 600;
        color: #222;
    }

    /* ADD BUTTON */
    #nav_addscholar {
        background: #1e293b;
        color: white;
        padding: 8px 14px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 500;
        transition: 0.2s;
    }

    #nav_addscholar:hover {
        background: #1565c0;
    }


    /* BACKDROP */
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

    .filter-bar {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        margin: 15px 0;
    }

    #searchForm {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    #search {
        width: 250px;
        padding: 8px 12px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    #searchForm button {
        padding: 8px 16px;
        border: none;
        background: #2d8cff;
        color: white;
        border-radius: 5px;
        cursor: pointer;
    }

    #searchForm button:hover {
        background: #1f73d8;
    }

    #statusFilter,
    #barangayFilter {
        min-width: 170px;
        padding: 8px 12px;
        border: 1px solid #ccc;
        border-radius: 5px;
        cursor: pointer;
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
</style>
<div class="scholar-header">
    <h2>Scholars</h2>
    <a href="admin_scholars_create.php" id="nav_addscholar">Add Scholar</a>
</div>

<div class="filter-bar">
    <form method="GET" id="searchForm">
        <input type="text" name="search" id="search" placeholder="Search…" value="<?= $_GET['search'] ?? '' ?>">
        <button type="submit">search</button>
    </form>

    <select name="status" id="statusFilter">
        <option value="1">Active</option>
        <option value="0">Inactive</option>
        <option value="">All Scholars</option>
    </select>

    <select name="barangay" id="barangayFilter">
        <option value="">All Barangays</option>
        <option value="Atilano Ricardo">Atilano Ricardo</option>
        <option value="Bagumbayan">Bagumbayan</option>
        <option value="Banawang">Banawang</option>
        <option value="Binuangan">Binuangan</option>
        <option value="Binukawan">Binukawan</option>
        <option value="Ibis">Ibis</option>
        <option value="Ibaba">Ibaba</option>
        <option value="Pag-asa">Pag-asa</option>
        <option value="Parang">Parang</option>
        <option value="Paysawan">Paysawan</option>
        <option value="Quinawan">Quinawan</option>
        <option value="San Antonio">San Antonio</option>
        <option value="Saysain">Saysain</option>
        <option value="Tabing-Ilog">Tabing-Ilog</option>
    </select>
</div>


<div class="table-container">
    <table class="table">
        <thead style="background: rgb(64, 188, 64);">
            <tr>
                <th>Iskolar No. </th>
                <th>Full Name</th>
                <th>Barangay</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody id="scholarsTable">
            <?php foreach ($allscho as $s): ?>

                <tr>
                    <td><?= $s['username'] ?></td>
                    <td><?= $s['last_name'] . ', ' . $s['first_name'] ?></td>
                    <td><?= $s['barangay'] ?></td>
                    <td>
                        <div class="action-group">
                            <a href="/esko/admin/admin_scholars_view.php?semid=<?= urlencode($activeSem['sem_id']) ?>&scholarid=<?= urlencode($s['user_id']) ?>"
                                class="action-icon"
                                title="View Scholar Records">
                                <i class="fa-solid fa-eye"></i></a>
                            <a href="/esko/admin/admin_scholars_edit.php?scholarid=<?= urlencode($s['user_id']) ?>"
                                class="action-icon"
                                title="Edit Scholar Details">
                                <i class="fa-solid fa-pen-to-square"></i></a>
                            <form method="POST" action="/esko/admin/admin_scholars_deactivate.php" name="deleteForm">
                                <input type="hidden"
                                    name="scholarid"
                                    value="<?= $s['user_id'] ?>">
                                <button type="button"
                                    class="action-icon action-btn scholar-action-btn"
                                    title="<?= $s['is_active'] ? 'Deactivate' : 'Reactivate' ?>"
                                    data-action="<?= $s['is_active'] ? 'deactivate' : 'reactivate' ?>"
                                    data-name="<?= $s['last_name'] . ', ' . $s['first_name'] ?>"
                                    data-barangay="<?= $s['barangay'] ?>"
                                    data-iskolarno="<?= $s['username'] ?>">
                                    <i class="fa-solid <?= $s['is_active'] ? 'fa-trash' : 'fa-rotate-left' ?>"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>


<!-- The Modal -->
<div id="myModal" class="modal">

    <!-- Modal content -->
    <div class="modal-content">
        <span class="close" id="closeModal">&times;</span>
        <p><strong>Action Confirmation</strong></p>
        <p>Are you sure you want to <span id="modalAction"></span> this account?</p>
        <div class="modal-info">
            <p><span id="modalIskoNo"></span></p>
            <p><span id="modalName"></span></p>
            <p><span id="modalBrgy"></span></p>
        </div>
        <div class="modal-buttons">
        <button id="confirmBtn">Confirm</button>
        <button id="cancelBtn">Cancel</button></div>
    </div>

</div>

<script>
    let timer;

    const tableBody = document.getElementById('scholarsTable');
    const originalHTML = tableBody.innerHTML;

    function loadScholars() {
        clearTimeout(timer);

        timer = setTimeout(() => {
            let search = document.getElementById('search').value.trim();
            let status = document.getElementById('statusFilter').value;
            let barangay = document.getElementById('barangayFilter').value;
            if (search === "" && status == "" && barangay == "") {
                tableBody.innerHTML = originalHTML;
                return;
            }

            fetch(`admin_scholars_filter.php?search=${encodeURIComponent(search)}&status=${status}&barangay=${encodeURIComponent(barangay)}`)
                .then(res => res.text())
                .then(data => {
                    tableBody.innerHTML = data;
                });

        }, 300);
    }

    // live search
    document.getElementById('search').addEventListener('input', loadScholars);
    document.getElementById('statusFilter').addEventListener('change', loadScholars);
    document.getElementById('barangayFilter').addEventListener('change', loadScholars);

    const searchInput = document.getElementById('search');
    const form = document.getElementById('searchForm');

    form.addEventListener('submit', e => e.preventDefault());

    searchInput.addEventListener('keydown', e => {
        if (e.key === 'Enter') e.preventDefault();
    });

    let currentForm;
    let modal = document.getElementById("myModal");
    let modalName = document.getElementById("modalName");
    let modalBrgy = document.getElementById("modalBrgy");
    let modalIskoNo = document.getElementById("modalIskoNo");
    let confirmBtn = document.getElementById("confirmBtn");
    let closeModal = document.getElementById("closeModal");
    let cancelBtn = document.getElementById("cancelBtn");

    document.addEventListener('click', function(ev) {
        // if(ev.target.classList.contains('delete-btn')){
        //      ev.preventDefault();

        // modalName.innerText = ev.target.dataset.name;
        // modalBrgy.innerText = ev.target.dataset.barangay;
        // modalIskoNo.innerText = ev.target.dataset.iskolarno;

        // currentForm = ev.target.closest("form");
        // modal.style.display = "block";
        // }    

        // reactivation logic
        const btn = ev.target.closest('.scholar-action-btn');
        if (btn) {
            document.getElementById('modalAction').innerText =
                btn.dataset.action;

            modalName.innerText = btn.dataset.name;
            modalBrgy.innerText = btn.dataset.barangay;
            modalIskoNo.innerText = btn.dataset.iskolarno;

            currentForm = btn.closest('form');
            modal.style.display = 'flex';
        }
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