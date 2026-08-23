<?php
require_once "../classes/supabase.php";
$config = require __DIR__ . "esko/../api/supabase.php";

$api = new Supabase($config);
require_once "../vendor/autoload.php";

class Officer
{
    private $api;

    function __construct($api)
    {
        $this->api = $api;
    }

    function getOfficers()
    {
        return $this->api->get(
            "officer_view?select=*&is_active=eq.true&order=last_name.asc"
        );
    }

    function getOfficerInfo($officerId)
    {
        $data = $this->api->get(
            "officer_view?select=*&is_active=eq.true&user_id=eq.$officerId"
        );

        return $data[0] ?? null;
    }

    function updateOfficer(
        $userid,
        $firstname,
        $lastname,
        $barangay,
        $role,
        $phonenumber,
        $email,
        $username,
        $password = null
    ) {
        // 1. update users
        $this->api->patch(
            "users?id=eq.$userid",
            "",
            [
                "first_name" => $firstname,
                "last_name"  => $lastname,
                "username"   => $username,
                "role"   => $role
            ]
        );

        if (!empty($password)) {
            $this->api->patch(
                "users?id=eq.$userid",
                "",
                [
                    "password" => password_hash($password, PASSWORD_DEFAULT)
                ]
            );
        }

        // 2. update officers
        $this->api->patch(
            "officers?user_id=eq.$userid",
            "",
            [
                "barangay"    => $barangay,
                "phonenumber" => $phonenumber,
                "email"       => $email
            ]
        );

        return true;
    }

    function deactivateOfficer($officerId)
    {
        return $this->api->patch(
            "users?id=eq.$officerId",
            "",
            [
                "is_active" => false
            ]
        );
    }

    function createOfficer($firstname, $lastname, $brgy, $role, $phonenumber, $email, $officerno, $password)
    {
        $existing = $this->api->get(
            "users",
            "?username=eq." . urlencode($officerno) . "&select=id"
        );

        if (!empty($existing)) {
            return [
                "success" => false,
                "error"   => "duplicate_officerno"
            ];
        }

        $existingRep = $this->api->get(
            "users",
            "?role=eq.representative&is_active=eq.true&select=id"
        );

        if (!empty($existingRep)) {
            return [
                "success" => false,
                "error"   => "duplicate_representative"
            ];
        }

        $user = $this->api->post("users", [
            "first_name" => $firstname,
            "last_name"  => $lastname,
            "username"   => $officerno,
            "password"   => password_hash($password, PASSWORD_DEFAULT),
            "role"       => $role,
            "is_active"  => true
        ]);

        $userId = $user[0]['id'] ?? $user['id'] ?? null;

        if (!$userId) {
            return [
                "success" => false,
                "error"   => "user_creation_failed"
            ];
        }

        // 2. create officer profile
        $officer = $this->api->post("officers", [
            "user_id"     => $userId,
            "officerno"   => $officerno,
            "barangay"    => $brgy,
            "phonenumber" => $phonenumber,
            "email"       => $email
        ]);

        if (!$officer) {
            return [
                "success" => false,
                "error"   => "officer_creation_failed"
            ];
        }

        return [
            "success" => true,
            "user_id" => $userId
        ];
    }

    function getAdminName($adminId)
    {
        $data = $this->api->get(
            "users?select=first_name,last_name&is_active=eq.true&id=eq.$adminId"
        );

        if (empty($data)) {
            return null;
        }

        return $data[0]['first_name'] . ' ' . $data[0]['last_name'];
    }

    function getRepresentative($barangay)
    {
        $brgy = rawurlencode($barangay);
        $query = "officer_view?select=first_name,last_name&is_active=eq.true&role=eq.representative&barangay=eq.$brgy";
        $result = $this->api->get($query);

        if (empty($result)) {
            return null;
        }
        return $result[0];
    }
}
