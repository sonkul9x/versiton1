<div class="filter">
    <?php echo form_open('dashboard/pages'); ?>
    <span class="display_status">Ngôn ngữ: <?php if (isset($lang_combobox)) echo $lang_combobox; ?></span>
    Từ khóa: <?php echo form_input(array('name' => 'search', 'id' => 'search', 'maxlength' => '256', 'value' => $search)); ?>
    <input type="submit" name="submit" value="Tìm kiếm" class="btn" />
    <?php echo form_close(); ?>
</div>
