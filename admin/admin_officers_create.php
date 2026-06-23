<?php
$title = "Officer";
include "../assets/layout.php";
include "../classes/supa_officers.php";

require_once "../classes/supabase.php";
$config = require __DIR__ . "esko/../api/supabase.php";

$api = new Supabase($config);

$offobj = new Officer($api);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $barangay = $_POST['barangay'];
    $phone = $_POST['phonenumber'];
    $email = $_POST['email'];
    $officerno = $_POST['officerno'];
    $password = $_POST['password'];

   try { 

        $result = $offobj->createOfficer($first_name, $last_name, $barangay, $phone, $email, $officerno, $password);
        echo $result ? " created successfully!" : "Failed to add.";
        header("location: /esko/admin/admin_officers.php");

   } catch(Exception $e) {
     //    $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }
    
}
?>
<style>
#scholarform {
    max-width: 480px; 
    margin: 12px auto;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 14px 20px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    font-family: system-ui, -apple-system, sans-serif;
}

h2 {
    max-width: 480px;
    margin: 0 auto 8px auto;
    font-size: 1.15rem;
    font-weight: 700;
    color: #0f172a;
}

/* Low-profile section headers */
.scholar-form h4 {
    font-size: 0.75rem;
    font-weight: 600;
    color: #1e88e5; 
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin: 12px 0 6px 0;
}

.scholar-form br { display: none; }

/* The Grid System: Forces fields side-by-side to save height */
.form-row-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    margin-bottom: 4px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 3px;
}

.scholar-form label {
    font-size: 0.75rem;
    font-weight: 500;
    color: #64748b;
}

/* Reduced height inputs */
.scholar-form input[type="text"],
.scholar-form input[type="email"],
.scholar-form input[type="password"],
.scholar-form input[type="number"],
.scholar-form select {
    width: 100%;
    padding: 6px 10px; /* Tight padding */
    font-size: 0.85rem;
    color: #1e293b;
    background-color: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 5px;
    box-sizing: border-box;
}

.scholar-form input:focus,
.scholar-form select:focus {
    outline: none;
    border-color: #2563eb;
    background-color: #ffffff;
}

.scholar-form input::placeholder {
    font-size: 0.75rem;
    color: #94a3b8;
}

/* Small, inline-feeling button */
.scholar-form button[type="submit"] {
    width: 100%;
    padding: 8px;
    margin-top: 14px;
    background-color: #0f172a;
    color: #ffffff;
    border: none;
    border-radius: 5px;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
}

.scholar-form button[type="submit"]:hover {
    background-color: #1e293b;
}

</style>

<h2>Create Officer Account</h2>
<div id="scholarform">
    <form method="POST" class="scholar-form">

    <h4>Personal Details</h4>
        <div class="name-row">
            <div>
                <label for="first_name">First Name</label>
                <input type="text" name="first_name" id="first_name">
            </div>

            <div>
                <label for="last_name">Last Name</label>
                <input type="text" name="last_name" id="last_name">
            </div>
        </div>

        <label for="barangay">Barangay</label>
        <select name="barangay">
            <option value="Atilano Ricardo">Atilano Ricardo</option>
            <option value="Bagumbayan">Bagumbayan</option>
            <option value="Banawang">Banawang</option>
            <option value="Binuangan">Binuangan</option>
            <option value="Binukawan">Binukawan</option>
            <option value="Ibis">Ibis</option>
            <option value="Ibaba">Ibaba</option>
            <option value="Pag-asa">Pag-asa</option>
            <option value="Parang">Parang</option>
            <option value="Paysawan">Paysawan</option>
            <option value="Quinawan">Quinawan</option>
            <option value="San Antonio">San Antonio</option>
            <option value="Saysain">Saysain</option>
            <option value="Tabing-Ilog">Tabing-Ilog</option>
        </select>

         <h4>Contact Details</h4>
         <br>
        <label for="phonenumber">Phone Number</label>
        <input type="number" name="phonenumber" id="phonenumber">

        <label for="email">Email Address</label>
        <input type="email" name="email" id="email">

         <h4>Account Details</h4>
         <br>
        <label for="officerno">Officer No. </label>
        <input type="text" name="officerno" id="officerno">

        <label for="password">Password</label>
        <input type="password" name="password" id="password">


        <button type="submit" name="create_officer">Create Officer</button>

    </form>
</div>

<?php
include "../assets/layout_end.php";
?>