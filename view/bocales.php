
<?php

global $wpdb;
$total_bocal = 0;
$index = 0;
$total_bocal_rendu = 0;
$table_name = $wpdb->prefix . "consigne";
$result = $wpdb->get_results("SELECT * FROM $table_name");

foreach ($result as $value) {
    $total_bocal += $value->bocal_quantite;
    $index++;
    $total_bocal_rendu += intval(get_option($value->client_id));
}


?>
<div class="wrap">
    <h1>ACCUEIL</h1>
    <div id="home_stats">
        <h2>
            SHT Bocale
        </h2>
        <hr />
        <p>Gestion de Bocale</p>
        <div class="row">

            <div class="col-md-4">
                <p style="padding-top: 15px;font-size: 1.5625rem;color: black" class="h2" /><?php echo $index ?></p>
                <p style="color: black">Clients</p>
            </div>
            <div class="col-md-4">
                <p style="padding-top: 15px;font-size: 1.5625rem;color: black" class="h2"><?php echo $total_bocal ?></p>
                <p style="color: black">Total Bocale</p>
            </div>
            <div class="col-md-4">
                <p style="padding-top: 15px;font-size: 1.5625rem;color: black" class="h2"><?php echo $total_bocal_rendu ?></p>
                <p style="color: black">Bocaux Rendus</p>
            </div>

        </div>
    </div>
</div>