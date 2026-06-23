<?php
// include "../classes/supa_scholar.php";
// require_once "../classes/supabase.php";
// $config = require __DIR__ . "esko/../api/supabase.php";

// $api = new Supabase($config);
// $schoobj = new Scholar($api);
require_once "../test.php";
// include "../assets/layout.php";

$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? '';
$barangay = $_GET['barangay'] ?? '';

$scholars = $schoobj->filterScholars($search, $status, $barangay);

if(empty($scholars)){
    echo "
        <tr>
            <td>No scholars found. </td>
        </tr>
    ";
} else{
    foreach ($scholars as $s) {
$first = $s['first_name'] ?? '';
$last  = $s['last_name'] ?? '';
$name  = trim("$last, $first");

        echo "
        <tr>
            <td>{$s['username']}</td>
            <td>{$name}</td>
            <td>{$s['barangay']}</td>
            <td>
                <a href='admin_scholars_view.php?scholarid={$s['user_id']}'>View</a>
                <a href='admin_scholars_edit.php?scholarid={$s['user_id']}'>Edit</a>

                <form method='POST'
                    action='" . ($s['is_active']
                            ? "/esko/admin/admin_scholars_deactivate.php"
                            : "/esko/admin/admin_scholars_reactivate.php") . "'>

                    <input type='hidden'
                        name='scholarid'
                        value='{$s['user_id']}'>

                    <button type='button'
                            class='action-btn'
                            data-action='" . ($s['is_active']
                                ? "deactivate"
                                : "reactivate") . "'
                            data-name='{$name}'
                            data-barangay='{$s['barangay']}'
                            data-iskolarno='{$s['username']}'>
                        " . ($s['is_active']
                            ? "Delete"
                            : "Reactivate") . "
                    </button>

                </form>
            </td>
        </tr>
        ";
    }
}
?>