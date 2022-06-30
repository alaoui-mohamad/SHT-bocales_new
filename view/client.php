<div class="wrap">
	<h1>GESTION DES BOCAUX</h1>
	<?php
	
require plugin_dir_path(__DIR__) . 'controller/rest_api.php';
require plugin_dir_path(__DIR__) . 'controller/code.php';




global $wpdb;
$table_name = $wpdb->prefix . "consigne";
$table_name_bocales = $wpdb->prefix . "bocales";
$result = $wpdb->get_results('SELECT * FROM ' . $table_name);
$bocale_result = $wpdb->get_results("SELECT * FROM  $table_name_bocales");

	define('PLUGIN_ROOT_DIR', plugin_dir_path(__DIR__));

	add_option('id_user');


	$table_bocal_order = $wpdb->prefix . "bocal_order";



	if (isset($_POST["edit_post"])) {
		update_option('id_user', $_POST["edit_post"]);
		$post = $_POST["edit_post"];
		$user = $wpdb->get_results("SELECT * FROM $table_name WHERE `client_id`=$post");
	}
	foreach ($result as $key => $value) {
		add_option($value->client_id, 0);
	}
	if (isset($_POST['submitt'])) {

		echo "<meta http-equiv='refresh' content='0'>";
	}
	?>

	<ul class="nav nav-tabs">
		<li class="<?php echo !isset($_POST["edit_post"]) ? 'active' : '' ?>"><a href="#tab-1">Liste Client</a></li>
		<li class="<?php echo isset($_POST["edit_post"]) ? 'active' : 'disabled' ?>">
			<a href="#tab-2">
				Modifier Client Bocales
			</a>
		</li>

	</ul>

	<div class="tab-content">
		<div id="tab-1" class="tab-pane <?php echo !isset($_POST["edit_post"]) ? 'active' : '' ?>">

			<h3>La listes Des Clients </h3>

			<?php




			echo '<table class="cpt-table"><tr><th>ID</th><th>Nom Client</th><th>Nombre De Bocaux</th><th class="text-center">Nombre De Bocaux Rendu</th><th class="text-center">Actions</th></tr>';
			
			foreach ($result as $item) {
				$customer = new WC_Customer($item->client_id);

				$username = $customer->get_username();
				$option_name = get_option($item->client_id);
				echo "<tr><td>{$item->request_bocal_id}</td><td>{$username}</td><td>{$item->bocal_quantite}</td><td class=\"text-center\">{$option_name}</td><td class=\"text-center\">";

				echo '<form method="post" action="" class="inline-block">';
				echo '<input type="hidden" name="edit_post" value="' . $item->client_id . '">';
				submit_button('Edit', 'primary small', 'submit', false);

				echo '</form></td></tr>';
			}

			echo '</table>';

			?>

		</div>

		<div id="tab-2" class="tab-pane <?php echo isset($_POST["edit_post"]) ? 'active' : '' ?>">
			<form method="post" action="">
				<div class="container d-flex ">

					<div class="card p-3">

						<div class="d-flex align-items-center">

							<div class="image">
								<img src="<?php echo get_avatar_url($user[0]->client_id) ?>" class="rounded" width="40">
							</div>

							<div class="ml-3 w-100">

								<h4 class="mb-0 mt-0"><?php


														$customer = new WC_Customer($user[0]->client_id);

														$username     = $customer->get_username();
														echo $username;
														?> </h4>


								<div class="p-2 mt-2 bg-primary d-flex justify-content-between rounded text-white stats">

									<div class="d-flex flex-column">

										<span class="articles">Nombre Consigne</span>
										<span class="number1"><?php echo get_option('n_orders') ?></span>

									</div>

									<div class="d-flex flex-column">

										<span class="followers">Nombre de Bocaux</span>
										<span class="number2"><?php echo $user[0]->bocal_quantite; ?></span>

									</div>


									<div class="d-flex flex-column">

										<span class="rating">Nombre De Bocaux Rendu</span>
										<span class="number3"><?php echo get_option($user[0]->client_id) ?></span>

									</div>

								</div>

							</div>


						</div>

					</div>

				</div>
				<?php echo '<div style="margin-top: 10px"></div>'; ?>


				<table class="cpt-table">

					<tr>
						<th>ID De commande </th>
						<th>Bocales Statistique</th>
					</tr>
					<tr>
						<?php
						global $wpdb;
						$array = array();
						$bocal_datas = array();
						$bocals_back = array();
						$bocal_total_back_user = 0;
						$amount_coupon = 0;
						$user_id = 0;
						$ids = get_option('id_user');
						
						$table_bocal_order = $wpdb->prefix . "bocal_order";

						$order_datas =  $wpdb->get_results("SELECT * FROM $table_bocal_order WHERE `client_id`=$ids");
						$index_order = 0;
						foreach ($order_datas as $order_data) {

							$array[] = $order_data->order_id;
						}

						$array = array_unique($array);
						$arrLength = count($array);
						update_option('n_orders',$arrLength);
						foreach ($array as $value) {
						?>
						<tr>
							<td>
								Commande #<?php echo $value ?>
							</td>
							<td>
								<table class="cpt-table">

									<?php

									unset($bocal_datas);	
									foreach ($order_datas as $order_data) {
										if ($order_data->order_id == $value) {
											$bocal_datas[] = $order_data;
										}
									}

									foreach ($bocal_datas as $bocal_data) {
										
									?>
										<tr>
											<td>ID De Bocale: <strong><?php echo  $bocal_data->bocal_id ?></strong></td>
											<td>Nom De Bocal: <strong><?php echo  $bocal_data->bocal_name ?></strong></td>
											<td>Nombre De Bocale Pas Rendu: <strong><?php echo  $bocal_data->bocal_quantity ?></strong></td>
											<td>Nombre De Bocale Rendu: <strong><?php echo  $bocal_data->bocal_back ?></strong></td>

											<td class="input text-center"><span>Nouvelle Bocale Rendu: </span><input type="number" name="<?php echo  $bocal_data->id ?>" min="0" max=<?php echo  $bocal_data->bocal_quantity - $bocal_data->bocal_back ?> value="0"></td>
										</tr>

								<?php
										
										$user_id = $user[0]->client_id;
										
										update_option('user_id',$user_id);
										if (isset($_POST[$bocal_data->id])) {

											
											foreach($bocale_result as $item){
													
													if($bocal_data->bocal_id == $item->bocal_id)
														{	
															
															if($_POST[$bocal_data->id]>0){
																
																$amount_coupon += $_POST[$bocal_data->id]*$item->bocal_price;
																
															}
															
														}
											}
											$bocal_back_new = $bocal_data->bocal_back + $_POST[$bocal_data->id];
											$bocals_back["$bocal_data->id"] = $bocal_back_new;
										}
										$bocal_total_back_user += $bocal_data->bocal_back;
									
									}
								


								?>

								</table>

							</td>

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
			<?php
		
			foreach ($bocals_back as $key => $value) {
				$wpdb->update(
					$table_bocal_order,
					array('bocal_back' => $value),
					array('id' => $key)

				);
			}
			
			$amount = $bocale_result ;
			$customer = new WC_Customer(get_option('id_user'));
			$email = array();
			$email[] = $customer->get_email();
			
			$code = 'AV';
			$code .= get_option('id_user');
			$permitted_chars = '3879abcdefghijklmnopqrstuvwxyzAZERTYUIOPQSDFGHJKLWXCVBN';

			$code .=substr(str_shuffle($permitted_chars), 0, 4);
			

				if($amount_coupon>0 ){
					$amount = strval($amount_coupon);	
					$data = [
						'code' => $code,
						'amount' => $amount,
						'usage_limit' => 1,
						'email_restrictions' => $email,
					];
					$woocommerce->post('coupons', $data);	
				}
			
			
				
			foreach ($bocals_back as $key => $value) {
				$wpdb->update(
					$table_bocal_order,
					array('bocal_back' => $value),
					array('id' => $key)
				);
			}

			?>
		</div>

	
	</div>
</div>