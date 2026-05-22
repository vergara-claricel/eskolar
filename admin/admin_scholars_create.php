<?php
$title = "Scholars";
include "../assets/layout.php";
require "../save_scholar.php";
?>
<style>
    #scholarform{
        padding: 15px;
        border-radius: 6px;
        display: flex;
        justify-content: center;
    }
    #scholarform button {
    grid-column: span 2;
    padding: 9px;
    background: #1e88e5;
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 13px;
    cursor: pointer;
}
#scholarform form {
    width: 100%;
    max-width: 500px;
    background: #fff;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);

    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px 12px;
}

.name-row {
    grid-column: 1 / -1;
    display: grid !important;
    grid-template-columns: 1fr 1fr !important;
    gap: 12px;
}

/* EACH FIELD INSIDE NAME ROW */
.name-row > div {
    display: flex;
    flex-direction: column;
    width: 100%;
}


/* LABEL FIX */
#scholarform label {
    font-size: 12px;
    margin-bottom: 3px;
}

</style>
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
        <label for="iskolarno">Iskolar No. </label>
        <input type="text" name="iskolarno" id="iskolarno">

        <label for="password">Password</label>
        <input type="password" name="password" id="password">


        <button type="submit" name="create_scholar">Add Scholar</button>

    </form>
</div>

<?php
include "../assets/layout_end.php";
?>