<?php
// include "../classes/supa_activities.php";
// include "../classes/semester.php";
$title = "Dashboard";
require_once "../test.php";
include "../assets/layout.php";
// $config = require __DIR__ . "/../api/supabase.php";

// $actobj = new Activities($api);
$activesem = $semobj->getActiveSemester();
$activeSemId = $activesem['sem_id'];
$allacts = $actobj->getActivitiesOfActiveSem($activeSemId);

?>
<style>
    .scholar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        margin-inline-end: 80px;
    }

    /* TITLE */
    .scholar-header h2 {
        font-size: 22px;
        font-weight: 600;
        color: #222;
    }

    /* ADD BUTTON */
    #create_activitybtn {
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
</style>

<div class="scholar-header">
    <h2>Activities & Attendance</h2>
    <a href="/esko/admin/admin_activities_create.php" id="create_activitybtn">Create Activity</a>
</div>

<form method="GET" id="searchForm">
    <input type="text" name="search" id="searchActs" placeholder="Search…" value="<?= $_GET['search'] ?? '' ?>">
    <button type="submit">search</button>
</form>

<select name="status" id="statusFilter">
    <option value="">All Events</option>
    <option value="Upcoming">Upcoming</option>
    <option value="Active">Active</option>
    <option value="Done">Done</option>
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
                <th>Date</th>
                <th>Activity Name </th>
                <th>Status</th>
                <th>Classification</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody id="activitiesTable">
            <?php if (!empty($allacts)): ?>
                <?php foreach ($allacts as $s): ?>
                    <tr>
                        <td><?= $s['activitydate'] ?></td>
                        <td><?= $s['activityname'] ?></td>
                        <td><?= $s['status'] ?></td>
                        <td>
                            <?= $s['classification'] ?>
                            <?php if ($s['classification'] === 'Barangay' && !empty($s['barangay'])): ?>
                                - <?= $s['barangay'] ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="/esko/admin/admin_activities_view.php?actId=<?= $s['activity_id'] ?>">
                                View
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align:center; padding: 12px;">
                        No activities found.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
    // document.getElementById('searchActs').addEventListener('keyup', function() {
    //     let keyword = this.value;

    //     fetch('admin_activities_search.php?search=' + keyword)
    //     .then(res => res.text())
    //     .then(data => {
    //         document.getElementById('activitiesTable').innerHTML = data;
    //     });
    // });

    //         const searchInput = document.getElementById('searchActs');
    //         const form = document.getElementById('searchForm');

    //         form.addEventListener('submit', e => e.preventDefault());

    //         searchInput.addEventListener('keydown', e => {
    //             if (e.key === 'Enter') e.preventDefault();
    //         });

    let timer;

    const tableBody = document.getElementById('activitiesTable');
    const originalHTML = tableBody.innerHTML;

    function loadActivities() {
        // let search = document.getElementById('searchActs').value;
        // let status = document.getElementById('statusFilter').value;
        // let barangay = document.getElementById('barangayFilter').value;
        // console.log(barangay);
        // fetch(`admin_activities_search.php?search=${search}&status=${status}&barangay=${barangay}`)
        //     .then(res => res.text())
        //     .then(data => {
        //         document.getElementById('activitiesTable').innerHTML = data;
        //     });

        clearTimeout(timer);
        timer = setTimeout(() => {
            let search = document.getElementById('searchActs').value.trim();
            let status = document.getElementById('statusFilter').value;
            let barangay = document.getElementById('barangayFilter').value;
            if (search === "" && status == "" && barangay == "") {
                tableBody.innerHTML = originalHTML;
                return;
            }

            fetch(`admin_activities_search.php?search=${encodeURIComponent(search)}&status=${status}&barangay=${encodeURIComponent(barangay)}`)
                .then(res => res.text())
                .then(data => {
                    tableBody.innerHTML = data;
                });

        }, 300);
    }

    // live search
    document.getElementById('searchActs').addEventListener('input', loadActivities);
    document.getElementById('statusFilter').addEventListener('change', loadActivities);
    document.getElementById('barangayFilter').addEventListener('change', loadActivities);
</script>

<?php include "../assets/layout_end.php"; ?>