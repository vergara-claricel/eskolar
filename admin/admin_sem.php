<?php

$title = "Semester";
// include "../assets/layout.php";

// $config = require __DIR__ . "/../api/supabase.php";

// $api = new Supabase($config);

// $semobj = new Semester($api);

require_once "../test.php";


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
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_sem'])){
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
    } catch(Exception $e){
        echo "Error: " . $e->getMessage();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_sem_details'])){
    $sem_id = $_POST['sem_id'];
    $semester_name  = $_POST['semester_name'];
    $semester_start  = $_POST['semester_start'];
    $semester_end  = $_POST['semester_end'];

    try {
        $editsem = $semobj->updateSemDetails($sem_id,$semester_name, $semester_start, $semester_end);
         if ($editsem) {
            echo "Semester Edited!";
            header("location: /esko/admin/admin_sem.php");
            exit;
        } else {
            echo "Update failed.";
        }
    } catch(Exception $e){
        echo "Error: " . $e->getMessage();
    }
}
include "../assets/layout.php";
?>

<style>
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

</style>

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
                    <option value=""<?= empty($activeSem) ? 'selected' : '' ?>>NONE</option>
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
                    <tr>
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
                                <button
                                 class="action-icon edit-sem"
                                title="Edit Semester"
                                data-semid="<?= $row['sem_id'] ?>"
                                data-semname="<?= $row['semester_name'] ?>"
                                data-semstart="<?= $row['semester_start'] ?>"
                                data-semend="<?= $row['semester_end'] ?>"
                                data-semstatus="<?= $row['semester_status'] ?>">
                             <i class="fa-solid fa-pen-to-square"></i></a>
                            </button>
                                <button class="action-icon archive-sem"
                                data-semid="<?= $row['sem_id'] ?>"
                                data-semname="<?= $row['semester_name'] ?>"
                                title="Archive Semester Records">
                             <i class="fa-solid fa-box-archive"></i></a>
                            </button>
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

        </div>
    </div>
        </form>
</div>

<div id="archiveModal" class="modal">

    <!-- Modal content -->
    <div class="modal-content">
        <span class="close" id="closeArchiveModal">&times;</span>
        <p><strong>Action Confirmation</strong></p>
        <p>Are you sure you want to archive this semester?</p>
        <div class="modal-info">
            <form method="post">
                <input type="hidden" name="sem_id" id="toArchiveSemId">
            </form>
            <p><span id="toArchiveSemName"></span></p>
        </div>
        <p>Doing so would remove ALL related activity and attendance records and exported into an Excel file. </p>
        <p>Continue?</p>
        <div class="modal-buttons">
        <button id="archiveBtn">Confirm</button>
        <button id="cancelArchiveBtn">Cancel</button></div>
    </div>

</div>

<script>
    const select = document.getElementById('activesem');
    let originalValue = select.value;

    let currentForm;
    let modal = document.getElementById("editSemModal");
    let modalSemId = document.getElementById("modalSemId");
    let modalSemName = document.getElementById("modalSemName");
    let modalSemStart = document.getElementById("modalSemStart");
    let modalSemEnd = document.getElementById("modalSemEnd");
    let confirmBtn = document.getElementById("confirmBtn");
    let closeModal = document.getElementById("closeModal");
    let cancelBtn = document.getElementById("cancelEditSem");

    let archiveModal = document.getElementById("archiveModal");
    let closeArchiveModal = document.getElementById("closeArchiveModal");
    let cancelArchiveBtn = document.getElementById("cancelArchiveBtn");
    let archiveBtn = document.getElementById("archiveBtn");
    let toArchiveSemId = document.getElementById("toArchiveSemId");
    let toArchiveName = document.getElementById("toArchiveSemName");
    // store original value when page loads
    document.addEventListener('DOMContentLoaded', () => {
        originalValue = select.value;
    });

    document.getElementById('update_sem').addEventListener('click', function(e) {
        const selectedText = select.options[select.selectedIndex].text;

        const confirmMsg = `Change active semester to "${selectedText}"?`;

        if (!confirm(confirmMsg)) {
            e.preventDefault();

            // revert to original selection
            select.value = originalValue;
        }
    });

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
        } else if (archivebtn){
            toArchiveSemId.value = archivebtn.dataset.semid;
            toArchiveName.innerText = archivebtn.dataset.semname;

            currentForm = archivebtn.closest('form');
            archiveModal.style.display = 'flex';
        }
    });

    confirmBtn.addEventListener("click", function() {
        if (currentForm) {
            currentForm.submit();
        }
    });

    archiveBtn.addEventListener("click", function() {
        if (currentForm) {
            currentForm.submit();
        }
    });

    closeModal.onclick = function() {
        modal.style.display = "none";
    }

    closeArchiveModal.onclick = function() {
        archiveModal.style.display = "none";
    }

    cancelBtn.onclick = function() {
        modal.style.display = "none";
    }

    cancelArchiveBtn.onclick = function() {
        archiveModal.style.display = "none";
    }
</script>
<?php include "../assets/layout_end.php"; ?>