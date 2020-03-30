<?php
$balance = $this->balance;
$statement = $this->statement;
?>
<div class="wrap">
	<?php 
	$imgurl = plugin_dir_url( dirname( __FILE__ ) ).'img/logoeasy.png';
	?>
	<img src="<?php echo $imgurl; ?>" alt="" style="float:right"/>
	<h1 class="wp-heading-inline">Easypay Statements</h1>
	
	<h2><span>Total Balance : <?php echo ($balance && $balance->success ? $balance->data.' '.$balance->currencyCode : 'N/A'); ?></span></h2>
	<h2><span>Login Account : <?php echo $this->setting['phone']; ?></span></h2>	<?php if($this->setting['phone']){ ?>
	<h2><span>Account Unlink : <a href="<?php echo admin_url('admin.php?page=espay_setup_wizard&woocommerce_easypayments_unlink=1'); ?>">Unlink</a></span></h2>	<?php } ?>
	
	<table class="wp-list-table widefat fixed striped users">
		<thead>
			<tr>
				<th scope="col" class="manage-column">
					S.No
				</th>
				<th scope="col" class="manage-column">
					<span>From Name</span><span class="sorting-indicator"></span>
				</th>
				<th scope="col" class="manage-column">
					<span>To Name</span><span class="sorting-indicator"></span>
				</th>
				<th scope="col" class="manage-column">
					<span>From Phone</span><span class="sorting-indicator"></span>
				</th>
				<th scope="col" class="manage-column">
					<span>To Phone</span><span class="sorting-indicator"></span>
				</th>
				<th scope="col" class="manage-column">
					<span>Amount</span><span class="sorting-indicator"></span>
				</th>
				<th scope="col" class="manage-column">
					<span>Type</span><span class="sorting-indicator"></span>
				</th>
				<th scope="col" class="manage-column">
					Date
				</th>
				<th scope="col" class="manage-column">
					Action
				</th>
			</tr>
		</thead>
		<tbody>
		<?php 
			if($statement && $statement->success && $statement->data){
				$s=1;
				foreach($statement->data as $statmentObj){
						$methodClass = '';
						// if (strtolower($statmentObj->type)=='transfer'){
						   // if ($statmentObj->fromPhone==$this->phone){
						   if ($statmentObj->toPhone==$this->phone){
							   $methodClass = 'f8c2b7'; //'incoming-transaction';
						   } else {
							   $methodClass = 'b3d88e'; //'outgoing-transaction';
						   }
						// }
						/* if (strtolower($statmentObj->type)=='deposit'){
						   $methodClass = 'b3d88e'; //incoming-transaction';
						}
						if (strtolower($statmentObj->type)=='bill'){
						   $methodClass = 'f8c2b7'; //'outgoing-transaction';
						} */
						
						if(strtoupper($statmentObj->type)=='TRANSFER'){
							$fromName = $statmentObj->toName;
							$toName = $statmentObj->fromName;
							$fromPhone = $statmentObj->toPhone;
							$toPhone = $statmentObj->fromPhone;
						} else {	
							$toName = $statmentObj->toName;
							$fromName = $statmentObj->fromName;
							$fromPhone = $statmentObj->toPhone;
							$toPhone = $statmentObj->fromPhone;
							
							// $toName = $statmentObj->toName;
							// $fromName = $statmentObj->fromName;
							// $toPhone = $statmentObj->toPhone;
							// $fromPhone = $statmentObj->fromPhone;
						}
		?>
			<tr id="user-1" style="background:#<?php echo $methodClass; ?>">
				<td><?php echo $statmentObj->id; ?></td>
				<td><?php echo $fromName; ?></td>
				<td><?php echo $toName; ?></td>
				<td><?php echo $fromPhone; ?></td>
				<td><?php echo $toPhone; ?></td>
				<td><?php echo $statmentObj->amount.$statmentObj->currency; ?></td>
				<td><?php echo ucfirst($statmentObj->type); ?></td>
				<td><?php echo $statmentObj->date; ?></td>
				<td><a href="javascript:void(0);" onclick='swal("Reason", "<?php echo $statmentObj->reason ?>");'>View</a></td>
			</tr>
		<?php 
			$s++;
			}
		} else {
		?>
			<tr id="user-1">
				<td colspan="9">No Record Found</td>
			</tr>
		<?php } ?>
		</tbody>
		<tfoot>
			<tr>
				<th scope="col" class="manage-column">
					S.No
				</th>
				<th scope="col" class="manage-column">
					<span>From Name</span><span class="sorting-indicator"></span>
				</th>
				<th scope="col" class="manage-column">
					<span>To Name</span><span class="sorting-indicator"></span>
				</th>
				<th scope="col" class="manage-column">
					<span>From Phone</span><span class="sorting-indicator"></span>
				</th>
				<th scope="col" class="manage-column">
					<span>To Phone</span><span class="sorting-indicator"></span>
				</th>
				<th scope="col" class="manage-column">
					<span>Amount</span><span class="sorting-indicator"></span>
				</th>
				<th scope="col" class="manage-column">
					<span>Type</span><span class="sorting-indicator"></span>
				</th>
				<th scope="col" class="manage-column">
					Date
				</th>
				<th scope="col" class="manage-column">
					Action
				</th>
			</tr>
		</tfoot>
	</table>
	
	<?php if($statement && $statement->success && $statement->data){
		?>
		<div class="tablenav bottom">
				<?php 
				
						$num_of_pages = ceil( $statement->total / $this->limit );
						$page_links = paginate_links( array(
							'base' => add_query_arg( 'pagenum', '%#%' ),
							'format' => '',
							'prev_text' => __( '&laquo;', 'text-domain' ),
							'next_text' => __( '&raquo;', 'text-domain' ),
							'total' => $num_of_pages,
							'current' => $this->pagenum
						) );

						if ( $page_links ) {
							?>
							<div class="tablenav-pages">
								<span class="displaying-num"><?php echo $statement->total; ?> items</span>
								<span class="pagination-links">
									<?php echo $page_links; ?>
								</span>
							</div>
							<?php
						}
					?>
				<br class="clear">
		</div>
		<?php
	}
	?>
			
</div>
<style>
td{
	color:#000 !important;
}
</style>