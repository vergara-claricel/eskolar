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

    function createActivity($name, $date, $timeinStart, $timeinEnd, $timeoutStart, $timeoutEnd, $venue, $classification, $barangay, $info, $semid)
    {
        $data = [
            'activityname' => $name,
            'activitydate' => $date,
            'timein_start' => $timeinStart,
            'timein_end' => $timeinEnd,
            'timeout_start' => $timeoutStart,
            'timeout_end' => $timeoutEnd,
            'venue' => $venue,
            'classification' => $classification,
            'barangay' => $barangay,
            'info' => $info,
            'semester' => $semid
        ];

        return $this->api->post("activities", $data);
    }

    function getActivitiesOfActiveSem($semid, $page, $limit)
    {
        $offset = ($page - 1) * $limit;

        return $this->api->get(
            "activities_with_status",
            "?semester=eq.$semid" .
            "&order=activitydate.desc" .
            "&limit=$limit" .
            "&offset=$offset"
        );
    }

    function countActivitiesOfActiveSem($semid)
{
    $result = $this->api->get(
        "activities_with_status",
        "?select=count&semester=eq.$semid"
    );

    return (int) ($result[0]['count'] ?? 0);
}
    function filterActivities(
        $search = '',
        $status = '',
        $barangay = '',
        $semid,
        $limit = null,
        $offset = null
    ) {
        $query = "activities_with_status?select=*&semester=eq.$semid";

        $filters = [];

        if ($search !== '') {
            $q = "*$search*";
            $filters[] = "activityname.ilike.$q";
        }

        if ($status !== '') {
            $filters[] = "computed_status=eq.$status";
        }

        if ($barangay !== '') {
            $brgy = urlencode($barangay);
            $filters[] = "barangay=eq.$brgy";
        }

        if ($filters) {
            $query .= "&" . implode("&", $filters);
        }

        // Pagination
        if ($limit !== null) {
            $query .= "&limit=$limit";
        }

        if ($offset !== null) {
            $query .= "&offset=$offset";
        }

        return $this->api->get($query);
    }


    function countFilteredActivities($search = '', $status = '', $barangay = '', $semid)
{
    $query = "activities_with_status?select=count&semester=eq.$semid";

    $filters = [];

    if ($search) {
        $q = "*$search*";
        $filters[] = "or=(activityname.ilike.$q,barangay.ilike.$q)";
    }

    if ($status !== '') {
        $filters[] = "computed_status=eq.$status";
    }

    if ($barangay) {
        $brgy = urlencode($barangay);
        $filters[] = "barangay=eq.$brgy";
    }

    if ($filters) {
        $query .= "&" . implode("&", $filters);
    }

    $result = $this->api->get($query);

    return (int) ($result[0]['count'] ?? 0);
}

    function getActivityDetails($actId)
    {

        $res = $this->api->get(
            "activities_with_status",
            "?activity_id=eq.$actId"
        );

        return $res[0] ?? null;
    }

    function updateActivity(
        $actId,
        $name,
        $classification,
        $barangay,
        $date,
        $timeinStart,
        $timeinEnd,
        $timeoutStart,
        $timeoutEnd,
        $venue,
        $info
    ) {

        $actId = (int)$actId;

        $data = [
            'activityname' => $name,
            'classification' => $classification,
            'barangay' => $barangay,
            'activitydate' => $date,
            'timein_start' => $timeinStart,
            'timein_end' => $timeinEnd,
            'timeout_start' => $timeoutStart,
            'timeout_end' => $timeoutEnd,
            'venue' => $venue,
            'info' => $info
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
            "activities_with_status",
            "?semester=eq.$semId&computed_status=eq.Upcoming"
        );
    }

    function getAttendanceRecords($actId)
    {
        return $this->api->get(
            "scholar_attendance_view?activityid=eq.$actId&is_active=eq.true&order=last_name.asc"
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

    // function getActivityAttendanceReport($actId, $classification, $barangay, $search = null)
    // {
    //     $scholars = $this->getEligibleScholars($classification, $barangay);
    //     $logs = $this->getAttendanceRecords($actId);

    //     $logMap = [];
    //     foreach ($logs as $log) {
    //         $logMap[$log['user_id']] = $log;
    //     }

    //     $result = [];

    //     // MERGE (no reference usage)
    //     foreach ($scholars as $s) {
    //         $log = $logMap[$s['user_id']] ?? null;

    //         $s['ar_id'] = $log['ar_id'] ?? null;
    //         $s['timein'] = $log['timein'] ?? null;
    //         $s['timeout'] = $log['timeout'] ?? null;
    //         $s['scanned_by'] = $log['scanned_by'] ?? null;
    //         $s['attendance_status'] = $log['attendance_status'] ?? 'absent';
    //         $s['officer_fullname'] = $log['officer_fullname'] ?? null;

    //         $result[] = $s;
    //     }

    //     if (!empty($search)) {
    //         $search = strtolower(trim($search));

    //         $result = array_filter($result, function ($s) use ($search) {
    //             return
    //                 str_contains(strtolower($s['username'] ?? ''), $search) ||
    //                 str_contains(strtolower($s['first_name'] ?? ''), $search) ||
    //                 str_contains(strtolower($s['last_name'] ?? ''), $search);
    //         });

    //         $result = array_values($result); 
    //     }

    //     return $result;
    // }

    function buildActivityAttendanceReport($actId, $classification, $barangay, $search = '')
    {
        $scholars = $this->getEligibleScholars($classification, $barangay);
        $logs = $this->getAttendanceRecords($actId);

        // Map attendance logs by user_id
        $logMap = [];
        foreach ($logs as $log) {
            $logMap[$log['user_id']] = $log;
        }

        $result = [];

        foreach ($scholars as $s) {

            $log = $logMap[$s['user_id']] ?? null;

            $s['ar_id'] = $log['ar_id'] ?? null;
            $s['timein'] = $log['timein'] ?? null;
            $s['timeout'] = $log['timeout'] ?? null;
            $s['scanned_by'] = $log['scanned_by'] ?? null;
            $s['attendance_status'] = $log['attendance_status'] ?? 'absent';
            $s['officer_fullname'] = $log['officer_fullname'] ?? null;

            $result[] = $s;
        }

        // Search
        if (!empty($search)) {

            $search = strtolower(trim($search));

            $result = array_values(array_filter($result, function ($s) use ($search) {

                return
                    str_contains(strtolower($s['username'] ?? ''), $search) ||
                    str_contains(strtolower($s['first_name'] ?? ''), $search) ||
                    str_contains(strtolower($s['last_name'] ?? ''), $search);
            }));
        }

        return $result;
    }

    function getActivityAttendanceReport(
        $actId,
        $classification,
        $barangay,
        $search = '',
        $page = 1,
        $limit = 10
    ) {

        $result = $this->buildActivityAttendanceReport(
            $actId,
            $classification,
            $barangay,
            $search
        );

        $offset = ($page - 1) * $limit;

        return array_slice($result, $offset, $limit);
    }

    function countActivityAttendanceReport(
        $actId,
        $classification,
        $barangay,
        $search = ''
    ) {

        return count(
            $this->buildActivityAttendanceReport(
                $actId,
                $classification,
                $barangay,
                $search
            )
        );
    }


    function upsertAttendance(
        $ar_id,
        $activity_id,
        $user_id,
        $date,
        $timein,
        $timeout,
        $scanned_by
    ) {
        $ar_id = (int)$ar_id;
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
    public function deleteActivity($activity_id)
    {
        return $this->api->delete(
            "activities",
            "?activity_id=eq.$activity_id"
        );
    }

    //     function searchAttendanceRecords($keyword, $actId)
    //     {
    //         $query = "attendance_view?select=*";

    //         $filters = [];

    //         if ($keyword) {
    //             $q = "*$keyword*";
    //             $filters[] = "or=(activityname.ilike.$q,barangay.ilike.$q)";
    //         }

    //         if ($barangay) {
    //             $brgy = urlencode($barangay);
    //             $filters[] = "barangay=eq.$brgy";
    //         }

    //         if ($filters) {
    //             $query .= "&" . implode("&", $filters);
    //         }

    //         return $this->api->get($query);
    //     }


}
