<?php

$title = "Semester";

require_once "../test.php";
$adminId = $_SESSION['userid'];
$activeSem = $semobj->getActiveSemester();
$activeSemId = $activeSem['sem_id'] ?? '';

// archive restore
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['archive_action'])) {
    $semid = $_POST['sem_id'];
    $action = $_POST['archive_action'];

    if ($action === 'archive') {
        $csv = $archiveobj->exportSemesterAttendanceCsv($semid);
        if ($csv) {
            $archiveobj->archiveSemesterRecords($semid);
            $_SESSION['success_message'] =
                'Semester records were successfully archived.';
        }
    } elseif ($action === 'restore') {
        $archiveobj->restoreSemesterRecords($semid);
        $_SESSION['success_message'] =
            'Semester records were successfully restored.';
    }

    header("Location: admin_sem.php");
    exit;
}

//download archived
if (isset($_GET['export'])) {
    $semid = (int) $_GET['export'];
    $archiveobj->exportSemesterAttendanceCsv($semid);
    exit;
}

// creating sem
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_sem'])) {
    $semester_name = $_POST['semester_name'];
    $semester_start = $_POST['semester_start'];
    $semester_end = $_POST['semester_end'];
    $semester_status = $_POST['semester_status'];

    try {
        $result = $semobj->saveSemester(
            $semester_name,
            $semester_start,
            $semester_end,
            $semester_status
        );
        header("location: /esko/admin/admin_sem.php");
        exit;
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

// change active sem
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_sem'])) {
    $sem_id = $_POST['activesem'];

    try {
        $newsem = $semobj->updateActiveSem($sem_id);
        if ($newsem) {
            echo "Active semester updated!";
            header("location: /esko/admin/admin_sem.php");
            exit;
        } else {
            echo "Update failed.";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

// update sem
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_sem_details'])) {
    $sem_id = $_POST['sem_id'];
    $semester_name  = $_POST['semester_name'];
    $semester_start  = $_POST['semester_start'];
    $semester_end  = $_POST['semester_end'];

    try {
        $editsem = $semobj->updateSemDetails($sem_id, $semester_name, $semester_start, $semester_end);
        if ($editsem) {
            echo "Semester Edited!";
            header("location: /esko/admin/admin_sem.php");
            exit;
        } else {
            echo "Update failed.";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
include "../assets/layout.php";
?>

<style>
    .success-archive-content {
        text-align: center;
        max-width: 400px;
    }

    .success-icon {
        font-size: 45px;
        margin-bottom: 15px;
    }

    #successMessage {
        margin-bottom: 20px;
    }

    .loading-content {
        text-align: center;
        padding: 30px;
    }

    .loader {
        width: 40px;
        height: 40px;
        margin: 0 auto 20px;
        border: 4px solid #ddd;
        border-top: 4px solid #333;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
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
        color: black;
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

    #archiveBtn,
    #cancelArchiveBtn,
    #confirmBtn,
    #cancelBtn,
    #cancelEditSem {
        border: none;
        padding: 9px 18px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
    }

    #confirmBtn {
        background: #FFC857;
        color: white;
    }

    #archiveBtn {
        background: #254725;
        color: white;
    }

    #archiveBtn:hover {
        background: rgb(36, 167, 13);
    }

    #confirmBtn:hover {
        background: #c28f2b;
    }

    #cancelBtn {
        background: #e0e0e0;
        color: #333;
    }

    #cancelArhiveBtn,
    #cancelBtn:hover {
        background: #cfcfcf;
    }

    .semester-tabs {
        display: flex;
        gap: 20px;
        margin-bottom: 15px;
        border-bottom: 1px solid #e2e8e2;
    }

    .semester-tab {
        background: none;
        border: none;
        padding: 8px 2px 10px;
        color: #718071;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        border-bottom: 2px solid transparent;
    }

    .semester-tab:hover {
        color: #4C7742;
    }

    .semester-tab.active {
        color: #254725;
        font-weight: 600;
        border-bottom-color: #4C7742;
    }

    /* =========================
   SEMESTER ACTIONS
========================= */

    .action-group {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    /* All action buttons / links */
    .action-group .action-icon,
    .action-group .download-btn {
        width: 34px;
        height: 34px;

        display: inline-flex;
        align-items: center;
        justify-content: center;

        padding: 0;

        border: none;
        border-radius: 6px;

        background: #f5f5f5;
        color: #555;

        text-decoration: none;

        cursor: pointer;

        transition: all 0.2s ease;
    }

    /* Icons */
    .action-group i {
        font-size: 14px;
    }

    /* Edit */
    .action-group .edit-sem:hover {
        background: #e8f1ff;
        color: #FFC857;
    }

    /* Archive / Restore */
    .action-group .archive-sem:hover {
        background: #fff4dc;
        color: #254725;
    }

    /* Download */
    .action-group .download-btn:hover {
        background: #e8f7ed;
        color: #28a745;
    }

    /* Optional: slight click effect */
    .action-group .action-icon:active,
    .action-group .download-btn:active {
        transform: scale(0.95);
    }
</style>

<?php if (isset($_SESSION['success_message'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showSuccessModal(
                'Success',
                <?= json_encode($_SESSION['success_message']) ?>
            );
        });
    </script>
<?php
    unset($_SESSION['success_message']);
endif;
?>

<div class="page-header">
    <h2>Semester</h2>
    <p>Manage semesters and set up active semester. </p>
</div>

<div class="sem-page">

    <!-- LEFT / MAIN AREA -->
    <div class="sem-left">

        <!-- SET ACTIVE SEM -->
        <div id="setsem">
            <h3>Set Active Semester</h3>
            <form method="POST">

                <?php $allsem = $semobj->getAllSemester(); ?>

                <select name="activesem" id="activesem">
                    <option value="" <?= empty($activeSem) ? 'selected' : '' ?>>NONE</option>
                    <?php foreach ($allsem as $sem): ?>
                        <option value="<?= $sem['sem_id'] ?>"
                            <?= ($sem['semester_status'] == 'active') ? 'selected' : '' ?>>
                            <?= $sem['semester_name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" name="update_sem" id="update_sem">Update</button>
            </form>
        </div>

        <!-- SEM LIST -->
        <div id="semlist">

            <div class="semester-tabs">
                <button type="button" class="semester-tab active" data-filter="active">
                    Existing
                </button>

                <button type="button" class="semester-tab" data-filter="archived">
                    Archived
                </button>
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Semester Name</th>
                        <th>Duration</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($allsem as $row): ?>
                        <tr class="semester-row"
                            data-archived="<?= $row['is_archived'] ? 'true' : 'false' ?>">
                            <td><?= $row['semester_name'] ?></td>
                            <td>
                                <?= (new DateTime($row['semester_start']))->format('M d, Y') ?>
                                -
                                <?= (new DateTime($row['semester_end']))->format('M d, Y') ?>
                            </td>
                            <td><?= $row['semester_status'] ?></td>
                            <td>
                                <!-- <button>Edit</button> -->
                                <div class="action-group">
                                    <?php if (!$row['is_archived']): ?>
                                        <button
                                            type="button"
                                            class="action-icon edit-sem"
                                            title="Edit Semester"
                                            data-semid="<?= $row['sem_id'] ?>"
                                            data-semname="<?= htmlspecialchars($row['semester_name']) ?>"
                                            data-semstart="<?= $row['semester_start'] ?>"
                                            data-semend="<?= $row['semester_end'] ?>"
                                            data-semstatus="<?= $row['semester_status'] ?>">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                    <?php endif; ?>
                                    <button type="button"
                                        class="action-icon archive-sem"
                                        data-semid="<?= $row['sem_id'] ?>"
                                        title="<?= $row['is_archived'] ? 'Restore Semester Records' : 'Archive Semester Records' ?>"
                                        data-action="<?= $row['is_archived'] ? 'restore' : 'archive' ?>"
                                        data-semname="<?= $row['semester_name'] ?>">
                                        <i class="fa-solid <?= $row['is_archived'] ? 'fa-rotate-left' : 'fa-box-archive' ?>"></i>
                                    </button>
                                    <?php if ($row['is_archived']): ?>
                                        <a href="admin_sem.php?export=<?= $row['sem_id'] ?>" class="download-btn">
                                            <i class="fa-solid fa-download"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>

                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- RIGHT SIDE / CREATE FORM -->
    <div class="sem-right">

        <div id="semform">
            <form method="POST" class="sem-form">

                <h4>Create New Semester</h4>

                <div class="semform-item">
                    <label>Semester Name</label>
                    <input type="text" name="semester_name" required>
                </div>

                <div class="semform-item">
                    <label>Start Date</label>
                    <input type="date" name="semester_start" required>
                </div>

                <div class="semform-item">
                    <label>End Date</label>
                    <input type="date" name="semester_end" required>
                </div>
                <button type="submit" name="create_sem">CREATE SEM</button>
            </form>
        </div>

    </div>
</div>

<div id="editSemModal" class="modal">
    <!-- Modal content -->
    <div class="modal-content">
        <span class="close" id="closeModal">&times;</span>
        <p><strong>Edit Semester</strong></p>
        <form method="POST">

            <input type="hidden" name="sem_id" id="modalSemId">
            <div class="semform-item">
                <label>Semester Name</label>
                <input id="modalSemName" type="text" name="semester_name" value>
            </div>

            <div class="semform-item">
                <label>Start Date</label>
                <input id="modalSemStart" type="date" name="semester_start" required>
            </div>

            <div class="semform-item">
                <label>End Date</label>
                <input id="modalSemEnd" type="date" name="semester_end" required>
            </div>

            <div class="modal-buttons">
                <button type="submit" name="update_sem_details" id="confirmBtn">Save Changes</button>
                <button type="button" id="cancelEditSem">Cancel</button>
            </div>
        </form>
    </div>
</div>


<div id="archiveModal" class="modal">
    <!-- Modal content -->
    <div class="modal-content">
        <span class="close" id="closeArchiveModal">&times;</span>
        <p><strong><span id="titleModalAction"></span> Confirmation</strong></p>
        <p>Are you sure you want to <span id="modalAction"></span> this semester?</p>
        <div class="modal-info">
            <form method="POST" id="archiveForm">
                <input type="hidden" name="sem_id" id="toArchiveSemId">
                <input type="hidden" name="archive_action" id="whatAction">
            </form>
            <p><span id="toArchiveSemName"></span></p>
        </div>
        <p>Doing so would export all related records for this semester into a CSV file. </p>
        <p>Continue?</p>
        <div class="modal-buttons">
            <button type="submit" id="archiveBtn">Confirm</button>
            <button type="button" id="cancelArchiveBtn">Cancel</button>
        </div>
    </div>
</div>

<!-- LOADING -->
<div id="loadingModal" class="modal">
    <div class="modal-content loading-content">
        <div class="loader"></div>
        <p><strong>Processing...</strong></p>
        <p id="loadingText">Exporting semester records. Please wait.</p>
    </div>
</div>

<!-- SUCCESS -->
<div id="successModal" class="modal">
    <div class="modal-content success-archive-content">
        <span class="close" id="closeSuccessModal">&times;</span>
        <div class="success-icon">
            <i class="fa-solid fa-circle-check"></i>
        </div>
        <p><strong id="successTitle">Success</strong></p>
        <p id="successMessage">Action completed successfully.</p>
        <div class="modal-buttons">
            <button type="button" id="successOkBtn">OK</button>
        </div>
    </div>
</div>


<script>
    const select = document.getElementById('activesem');
    let originalValue = select.value;

    let currentForm;

    // Edit modal
    const modal = document.getElementById("editSemModal");
    const modalSemId = document.getElementById("modalSemId");
    const modalSemName = document.getElementById("modalSemName");
    const modalSemStart = document.getElementById("modalSemStart");
    const modalSemEnd = document.getElementById("modalSemEnd");
    const confirmBtn = document.getElementById("confirmBtn");
    const closeModal = document.getElementById("closeModal");
    const cancelBtn = document.getElementById("cancelEditSem");

    // Archive modal
    const archiveModal = document.getElementById("archiveModal");
    const closeArchiveModal = document.getElementById("closeArchiveModal");
    const cancelArchiveBtn = document.getElementById("cancelArchiveBtn");
    const archiveBtn = document.getElementById("archiveBtn");
    const toArchiveSemId = document.getElementById("toArchiveSemId");
    const toArchiveName = document.getElementById("toArchiveSemName");

    // --------------------------------
    // ACTIVE SEMESTER
    // --------------------------------

    document.addEventListener('DOMContentLoaded', function() {
        originalValue = select.value;
    });

    document.getElementById('update_sem').addEventListener('click', function(e) {

        const selectedText =
            select.options[select.selectedIndex].text;

        const confirmMsg =
            `Change active semester to "${selectedText}"?`;

        if (!confirm(confirmMsg)) {
            e.preventDefault();

            // Restore original selection
            select.value = originalValue;
        }
    });


    // --------------------------------
    // OPEN EDIT / ARCHIVE MODALS
    // --------------------------------

    document.addEventListener('click', function(ev) {
        const btn = ev.target.closest('.edit-sem');
        const archivebtn = ev.target.closest('.archive-sem');
        if (btn) {

            modalSemId.value = btn.dataset.semid;
            modalSemName.value = btn.dataset.semname;
            modalSemStart.value = btn.dataset.semstart;
            modalSemEnd.value = btn.dataset.semend;

            currentForm = btn.closest('form');

            modal.style.display = 'flex';

        }

        // ARCHIVE / RESTORE
        else if (archivebtn) {

            const action = archivebtn.dataset.action;

            document.getElementById('modalAction').innerText =
                action;

            document.getElementById('titleModalAction').innerText =
                action === 'archive' ? 'Archive' : 'Restore';

            // Change title color
            document.getElementById('titleModalAction').style.color = action === 'archive' ? '#28a745' : 'black';
            document.getElementById('archiveBtn').style.backgroundColor = action === 'archive' ? '#28a745' : 'black';

            toArchiveSemId.value = archivebtn.dataset.semid;
            toArchiveName.innerText = archivebtn.dataset.semname;
            document.getElementById('whatAction').value = action;
            archiveBtn.innerText = action === 'archive' ? 'Archive' : 'Restore';

            archiveModal.style.display = 'flex';
        }
    });


    // --------------------------------
    // EDIT CONFIRM
    // --------------------------------

    confirmBtn.addEventListener("click", function() {

        if (currentForm) {
            currentForm.submit();
        }

    });


    // --------------------------------
    // ARCHIVE / RESTORE CONFIRM
    // --------------------------------

    archiveBtn.addEventListener("click", function(e) {
        e.preventDefault();
        const action =
            document.getElementById('whatAction').value;

        const loadingModal =
            document.getElementById('loadingModal');

        const archiveForm =
            document.getElementById('archiveForm');

        document.getElementById('loadingText').innerText =
            action === 'archive' ?
            'Archiving semester records. Please wait.' :
            'Restoring semester records. Please wait.';

        archiveModal.style.display = 'none';
        document.getElementById('loadingModal').style.display = 'flex';
        document.getElementById('archiveForm').submit();
    });


    closeModal.onclick = function() {
        modal.style.display = "none";
    };

    cancelBtn.onclick = function() {
        modal.style.display = "none";
    };

    closeArchiveModal.onclick = function() {
        archiveModal.style.display = "none";
    };

    cancelArchiveBtn.onclick = function() {
        archiveModal.style.display = "none";
    };

    document.getElementById('successOkBtn').onclick = function() {
        document.getElementById('successModal').style.display = "none";
    };

    function showSuccessModal(title, message) {

        // Hide loading modal
        document.getElementById('loadingModal').style.display = 'none';
        // Set success information
        document.getElementById('successTitle').innerText =
            title;
        document.getElementById('successMessage').innerText =
            message;
        // Show success modal
        document.getElementById('successModal').style.display =
            'flex';
    }

    function filterSemesters(filter) {
        document.querySelectorAll('.semester-row').forEach(row => {
            const archived =
                row.dataset.archived === 'true';
            if (filter === 'active') {
                row.style.display =
                    archived ? 'none' : '';
            } else if (filter === 'archived') {
                row.style.display =
                    archived ? '' : 'none';
            }
        });
    }

    document.querySelectorAll('.semester-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.semester-tab')
                .forEach(t => {
                    t.classList.remove('active');
                });
            this.classList.add('active');
            filterSemesters(
                this.dataset.filter
            );

        });

    });

    filterSemesters('active');
</script>
<?php include "../assets/layout_end.php"; ?>