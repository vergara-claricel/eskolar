<?php
require '../vendor/autoload.php';
// require '../classes/supa_semester.php';
// $config = require __DIR__ . "/../api/supabase.php";
use Dompdf\Dompdf;

// include "../classes/supa_scholar.php";
// include "../classes/supa_activities.php";
// include "../classes/supa_scorecard.php";

// $api = new Supabase($config);
// $schoobj = new Scholar($api);
// $sembobj = new Semester($api);
require_once "../test.php";
$scholarUserId = $_GET['scholarid'];
$activeSem = $semobj->getActiveSemester();
$semId = $activeSem['sem_id'];


$scholar_info = $schoobj->getScholarInfos($scholarUserId);

$scholar_ar = $schoobj->getScholarRecords($scholarUserId, $semId);


$html = "
<h2>Scholar Scorecard</h2>
<h3>{$scholar_info['first_name']} {$scholar_info['last_name']}</h3>
<p>Barangay: {$scholar_info['barangay']}</p>
<p>Semester: {$activeSem['semester_name']}</p>

<hr>
<h3>Activities Information</h3>
<table border='1' width='100%' cellpadding='5' cellspacing='0'>
<tr>
<th>Activity</th>
<th>Date</th>
<th>Classification</th>
<th>Officer Name</th>
</tr>
";

foreach ($scholar_ar as $s) {
    $html .= "
    <tr>
        <td>{$s['activityname']}</td>
        <td>{$s['date']}</td>
        <td>{$s['classification']}</td>
        <td>{$s['officer_fullname']}</td>
    </tr>
    ";
}

$html .= "</table>";

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("scholar_report.pdf", ["Attachment" => true]);
exit;