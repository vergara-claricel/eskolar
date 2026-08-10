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

<style>

@page {
    margin: 35px 45px 40px 45px;
}

body {
    font-family: 'Times New Roman', serif;
    font-size: 12px;
    color: #000;
}

/* =========================
   HEADER
   ========================= */

.header {
    width: 100%;
    text-align: center;
}

.header-title {
    font-size: 12px;
    font-weight: bold;
    line-height: 1.3;
}

.header-subtitle {
    font-size: 12px;
    line-height: 1.3;
}

.scorecard-title {
    font-size: 18px;
    font-weight: bold;
    margin-top: 10px;
}

/* Header logos */

.logo-left {
    width: 75px;
    height: 75px;
}

.logo-right {
    width: 75px;
    height: 75px;
}

.qr {
    width: 65px;
    height: 65px;
}

/* =========================
   HEADER TABLE
   ========================= */

.header-table {
    width: 100%;
    border-collapse: collapse;
}

.header-table td {
    border: none;
    vertical-align: middle;
}

.logo-cell {
    width: 20%;
    text-align: center;
}

.title-cell {
    width: 60%;
    text-align: center;
}

.qr-cell {
    width: 20%;
    text-align: center;
}

/* =========================
   DIVIDER
   ========================= */

.main-divider {
    border-bottom: 3px solid #000;
    margin-top: 8px;
    margin-bottom: 10px;
}

/* =========================
   SECTION TITLES
   ========================= */

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

/* =========================
   SCHOLAR INFORMATION
   ========================= */

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

/* =========================
   ACTIVITY TABLE
   ========================= */

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

/* =========================
   FOOTER
   ========================= */

.footer-divider {
    border-top: 3px solid #000;
    margin-top: 15px;
    margin-bottom: 10px;
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

<tr>

    <!-- LEFT LOGO -->
    <td class='logo-cell'>
        <img src='LOGO_LEFT' class='logo-left'>
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


    <!-- RIGHT LOGO + QR -->
    <td class='qr-cell'>

        <table width='100%'>
            <tr>
                <td align='center'>
                    <img src='LOGO_RIGHT' class='logo-right'>
                </td>
                <td align='center'>
                    <img src='QR_CODE' class='qr'>
                </td>
            </tr>

            <tr>
                <td colspan='2'
                    style='font-size:8px; text-align:center;'>
                    Scan to view record
                </td>
            </tr>
        </table>

    </td>

</tr>

</table>


<!-- DIVIDER -->

<div class='main-divider'></div>


<!-- =========================
     SCHOLAR INFORMATION
     ========================= -->

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


<!-- =========================
     ACTIVITY INFORMATION
     ========================= -->

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
            {$s['date']}
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


<!-- =========================
     FOOTER
     ========================= -->

<div class='footer-divider'></div>

<table class='footer-table'>

<tr>

    <td width='42%'>
        <b>REPRESENTATIVE'S NAME:</b>
        <span class='signature-line'>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        </span>
    </td>

    <td width='25%'>
        <b>REMARKS:</b>
        &nbsp; PASSED
    </td>

    <td width='33%'>
        <b>PRESIDENT'S SIGNATURE:</b>
        <span class='signature-line'>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        </span>
    </td>

</tr>

</table>

";

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("scholar_report.pdf", ["Attachment" => true]);
exit;