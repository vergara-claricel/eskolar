<?php
require_once '../classes/supabase.php';
$config = require __DIR__ . "esko/../api/supabase.php";

$api = new Supabase($config);

class Semester {
    private $api;

    function __construct($api) {
        $this->api = $api;
    }

    function getAllSemester() {
    return $this->api->get("semesters", "?select=*");
    }

    function getActiveSemester() {

    $result = $this->api->get(
        "semesters",
        "?semester_status=eq.active&limit=1"
    );
        return $result[0] ?? null;
    }

    function saveSemester($semname, $semstart, $semend, $semstatus) {

    if ($semstatus === "active") {
        // set all inactive first
        $this->api->patch(
           "semesters",
        "?semester_status=eq.active",
        ["semester_status" => "inactive"]
        );
    }

    $data = [
        "semester_name" => $semname,
        "semester_start" => $semstart,
        "semester_end" => $semend,
        "semester_status" => $semstatus
    ];

    $result = $this->api->post("semesters", $data);

    print_r($result);
    if (!$result) {
    error_log("Failed to insert semester");
}   
    return !empty($result);
}


function updateActiveSem($sem_id) {

    // set all inactive
    $this->api->patch(
        "semesters",
        "?semester_status=eq.active",
        ["semester_status" => "inactive"]
    );
    // set selected active
    $this->api->patch(
    "semesters",
    "?sem_id=eq.$sem_id",
    ["semester_status" => "active"]
);
    return true;
    }
}
$semobj = new Semester($api);


?>