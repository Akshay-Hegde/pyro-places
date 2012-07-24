<section class="title">
	<!-- We'll use $this->method to switch between sample.create & sample.edit -->
	<h4><?php echo lang('places:'.$this->method); ?></h4>
</section>

<section class="item">

	<?php echo form_open_multipart($this->uri->uri_string(), 'class="crud"'); ?>
		
		<div class="form_inputs">
	
		<ul>
			<li class="<?php echo alternator('', 'even'); ?>">
				<label for="name"><?php echo lang('places:name'); ?> <span>*</span></label>
				<div class="input"><?php echo form_input('name', set_value('name', $name)); ?></div>
			</li>
			<li class="<?php echo alternator('', 'even'); ?>">
				<label for="description"><?php echo lang('places:description'); ?> </label>
				<div class="input"><?php echo form_textarea('description', set_value('description', isset($description) ? $description : ''), 'wysiwyg-simple"'); ?></div>
			</li>
			<li>
				<label for="address"><?php echo lang('places:address'); ?> </label>
				<div class="input"><?php echo form_input('address', set_value('address', $address)); ?></div>
			</li>
		</ul>
		
		</div>

		<div class="buttons">
			<?php $this->load->view('admin/partials/buttons', array('buttons' => array('save', 'cancel') )); ?>
		</div>
		
	<?php echo form_close(); ?>

</section>