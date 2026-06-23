<?php
include "../classes/supa_scholar.php";
require_once "../classes/supabase.php";
$config = require __DIR__ . "esko/../api/supabase.php";

$api = new Supabase($config);
include "../classes/supa_scholar.php";
$schoobj = new Scholar($api);

$keyword = $_GET['search'] ?? '';
$allscho = $schoobj->searchScholars($keyword);


    foreach ($allscho as $s) {
    echo "
        <tr>
            <td>{$s['username']}</td>
            <td>{$s['last_name']}, {$s['first_name']}</td>
            <td>{$s['barangay']}</td>
            <td>
                <a href='/esko/admin/admin_scholars_view.php?scholarid={$s['user_id']}'>View</a>
                <a href='/esko/admin/admin_scholars_edit.php?scholarid={$s['user_id']}'>Edit</a>
            </td>
        </tr>
    ";

    }


?>