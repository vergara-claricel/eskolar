<?php

class Supabase
{
    private $url;
    private $key;

    function __construct($config)
    {
        $this->url = $config["url"];
        $this->key = $config["key"];
    }

    function get($table, $query = "")
    {
        $ch = curl_init("{$this->url}/rest/v1/{$table}{$query}");

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "apikey: {$this->key}",
            "Authorization: Bearer {$this->key}"
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    function post($table, $data)
    {
        $ch = curl_init("{$this->url}/rest/v1/{$table}");

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "apikey: {$this->key}",
            "Authorization: Bearer {$this->key}",
            "Content-Type: application/json",
            "Prefer: return=representation"
        ]);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (curl_error($ch)) {
            echo "CURL ERROR: " . curl_error($ch);
        }

        curl_close($ch);

        echo "HTTP CODE: " . $httpCode;
        echo "<pre>";
        echo $response;
        echo "</pre>";


        return json_decode($response, true);
    }

    function patch($table, $query, $data)
    {
        $ch = curl_init("{$this->url}/rest/v1/{$table}{$query}");

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "apikey: {$this->key}",
            "Authorization: Bearer {$this->key}",
            "Content-Type: application/json",
            "Prefer: return=representation"
        ]);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }
}
