<?php
// require_once "../classes/supabase.php";
// $config = require __DIR__ . "esko/../api/supabase.php";

// $api = new Supabase($config);
// include "../classes/supa_scholar.php";
// $schoobj = new Scholar($api);

$title = "Scholars";
require_once "../test.php";
$adminId = $_SESSION['userid'];
$barangays = [
    'Atilano Ricardo',
    'Bagumbayan',
    'Banawang',
    'Binuangan',
    'Binukawan',
    'Ibis',
    'Ibaba',
    'Pag-asa',
    'Parang',
    'Paysawan',
    'Quinawan',
    'San Antonio',
    'Saysain',
    'Tabing-Ilog'
];

// $allscho = $schoobj->getAllActiveScholars();
// print_r($allscho);
$activeSem = $semobj->getActiveSemester();
$semId = $activeSem['sem_id'] ?? '';
// include "../assets/layout.php";
$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? '1';
$barangay = $_GET['barangay'] ?? '';

$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 5;
$offset = ($page - 1) * $limit;

if ($search === '' && $status === '1' && $barangay === '') {
    $allscho = $schoobj->getAllActiveScholars($page, $limit);
    $totalRows = $schoobj->getActiveScholarCount();
} else {
    $allscho = $schoobj->filterScholars(
        $search,
        $status,
        $barangay,
        $limit,
        $offset
    );

    $totalRows = $schoobj->countFilteredScholars(
        $search,
        $status,
        $barangay
    );
}

$totalPages = ceil($totalRows / $limit);
include "../assets/layout.php";
// $allscho = $schoobj->getAllActiveScholars($page, $limit);
// $totalRows = $schoobj->getActiveScholarCount();
// $totalPages = ceil($totalRows / $limit);
?>

<style>

    /* ADD BUTTON */
    #nav_addscholar {
        background: #FFC857;
        color: white;
        padding: 8px 14px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 500;
        margin-left: auto;
        margin-right: 20px;
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
    align-items: flex-end;
    gap: 12px;
    flex-wrap: wrap;
    margin: 15px 0;
    width: 100%;
    box-sizing: border-box;
    }

    #searchForm {
        display: flex;
    align-items: flex-end;
    gap: 8px;
    flex-wrap: wrap;
    }

    #search {
        width: 250px;
    height: 36px;
    padding: 8px 12px;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-sizing: border-box;
    }

    #searchForm button {
        height: 36px;
    padding: 8px 16px;
    border: none;
    background: #2d8cff;
    color: white;
    border-radius: 5px;
    cursor: pointer;
    box-sizing: border-box;
    }

    #searchForm button:hover {
        background: #1f73d8;
    }

    #statusFilter,
    #barangayFilter {
        min-width: 170px;
    height: 36px;
    padding: 8px 12px;
    border: 1px solid #ccc;
    border-radius: 5px;
    cursor: pointer;
    box-sizing: border-box;
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


    .action-btn {
        background: none;
        border: none;
    }

    @media (max-width: 480px) {

    /* Entire filter section */
    .filter-bar {
        flex-direction: column;
        align-items: stretch;
        width: 100%;
        gap: 12px;
    }

    /* Form becomes the mobile layout */
    #searchForm {
        display: flex;
        flex-direction: column;
        align-items: stretch;
        width: 100%;
        gap: 12px;
    }

    /* Search input */
    #search {
        width: 100%;
        height: 40px;
    }

    /* Search button */
    #search button {
        width: 100%;
        height: 40px;
    }

    /* Filter items */
    .filter-item {
        width: 100%;
    }

    #statusFilter,
    #barangayFilter {
        width: 100%;
        min-width: 0;
        height: 40px;
    }

    /* Reset button */
    .action-icon {
        align-self: flex-start;
    }

    /* Create Scholar */
    #nav_addscholar {
        width: 100%;
        margin: 0;
        box-sizing: border-box;
    }
}

</style>

<div class="page-header">
    <h2>Scholars</h2>
    <p>View scholar attendance records, edit scholar details and manage account status. </p>
</div>

<!-- filter  -->
<div class="filter-bar">
    <form method="GET" id="searchForm">
        <input type="text" name="search" id="search" placeholder="Search…" value="<?= $_GET['search'] ?? '' ?>">
        <button type="submit">search</button>
    

    <select name="status" id="statusFilter">
        <option value="1" <?= $status === "1" ? "selected" : "" ?>>
            Active
        </option>

        <option value="0" <?= $status === "0" ? "selected" : "" ?>>
            Inactive
        </option>

        <option value="" <?= $status === "" ? "selected" : "" ?>>
            All Scholars
        </option>
    </select>

    <select name="barangay" id="barangayFilter">
        <option value="">All Barangays</option>
        <?php foreach ($barangays as $brgy): ?>
        <option value="<?= htmlspecialchars($brgy) ?>"
            <?= $barangay === $brgy ? 'selected' : '' ?>>
            <?= htmlspecialchars($brgy) ?>
        </option>
        <?php endforeach; ?>
    </select>

    <a href="?page=1"
   class="action-icon"
   title="Reset Filters">
    <i class="fa-solid fa-filter-circle-xmark"></i>
</a>
    </form>
<!-- end filter  -->

    <a href="admin_scholars_create.php" id="nav_addscholar"> &plus; Add Scholar</a>
</div>

<!-- scholar table -->
<div class="table-container">
    <table class="table">
        <thead>
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
                            <a href="/esko/admin/admin_scholars_view.php?semid=<?= urlencode($semId) ?>&scholarid=<?= urlencode($s['user_id']) ?>"
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
                                    title="<?= $s['is_active'] ? 'Deactivate User' : 'Reactivate User' ?>"
                                    data-action="<?= $s['is_active'] ? 'deactivate' : 'reactivate' ?>"
                                    data-name="<?= $s['last_name'] . ', ' . $s['first_name'] ?>"
                                    data-barangay="<?= $s['barangay'] ?>"
                                    data-iskolarno="<?= $s['username'] ?>">
                                    <i class="fa-solid <?= $s['is_active'] ? 'fa-user-slash' : 'fa-rotate-left' ?>"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>


<div class="pagination">
<?php
$query = http_build_query([
    'search' => $search,
    'status' => $status,
    'barangay' => $barangay
]);
?>

<?php if ($page > 1): ?>
    <a href="?page=<?= $page - 1 ?>&<?= $query ?>">Previous</a>
<?php endif; ?>

<?php for ($i = 1; $i <= $totalPages; $i++): ?>
    <a href="?page=<?= $i ?>&<?= $query ?>"
       class="<?= $i == $page ? 'active' : '' ?>">
        <?= $i ?>
    </a>
<?php endfor; ?>

<?php if ($page < $totalPages): ?>
    <a href="?page=<?= $page + 1 ?>&<?= $query ?>">Next</a>
<?php endif; ?>

</div>


<!-- REACTIVATE/ACTIVE MODAL -->
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
            <button id="cancelBtn">Cancel</button>
        </div>
    </div>

</div>

<script>
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

    document.getElementById('statusFilter').addEventListener('change', function () {
    document.getElementById('searchForm').submit();
});

document.getElementById('barangayFilter').addEventListener('change', function () {
    document.getElementById('searchForm').submit();
});
</script>

<?php include "../assets/layout_end.php"; ?>