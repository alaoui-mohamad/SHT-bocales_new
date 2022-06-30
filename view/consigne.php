<div class="wrap">
    <h1>GESTION DES BOCAUX</h1>

    <?php

    require plugin_dir_path(__DIR__) . 'controller/rest_api.php';
    require plugin_dir_path(__DIR__) . 'controller/code.php';
  
    
    global $wpdb;

    $table_name_bocales = $wpdb->prefix . "bocales";
    $id_attrubi = wc_attribute_taxonomy_id_by_name('pa_bocale');
    $terms = $woocommerce->get('products/attributes/' . $id_attrubi . '/terms');
   
    
    $terms_id = array();
    if (isset($_POST['submitt'])) {

		echo "<meta http-equiv='refresh' content='0'>";

	}
    foreach ($terms as $term) {

        $terms_id[] = $term->id;
    }

    foreach ($terms as $term) {
        $check = $wpdb->query("SELECT `bocal_id` FROM  $table_name_bocales WHERE `bocal_id` = $term->id ");

        if ($check == 0) {

            $wpdb->insert(

                $table_name_bocales,
                array(
                    'bocal_id' => 'hello',
                    'bocal_name' => 'hello',
                )
            );
        }
    }


    $result = $wpdb->get_results("SELECT * FROM  $table_name_bocales");
    foreach ($result as $item) {
        if (!in_array($item->bocal_id, $terms_id)) {

            $deletecolumn = $wpdb->query("DELETE FROM $table_name_bocales WHERE `bocal_id` = $item->bocal_id");
        }
    }

    ?>


    <form method="post" action="">



        <table class="cpt-table">

            <tr>
                <th>ID de bocale</th>
                <th>Nom de bocale</th>
                <th>Prix de bocale</th>
                <th>Modefier prix</th>
            </tr>
            <tr>

                <?php
                foreach ($result as $item) {


                ?>
            <tr>

                <td>
                    <strong><?php echo  $item->bocal_id ?></strong>
                </td>
                <td>
                    <strong><?php echo $item->bocal_name ?></strong>
                </td>
                <td>
                    <strong><?php echo  $item->bocal_price ?> Fr</strong>
                </td>

                <td class="input text-center"><span> </span><input type="number" name="<?php echo  $item->bocal_id ?>" value="<?php echo  $item->bocal_price ?>"></td>

                <?php
                    
                    if(isset($_POST[$item->bocal_id])){
                        $wpdb->update(
                            $table_name_bocales,
                            array('bocal_price' => $_POST[$item->bocal_id]),
                            array('id' => $item->id)
                        );
                    }
                    
                    

                   



                ?>

            </tr>
        <?php
                }


        ?>
        </table>
        <?php
        echo '<div style="margin-top: 10px"></div>';
        submit_button('Save Changes', 'primary', 'submitt', false);
        ?>

    </form>

</div>