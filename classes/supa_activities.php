<?php
require_once "../classes/supabase.php";
$config = require __DIR__ . "esko/../api/supabase.php";

$api = new Supabase($config);

class Activities
{
    private $api;

    function __construct($api)
    {
        $this->api = $api;
    }

    function createActivity($name, $status, $date, $start, $end, $venue, $classification, $barangay, $info, $semid)
    {
    $data = [
                'activityname' => $name,
                'status' => $status,
                'activitydate' => $date,
                'start_time' => $start,
                'end_time' => $end,
                'venue' => $venue,
                'classification' => $classification,
                'barangay' => $barangay,
                'info' => $info,
                'semester' => $semid
    ];

        return $this->api->post("activities", $data);
    }

    function getActivitiesOfActiveSem($semid)
    {
        return $this->api->get(
            "activities_current_sem_view",
            "?semester=eq.$semid&order=activitydate.desc"
        );
    }

    function filterActivities($search = '', $status = '', $barangay = '')
    {
        $query = "activities_current_sem_view?select=*";

        $filters = [];

        if ($search) {
            $q = "*$search*";
            $filters[] = "or=(activityname.ilike.$q,barangay.ilike.$q)";
        }

        if ($status !== '') {
            $filters[] = "status=eq.$status";
        }

        if ($barangay) {
            $brgy = urlencode($barangay);
            $filters[] = "barangay=eq.$brgy";
        }

        if ($filters) {
            $query .= "&" . implode("&", $filters);
        }

        return $this->api->get($query);
    }

    function getActivityDetails($actId)
    {

        $res = $this->api->get(
            "activities",
            "?activity_id=eq.$actId"
        );

        return $res[0] ?? null;
    }

    function updateActivity($actId, $name,
            $classification,
            $barangay,
            $date,
            $start_time,
            $end_time,
            $venue,
            $info)
    {

        $actId = (int)$actId;

        $data = [
            'activityname' =>$name,
            'classification' =>$classification,
            'barangay' =>$barangay,
            'activitydate' =>$date,
            'start_time' =>$start_time,
            'end_time' =>$end_time,
            'venue' =>$venue,
            'info' =>$info
        ];

    
        return $this->api->patch(
            "activities?activity_id=eq.$actId",
            "",
            $data
        );
    }
    function upcomingActivities($semId)
    {

        return $this->api->get(
            "activities",
            "?semester=eq.$semId&status=eq.Upcoming"
        );
    }

        function getAttendanceRecords($actId)
    {
        return $this->api->get(
            "attendance_view?activityid=eq.$actId"
        );
    }

    function getEligibleScholars($classification, $barangay = null)
{
    $query = "scholar_view?select=*&is_active=eq.true&order=last_name.asc";

    if ($classification === 'Barangay' && $barangay) {
        $query .= "&barangay=eq." . rawurlencode($barangay);
    }

    return $this->api->get($query);
}

    // function updateAttendance($ar_id,
    //         $activity_id,
    //         $user_id,
    //         $date,
    //         $timein,
    //         $timeout,
    //         $scanned_by)
    // {

    //     $ar_id = (int)$ar_id;

    //     $data = [
    //              'activityid' => $activity_id,
    //             'user_id' => $user_id,
    //             'date' => $date,
    //             'timein' => $timein,
    //             'timeout' => $timeout,
    //             'scanned_by' => $scanned_by,
    //     ];

    
    //     return $this->api->patch(
    //         "attendance_record?ar_id=eq.$ar_id",
    //         $data
    //     );
    // }


//     function updateAttendance($ar_id, $activity_id, $user_id, $date, $timein, $timeout, $scanned_by)
// {
//     $ar_id = (int)$ar_id;

//     $data = [
//         "activityid" => $activity_id,
//         "user_id" => $user_id,
//         "date" => $date,
//         "timein" => $timein ?: null,
//         "timeout" => $timeout ?: null,
//         "scanned_by" => $scanned_by
//     ];

//     return $this->api->patch(
//     "attendance_record",
//     "?ar_id=eq.$ar_id",
//     $data
// );
// }

function upsertAttendance(
    $ar_id,
    $activity_id,
    $user_id,
    $date,
    $timein,
    $timeout,
    $scanned_by
) {
    // 1. Check if record exists
    $check = $this->api->get(
        "attendance_record?select=ar_id&ar_id=eq.$ar_id"
    );

    $data = [
        "activityid" => $activity_id,
        "user_id" => $user_id,
        "date" => $date,
        "timein" => $timein,
        "timeout" => $timeout,
        "scanned_by" => $scanned_by
    ];

    // 2. UPDATE if exists
    if (!empty($check)) {

        return $this->api->patch(
            "attendance_record",
            "?ar_id=eq.$ar_id",
            $data
        );
    }

    // 3. INSERT if NOT exists
    return $this->api->post(
        "attendance_record",
        $data
    );
}

    function getActivityAttendanceReport($actId,
    $classification,
    $barangay){
    $scholars = $this->getEligibleScholars($classification, $barangay);
    $logs = $this->getAttendanceRecords($actId);

    $logMap = [];
    foreach ($logs as $log) {
        $logMap[$log['user_id']] = $log;
    }

    // merge
    foreach ($scholars as &$s) {
        $log = $logMap[$s['user_id']] ?? null;

        $s['ar_id'] = $log['ar_id'] ?? null;
        $s['iskolarno'] = $log['username'] ?? null;
        $s['timein'] = $log['timein'] ?? null;
        $s['timeout'] = $log['timeout'] ?? null;
        $s['scanned_by'] = $log['scanned_by'] ?? null;
        $s['attendance_status'] = $log['attendance_status'] ?? 'absent';
        $s['officer_fullname'] = $log['officer_fullname'] ?? null;
    }

    return $scholars;
    }

}

