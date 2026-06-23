<?php
require_once '../classes/supabase.php';
$config = require __DIR__ . "esko/../api/supabase.php";

$api = new Supabase($config);

class Scorecard
{
    private $api;

    function __construct($api)
    {
        $this->api = $api;
    }

    // 🔥 1. Attended events per scholar
    function getAttendedEventsPerScholar($scholarUserId, $semId)
    {
        $res = $this->api->get(
            "attendance_record?select=timein,timeout,activities(semester)&user_id=eq.$scholarUserId&activities.semester=eq.$semId"
        );

        if (!$res) return 0;

        $count = 0;

        foreach ($res as $r) {
            if (!empty($r['timein']) && !empty($r['timeout'])) {
                $count++;
            }
        }

        return $count;
    }

    // 🔥 2. Municipal events count
    function getMunicipalEvents($semId)
    {
        $res = $this->api->get(
            "activities?select=activity_id&classification=eq.Municipal&semester=eq.$semId"
        );

        return is_array($res) ? count($res) : 0;
    }

    // 🔥 3. Barangay events count
    function getTotalBarangayEvents($barangay, $semId)
    {
        $brgy = urlencode($barangay);

        $res = $this->api->get(
            "activities?select=activity_id&classification=eq.Barangay&barangay=eq.$brgy&semester=eq.$semId"
        );

        return is_array($res) ? count($res) : 0;
    }

    // 🔥 4. Total events
    function getTotalEvents($brgyEvents, $municipalEvents)
    {
        return $brgyEvents + $municipalEvents;
    }

    // 🔥 5. Percentage computation (same logic)
    function computeQuotaPercent($totalAttended, $totalEvents)
    {
        if ($totalEvents == 0) {
            return 0;
        }

        $percent = ($totalAttended / $totalEvents) * 100;
        return min($percent, 100);
    }

    // 🔥 6. Remark logic (fixed ordering)
    function attendanceRemark($totalPercentage)
    {
        if ($totalPercentage === null) {
            return '-';
        }

        return ($totalPercentage >= 80) ? 'PASSED' : 'FAILED';
    }
}