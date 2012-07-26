<section class="title">
	<h4><?php echo lang('places:places'); ?></h4>
</section>

<section class="item">
	<?php echo form_open('admin/places/delete');?>
	
	<?php if (!empty($places)): ?>
	
		<table>
			<thead>
			    <!-- Header row -->
				<tr>
					<th><?php echo form_checkbox(array('name' => 'action_to_all', 'class' => 'check-all'));?></th>
                    <th><?php echo lang('places:id'); ?></th>
					<th><?php echo lang('places:name'); ?></th>
					<th><?php echo lang('places:address'); ?></th>
					<th></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="3">
						<div class="inner"><?php $this->load->view('admin/partials/pagination'); ?></div>
					</td>
				</tr>
			</tfoot>
			<tbody>
				<?php				    // Loop through each team here.
				    foreach( $places as $location ):
			    ?>
				<tr>
					<td><?php echo form_checkbox('action_to[]', $location->id); ?></td>
					<td><?php echo $location->id; ?></td>
					<td><?php echo $location->name; ?></td>
					<td><?php echo $location->address; ?></td>
					<td class="actions">
						<?php echo
						anchor('http://maps.googleapis.com/maps/api/staticmap?'.$default_params.'&
							center='.$location->address.'&
							markers='.$location->address,
							lang('global:view'), 'class="button" target="_blank"') ?>
						<?php if ($can_edit) echo anchor('admin/places/edit/'.$location->id, lang('global:edit'), 'class="button"'); ?>
						<?php if ($can_delete) echo anchor('admin/places/delete/'.$location->id, 	lang('global:delete'), array('class'=>'button')); ?>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		
		<div class="table_action_buttons">
			<?php $this->load->view('admin/partials/buttons', array('buttons' => array('delete'))); ?>
		</div>
		
	<?php else: ?>
		<div class="no_data"><?php echo lang('sports:no_items'); ?></div>
	<?php endif;?>
	
	<?php echo form_close(); ?>
</section>
