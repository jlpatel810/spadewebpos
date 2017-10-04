<ul><?php 			if($deals) {													
					foreach($deals as $row){ 			
					$sel='';	
					if($selected_deals){
					if(in_array($row->rule_id,$selected_deals))	
					{					
					$sel=' checked="checked" ';	
					}					}?>	

					<li><input type='checkbox' name='deals[]' value='<?php echo $row->rule_id; ?>' <?php echo $sel; ?>  />  <?php echo $row->rule_name; ?></li>	
					
<?php } } else { echo "<li>There is no deals found for your criatarea.</li>";  } ?> 
					</ul>		