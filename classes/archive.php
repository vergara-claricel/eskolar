<?php

require_once '../classes/supabase.php';
$config = require __DIR__ . "esko/../api/supabase.php";
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

$api = new Supabase($config);
class Archive
{
    private $scholar;
    private $activities;
    private $api;

    public function __construct($scholar, $activities, $api)
    {
        $this->scholar = $scholar;
        $this->activities = $activities;
        $this->api = $api;
    }

    function getSemesterAttendanceArchive($semId)
    {
        // Get all scholars for the semester
        $scholars = $this->scholar->getAllScholarsForArchive();

        $archive = [];

        foreach ($scholars as $scholar) {

            $scholarId = $scholar['user_id'];

            // Get this scholar's activity records from Supabase RPC
            $records = $this->api->post(
                "rpc/get_activities_attendance_scholar",
                [
                    "p_user_id" => $scholarId,
                    "p_semester" => $semId
                ]
            );

            $row = [
                'user_id' => $scholarId,
                'username' => $scholar['username'],
                'name' => $scholar['last_name'] . ', ' . $scholar['first_name'],
                'activities' => [],
                'attended' => 0,
                'total_activities' => count($records),
                'quota' => 0,
                'remarks' => ''
            ];

            foreach ($records as $record) {

                $activityId = $record['activity_id'];

                $row['activities'][$activityId] = [
                    'activityname' => $record['activityname'],
                    'status' => $record['attendance_status'],
                    'timein' => $record['timein'],
                    'timeout' => $record['timeout']
                ];

                if ($record['attendance_status'] === 'present') {
                    $row['attended']++;
                }
            }

            // Calculate quota
            if ($row['total_activities'] > 0) {
                $row['quota'] =
                    ($row['attended'] / $row['total_activities']) * 100;
            }

            // Remarks
            $row['remarks'] =
                $row['quota'] >= 75
                ? 'PASSED'
                : 'FAILED';

            $archive[] = $row;
        }

        // print_r($archive);
        // exit;
        return $archive;
    }

public function exportSemesterAttendanceCsv($semId)
{
    $archive = $this->getSemesterAttendanceArchive($semId);

    if (empty($archive)) {
        return false;
    }

    // Get unique activities
    $activities = [];

    foreach ($archive as $scholar) {
        foreach ($scholar['activities'] as $activityId => $activity) {
            if (!isset($activities[$activityId])) {
                $activities[$activityId] = $activity['activityname'];
            }
        }
    }

    $fileName = "attendance_archive_semester_{$semId}.csv";

    header('Content-Type: text/csv; charset=utf-8');
    header("Content-Disposition: attachment; filename=\"$fileName\"");
    header('Cache-Control: max-age=0');

    $output = fopen('php://output', 'w');

    // Header
    $headers = ['Scholar Name'];

    foreach ($activities as $activityName) {
        $headers[] = $activityName;
    }

    $headers[] = 'Activities Attended';
    $headers[] = 'Quota %';
    $headers[] = 'Remarks';

    fputcsv($output, $headers);

    // Scholar rows
    foreach ($archive as $scholar) {

        $row = [];

        $row[] = $scholar['name'];

        foreach ($activities as $activityId => $activityName) {

            $status =
                $scholar['activities'][$activityId]['status']
                ?? 'absent';

            $row[] = ($status === 'present')
                ? 'Attended'
                : 'Not Attended';
        }

        $row[] = $scholar['attended'];

        // Your quota is currently stored as 0–100
        $row[] = $scholar['quota'] . '%';

        $row[] = $scholar['remarks'];

        fputcsv($output, $row);
    }

    fclose($output);
    exit;
}
}
