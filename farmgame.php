<?php
	session_start(['path'=>'farmgame']);
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Farm Game</title>
	</head>
	<body>
		<?php
			
			// alive
			$farm_arr = $alive_arr = array('farmer1','cow1','cow2','bunny1','bunny2','bunny3','bunny4');					
			if( isset($_SESSION['dead']) && !empty($_SESSION['dead']) && is_array($_SESSION['dead']) ){
				foreach( $alive_arr as $key=>$value ){
					if( in_array($value,$_SESSION['dead']) ){
						unset($alive_arr[$key]);
					}
				}
				$alive_arr = array_values($alive_arr);
			}
			
			

			if( isset($_POST['feed'])){
				
				// all_turn_count
				if( !isset($_SESSION['all_turn_count']) ){
					$_SESSION['all_turn_count'] = 1;
				}else{
					$_SESSION['all_turn_count']++;
				}			
				
				// random select from array
				
				print_r(shuffle($alive_arr));
				$selected = $alive_arr[0];
				echo '<br>selected='.$selected;
				echo '<br>alive='.print_r($alive_arr);
				echo '<br>turn='.$_SESSION['all_turn_count'];
				
				// feed  logic as per conditions


				foreach( $alive_arr as $value ){
					
					if( $value == $selected ){
						if( !isset($_SESSION['feed_count'][$value]) ){
							$_SESSION['feed_count'][$value] = 1;
						}else{
							$_SESSION['feed_count'][$value]++;
						}
						$_SESSION['table_farm'][][$value] = 'fed';
					}

					$limit = 8; // bunny die limit
					if( $value == 'farmer1' ){
						$limit = 15; // farmer die limit
					}elseif( in_array( $value, array('cow1','cow2') ) ){
						$limit = 10; // cow die limit
					}
					
					$fed = 0;
					if( isset($_SESSION['feed_count'][$value]) && !empty($_SESSION['feed_count'][$value]) ){
						$fed = $_SESSION['feed_count'][$value];
					}
					$all_turn_count = $_SESSION['all_turn_count'];
					$min = $all_turn_count / $limit;
					
					// calculate dead farm elements 
					if( $all_turn_count % $limit == 0 && $fed < $min ){
						$_SESSION['dead'][] = $value;
					}
					
				}
				
			}
			// new game condition
			if( isset($_POST['start']) && !empty($_POST['start']) ){
				session_destroy();
				$filename = basename($_SERVER['PHP_SELF']);
				header("location:".$filename);
			}
			
			$msg = '';
			$stop = 0;
			if( isset($_SESSION['all_turn_count']) && !empty($_SESSION['all_turn_count']) && $_SESSION['all_turn_count'] >= 20 ){
				// condition to check who wins
				$stop = 1;
				if( isset($alive_arr) && !empty($alive_arr) ){
					$farmer = $cow = $bunny = 0;
					foreach( $alive_arr as $alive ){
						if( $alive == 'farmer1' ){
							$farmer++;						
						}if( strpos($alive,'cow') !== false ){
							$cow++;						
						}if( strpos($alive,'bunny') !== false ){
							$bunny++;						
						}
					}
					if( $farmer >= 1 && $cow >= 1 && $bunny >= 1 ){
						$msg = 'You won the game.';
					}else{
						$msg = 'You lost the game. Atleast the farmer, 1 cow and 1 bunny should be alive.';
					}
				}
			}elseif( isset($_SESSION['dead']) && !empty($_SESSION['dead']) && in_array('farmer1',$_SESSION['dead']) ){
				$stop = 1;
				$msg = 'You lost the game, farmer died.';
			}
			//check button for new game conditon
			$name = 'feed';
			$type = 'submit';
			$disabled = '';
			if( $stop == 1 ){
				$name = 'stop';
				$type = 'button';
				$disabled = 'disabled';
			}
			
			
			
		?>

		<form method="post" action="">
			
			<input type="<?php echo $type; ?>" name="<?php echo $name; ?>" value="Feed" <?php echo $disabled; ?> />
			<?php if( $stop == 1 ){ ?>
				<input type="submit" name="start" value="Start a new game" />
				<br/><br/> Game Over. <?php echo $msg; ?>
			<?php } ?>
		</form>
		
		<br/>

		<?php
			if( isset($_SESSION['table_farm']) && !empty($_SESSION['table_farm']) ){
		?>
			<table border="1" cellspacing="0" cellpadding="10">
				<tr>
					<th>Round</td>
					<?php
						foreach( $farm_arr as $header ){
							$bgcolor = '';
							if( isset($_SESSION['dead']) && !empty($_SESSION['dead']) && in_array($header,$_SESSION['dead']) ){
								$bgcolor = 'bgcolor="red"';
							}
							echo "<th ".$bgcolor.">".ucfirst($header)."</th>";
						}
					?>
				</tr>
				<?php
					foreach( $_SESSION['table_farm'] as $key => $value ){
						echo "<tr>";
						echo "<td>".($key+1)."</td>";
						foreach( $farm_arr as $farm_elements ){
							if( isset($_SESSION['table_farm'][$key][$farm_elements]) && !empty($_SESSION['table_farm'][$key][$farm_elements]) ){
								echo "<td>".$_SESSION['table_farm'][$key][$farm_elements]."</td>";
							}else{
								echo "<td></td>";
							}
						}
						echo "</tr>";
					}				
				?>
			</table>
		<?php
			}
		?>
	
		<?php
			
			if( isset($_SESSION) ){ echo "<pre>"; print_r($_SESSION); echo "<pre/>"; }
		?>

	</body>
</html>
