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
<div class="scholar-header">
    <h2>Scholars</h2>
    <a href="admin_scholars_create.php" id="nav_addscholar">Add Scholar</a>
</div>

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
                        <a href="/esko/admin/admin_scholars_view.php?semid=<?= urlencode($activeSem['sem_id']) ?>&scholarid=<?= urlencode($s['user_id']) ?>">View</a>
                        <a href="/esko/admin/admin_scholars_edit.php?scholarid=<?= urlencode($s['user_id']) ?>">Edit</a>
                        <form method="POST" action="/esko/admin/admin_scholars_deactivate.php" name="deleteForm">
                            <input type="hidden"
                                name="scholarid"
                                value="<?= $s['user_id'] ?>">
                            <button type="button"
                                class="action-btn"
                                data-name="<?= $s['last_name'] . ', ' . $s['first_name'] ?>"
                                data-barangay="<?= $s['barangay'] ?>"
                                data-iskolarno="<?= $s['username'] ?>">
                                Delete
                            </button>
                        </form>
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
        <p>Are you sure you want to <span id="modalAction"></span>this account?</p>
        <p><span id="modalIskoNo"></span></p>
        <p><span id="modalName"></span></p>
        <p><span id="modalBrgy"></span></p>
        <button id="confirmBtn">Confirm</button>
        <button id="cancelBtn">Cancel</button>
    </div>

</div>

<script>
    function loadScholars() {
        let search = document.getElementById('search').value;
        let status = document.getElementById('statusFilter').value;
        let barangay = document.getElementById('barangayFilter').value;
        console.log(barangay);
        fetch(`admin_scholars_filter.php?search=${search}&status=${status}&barangay=${barangay}`)
            .then(res => res.text())
            .then(data => {
                document.getElementById('scholarsTable').innerHTML = data;
            });
    }

    // live search
    document.getElementById('search').addEventListener('keyup', loadScholars);
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
        if (ev.target.classList.contains('action-btn')) {
            document.getElementById('modalAction').innerText =
                ev.target.dataset.action;

            modalName.innerText = ev.target.dataset.name;
            modalBrgy.innerText = ev.target.dataset.barangay;
            modalIskoNo.innerText = ev.target.dataset.iskolarno;

            currentForm = ev.target.closest('form');
            modal.style.display = 'block';
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