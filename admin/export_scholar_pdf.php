<?php
require '../vendor/autoload.php';
// require '../classes/supa_semester.php';
// $config = require __DIR__ . "/../api/supabase.php";
use Dompdf\Dompdf;

require_once "../test.php";
// $scholarUserId = $_GET['scholarid'];
$token = $_GET['token'] ?? '';
if (empty($token)) {
    exit('Invalid scorecard token.');
}
$scholarUserId = $schoobj->getScholarIdByToken($token);
if (empty($scholarUserId)) {
    exit('Scholar not found.');
}

$activeSem = $semobj->getActiveSemester();
$semId = $activeSem['sem_id'];
$qrPath = $schoobj->generateScorecardQr($scholarUserId);
if (!file_exists($qrPath)) {
    die("QR not found: " . $qrPath);
}
$logoLeft = __DIR__ . '/../assets/images/logo_iskolar.jpg';
$logoRight = __DIR__ . '/../assets/images/bagac_logo.png';
$scholar_info = $schoobj->getScholarInfos($scholarUserId);
$qrPath = __DIR__ . '/../qrcodes/qr_' . $scholar_info['username'] . '.svg';

if (!file_exists($qrPath)) {
    die("QR file not found at path: " . $qrPath);
}
$svgContent = file_get_contents($qrPath);
$qrBase64 = 'data:image/svg+xml;base64,' . base64_encode($svgContent);
$scholar_ar = $schoobj->getScholarRecords($scholarUserId, $semId);

if($scholar_ar !== null){
    $scholarAttended = $scObj->getAttendedEventsPerScholar($scholarUserId, $semId);
    $totalBrgyEvents = $scObj->getTotalBarangayEvents($scholar_info['barangay'], $semId);
    $totalMunicipalEvents = $scObj->getMunicipalEvents($semId);
    $totalEvents = $scObj->getTotalEvents($totalBrgyEvents, $totalMunicipalEvents);
    $quotaPercentage = $scObj->computeQuotaPercent($scholarAttended, $totalEvents);
    $passFail = $scObj->attendanceRemark($quotaPercentage);
    $repData = $offobj->getRepresentative($scholar_info['barangay']);
} else{
    echo "<alert>No attendance records to show.</alert>";
    exit;
}
    if ($repData !== null) {
     $repName = $repData['first_name'] . ' ' . $repData['last_name'];
} else {
    $repName = 'N/A';
}

$html = "

<style>

@page {
    margin: 35px 45px 40px 45px;
}

body {
    font-family: 'Times New Roman', serif;
    font-size: 12px;
    color: #000;
}


.header {
    width: 100%;
    text-align: center;
}

.header-table {
    width: 100%;
    border-collapse: collapse;
}

.header-table td {
    border: none;
    vertical-align: middle;
}


/* QR ABOVE EVERYTHING */

.qr-top-cell {
    text-align: right;
    vertical-align: bottom !important;
    padding-bottom: 5px;
}

.qr {
    width: 55px;
    height: 55px;
}

.qr-label {
    font-size: 7px;
    margin-top: 2px;
}


/* LOGOS */

.logo-cell {
    width: 20%;
    text-align: center;
}

.logo-left,
.logo-right {
    width: 80px;
    height: 80px;
}


/* CENTER TEXT */

.title-cell {
    width: 60%;
    text-align: center;
}

.header-title {
    font-size: 11px;
    font-weight: bold;
    line-height: 1.3;
}

.header-subtitle {
    font-size: 11px;
    line-height: 1.3;
}

.scorecard-title {
    font-size: 15px;
    font-weight: bold;
    margin-top: 8px;
}


.main-divider {
    border-bottom: 3px solid #000;
    margin-top: 8px;
    margin-bottom: 10px;
}

.section-title {
    font-size: 12px;
    font-weight: bold;
    margin-bottom: 8px;
}

.activity-title {
    text-align: center;
    font-size: 13px;
    font-weight: bold;
    margin-top: 16px;
    margin-bottom: 10px;
}

.info-table {
    width: 100%;
    border-collapse: collapse;
}

.info-table td {
    border: none;
    padding: 3px 5px;
    vertical-align: bottom;
}

.label {
    white-space: nowrap;
}

.line {
    border-bottom: 1px solid #555;
    font-weight: bold;
    padding-left: 8px;
}

.activity-table {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
}

.activity-table th {
    font-weight: bold;
    text-align: center;
    border: none;
    padding: 4px;
}

.activity-table td {
    text-align: center;
    border: none;
    padding: 8px 4px;
}

/* column widths */

.date {
    width: 17%;
}

.activity {
    width: 28%;
}

.classification {
    width: 27%;
}

.officer {
    width: 28%;
}

.subheader {
    font-size: 9px;
    font-style: italic;
    font-weight: normal;
}


.footer-divider {
    border-top: 3px solid #000;
    margin-top: 10px;
}

.footer-table {
    width: 100%;
    border-collapse: collapse;
}

.footer-table td {
    border: none;
    font-size: 10px;
    vertical-align: bottom;
}

.signature-line {
    border-bottom: 1px solid #555;
}

</style>


<!-- =========================
     HEADER
========================= -->

<table class='header-table'>

    <!-- QR ON TOP -->
    <tr>
        <td colspan='3' class='qr-top-cell'>
            <img src='$qrBase64' class='qr'>
            <div class='qr-label'>
                Scan to view record
            </div>
        </td>
    </tr>


    <!-- LOGOS + CENTER TEXT -->
    <tr>

        <!-- LEFT LOGO -->
        <td class='logo-cell'>
            <img src='$logoLeft' class='logo-left'>
        </td>


        <!-- CENTER HEADER -->
        <td class='title-cell'>

            <div class='header-title'>
                REPUBLIC OF THE PHILIPPINES
            </div>

            <div class='header-subtitle'>
                MUNICIPALITY OF BAGAC
            </div>

            <div class='header-subtitle'>
                ISKOLAR NG BAGAC
            </div>

            <div class='scorecard-title'>
                SCORECARD
            </div>

        </td>


        <!-- RIGHT LOGO -->
        <td class='logo-cell'>
            <img src='$logoRight' class='logo-right'>
        </td>

    </tr>

</table>


<!-- DIVIDER -->

<div class='main-divider'></div>

<div class='section-title'>
    SCHOLAR'S INFORMATION
</div>


<table class='info-table'>

<tr>

    <td class='label'>
        NAME:
    </td>

    <td class='line' width='43%'>
        {$scholar_info['last_name']}, {$scholar_info['first_name']}
    </td>

    <td class='label'>
        ISKOLAR NO.
    </td>

    <td class='line' width='23%'>
        {$scholar_info['username']}
    </td>

</tr>


<tr>

    <td class='label'>
        BARANGAY:
    </td>

    <td class='line'>
        {$scholar_info['barangay']}
    </td>

    <td class='label'>
        SEMESTER:
    </td>

    <td class='line'>
        {$activeSem['semester_name']}
    </td>

</tr>

</table>


<div class='activity-title'>
    ACTIVITY INFORMATION
</div>


<table class='activity-table'>

<tr>

    <th class='date'>
        DATE
        <div class='subheader'>
            MM/DD/YY
        </div>
    </th>

    <th class='activity'>
        ACTIVITY TITLE
    </th>

    <th class='classification'>
        CLASSIFICATION
        <div class='subheader'>
            Barangay / Municipal
        </div>
    </th>

    <th class='officer'>
        VERIFIED BY
    </th>

</tr>
";

foreach ($scholar_ar as $s) {

    $html .= "

    <tr>

        <td class='date'>
            {$s['activitydate']}
        </td>

        <td class='activity'>
            {$s['activityname']}
        </td>

        <td class='classification'>
            {$s['classification']}
        </td>

        <td class='officer'>
            {$s['officer_fullname']}
        </td>

    </tr>

    ";
}

$html .= "

</table>

<div class='footer-divider'></div>

<table class='footer-table'>

<tr>
    <td width='42%'>
        <b>REPRESENTATIVE'S NAME:  <span style='text-decoration: underline;'>$repName</span></b>
        
    </td>

    <td width='25%'>
        <b>REMARKS: $passFail</b>
        &nbsp;
    </td>

    <td width='33%'>
        <b>PRESIDENT'S SIGNATURE: </b>
        
        <img src='/assets/images/pinlac_sig.png' 
         alt='President Signature'
         style='width: 40px; height: auto; vertical-align: bottom; margin-bottom: -10px;'>
    </td>

</tr>

</table>

";

$dompdf = new Dompdf();

$options = $dompdf->getOptions();
$options->set('isRemoteEnabled', true);
$options->set('isHtml5ParserEnabled', true);
$options->set('chroot', realpath(__DIR__ . '/..'));

$dompdf->setOptions($options);

$dompdf->loadHtml($html);
$dompdf->setPaper([0, 0, 595, 500], 'portrait');
$dompdf->render();
$dompdf->stream("eScorecard.pdf", ["Attachment" => false]);
exit;
