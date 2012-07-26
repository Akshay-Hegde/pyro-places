<section class="title">
	<h4><?phpecho lang('places:places'); ?></h4>
</section>

<section class="item">
	<?phpecho form_open('admin/places/delete');?>
	
	<?phpif (!empty($places)): ?>
	
		<table>
			<thead>
			    <!-- Header row -->
				<tr>
					<th><?phpecho form_checkbox(array('name' => 'action_to_all', 'class' => 'check-all'));?></th>
                    <th><?phpecho lang('places:id'); ?></th>
					<th><?phpecho lang('places:name'); ?></th>
					<th><?phpecho lang('places:address'); ?></th>
					<th></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="3">
						<div class="inner"><?php$this->load->view('admin/partials/pagination'); ?></div>
					</td>
				</tr>
			</tfoot>
			<tbody>
				<?php				    // Loop through each team here.
				    foreach( $places as $location ):
			    ?>
				<tr>
					<td><?phpecho form_checkbox('action_to[]', $location->id); ?></td>
					<td><?phpecho $location->id; ?></td>
					<td><?phpecho $location->name; ?></td>
					<td><?phpecho $location->address; ?></td>
					<td class="actions">
						<?php echo
						anchor('http://maps.googleapis.com/maps/api/staticmap?'.$default_params.'&
							center='.$location->address.'&
							markers='.$location->address,
							lang('global:view'), 'class="button" target="_blank"') ?>
						<?phpif ($can_edit) echo anchor('admin/places/edit/'.$location->id, lang('global:edit'), 'class="button"'); ?>
						<?phpif ($can_delete) echo anchor('admin/places/delete/'.$location->id, 	lang('global:delete'), array('class'=>'button')); ?>
					</td>
				</tr>
				<?phpendforeach; ?>
			</tbody>
		</table>
		
		<div class="table_action_buttons">
			<?php$this->load->view('admin/partials/buttons', array('buttons' => array('delete'))); ?>
		</div>
		
	<?phpelse: ?>
		<div class="no_data"><?phpecho lang('sports:no_items'); ?></div>
	<?phpendif;?>
	
	<?phpecho form_close(); ?>
</section>
