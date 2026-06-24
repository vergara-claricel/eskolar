<?php

require_once "../classes/supabase.php";
$config = require __DIR__ . "esko/../api/supabase.php";

$api = new Supabase($config);
require_once "../vendor/autoload.php";

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use chillerlan\QRCode\Common\EccLevel;

class Scholar
{
    private $api;

    function __construct($api)
    {
        $this->api = $api;
    }

    // function generateScholarQR($userId, $iskolarno)
    // {
    //     $folder = "../qrcodes/";
    //     if (!file_exists($folder)) {
    //         mkdir($folder, 0777, true);
    //     }

    //     $qrToken = $iskolarno . "_" . bin2hex(random_bytes(16));

    //     $qrData = json_encode([
    //         "iskolarno" => $iskolarno,
    //         "token" => $qrToken
    //     ]);

    //     $fileName = "qr_" . $iskolarno . ".png";
    //     $filePath = $folder . $fileName;

    //     $options = new QROptions([
    //         'version' => 5,
    //         'scale' => 6,
    //         'eccLevel' => EccLevel::L,
    //     ]);

    //     $qrcode = new QRCode($options);
    //     file_put_contents($filePath, $qrcode->render($qrData));

    //     $this->api->patch(
    //         "scholars?user_id=eq.$userId",
    //         [],
    //         [
    //             "qr_path" => $fileName,
    //             "qr_token" => $qrToken
    //         ]
    //     );

    //     return $filePath;
    // }

    function generateScholarToken($userId, $iskolarno)
    {
        $qrToken = $iskolarno . "_" . bin2hex(random_bytes(16));

        $this->api->patch(
            "scholars",
            "?user_id=eq." . (int)$userId,
            [
                "qrtoken" => $qrToken
            ]
        );

        return $qrToken;
    }

    function addScholar($firstname, $lastname, $brgy, $phonenumber, $email, $iskolarno, $password)
    {
        // 1. create user
        $user = $this->api->post("users", [
            "first_name" => $firstname,
            "last_name"  => $lastname,
            "username"   => $iskolarno,
            "password"   => password_hash($password, PASSWORD_DEFAULT),
            "role"       => "scholar"
        ]);

        var_dump($user);

        $userId = $user['id'] ??  $user[0]['id'] ?? $user['data']['id'] ?? null;


        if (!$userId) {
            die("User ID not found in response");
        }

        $this->api->post("scholars", [
            "user_id"     => (int)$userId,
            "barangay"    => $brgy,
            "phonenumber" => $phonenumber,
            "username"    => $iskolarno,
            "email"       => $email
        ]);

        // 3. generate QR
        $this->generateScholarToken($userId, $iskolarno);

        return $userId;
    }

    function getAllActiveScholars()
    {
        $data = $this->api->get(
            "scholar_view?select=*&order=last_name.asc"
        );

        return array_values(array_filter($data, function ($row) {
            return isset($row['is_active']) && $row['is_active'] === true;
        }));
    }

    function searchScholars($keyword)
    {
        $q = "%$keyword%";

        return $this->api->get(
            "scholars?select=**&order=last_name.asc,users(id,first_name,last_name,username)&or=(" .
                "users.first_name.ilike.$q," .
                "users.last_name.ilike.$q," .
                "username.ilike.$q," .
                "barangay.ilike.$q)"

        );
    }

    function getScholarInfo($user_id)
    {
        return $this->api->get(
            "scholars?select=*,users(*)&user_id=eq.$user_id"
        )[0] ?? null;
    }

    
    function getScholarInfos($user_id)
    {
        return $this->api->get(
            "scholar_view?select=*&user_id=eq.$user_id"
        )[0] ?? null;
    }



    function getScholarRecord($user_id, $semId)
    {
        return $this->api->get(
            "attendance_record?select=*,"
                . "activities!inner(*),"
                . "scholar:users!fk_attendance_user(first_name,last_name),"
                . "scanner:users!fk_scanned_by_user(first_name,last_name)"
                . "&user_id=eq.$user_id"
                . "&activities.semester=eq.$semId"
        );
    }

    function getScholarRecords($user_id, $semId){
        return $this->api->get(
            "attendance_view_logs?select=*"
                . "&user_id=eq.$user_id"
                . "&semester=eq.$semId&attendance_status=eq.present"
        );
    }


    function updateScholar($user_id, $first_name, $last_name, $barangay, $phonenumber, $email, $iskolarno, $password = null)
    {
        $res1 = $this->api->patch(
            "scholars?user_id=eq.$user_id",
            "",
            [
                "barangay" => $barangay,
                "phonenumber" => $phonenumber,
                "email" => $email,
                "username" => $iskolarno
            ]
        );

        $res2 = $this->api->patch(
            "users?id=eq.$user_id",
            "",
            [
                "first_name" => $first_name,
                "last_name" => $last_name,
                "username" => $iskolarno
            ]
        );

        if (!empty($password)) {
            $res3 = $this->api->patch(
                "users?id=eq.$user_id",
                "",
                [
                    "password" => password_hash($password, PASSWORD_DEFAULT)
                ]
            );
        }

        return [
            "scholars" => $res1,
            "users" => $res2
        ];
    }

    function deactivateScholar($id)
    {
        return $this->api->patch(
            "users?id=eq.$id",
            "",
            ["is_active" => 0]
        );
    }

    function reactivateScholar($id)
    {
        return $this->api->patch(
            "users?id=eq.$id",
            "",
            ["is_active" => 1]
        );
    }

    function filterScholars($search = '', $status = '', $barangay = '')
    {
        $query = "scholar_view?select=*";

        $filters = [];

        if ($search) {
            $q = "*$search*";
            $filters[] = "or=(username.ilike.$q,first_name.ilike.$q,last_name.ilike.$q)";
        }

        if ($status !== '') {
            $filters[] = "is_active=eq.$status";
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

}

