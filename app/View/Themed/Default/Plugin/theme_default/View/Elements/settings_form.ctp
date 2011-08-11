<?php echo $this->Form->input('Module.settings.slider_folder', array('between' => $this->Html->url('/files/', true), 'type' => 'text', 'label' => __d('theme_default', 'Image slider folder') ) ); ?>
<em>
    <?php echo __d('theme_quick', 'Recomended images size:') ?> 974x302px<br/>
</em>
<p>
    <?php echo $this->Html->script('/theme/Default/js/farbtastic/farbtastic.js'); ?>
    <?php echo $this->Html->css('/theme/Default/js/farbtastic/farbtastic.css'); ?>
    <?php echo $this->Html->useTag('fieldsetstart', __t('Color Scheme')); ?>
        <div style="width:200px; float:left;">
            <?php $color_header_top = @empty($this->data['Module']['settings']['color_header_top']) ? '#123456' : $this->data['Module']['settings']['color_header_top']; ?>
            <?php $color_header_bottom = @empty($this->data['Module']['settings']['color_header_bottom']) ? '#123456' : $this->data['Module']['settings']['color_header_bottom']; ?>
            <?php echo $this->Form->input('Module.settings.color_header_top', array('value' => $color_header_top, 'class' => 'colorwell', 'style' => 'width:50px;', 'type' => 'text', 'label' => __d('theme_default', 'Header top') ) ); ?>
            <?php echo $this->Form->input('Module.settings.color_header_bottom', array('value' => $color_header_bottom, 'class' => 'colorwell', 'style' => 'width:50px;', 'type' => 'text', 'label' => __d('theme_default', 'Header bottom') ) ); ?>
        </div>
        <div id="colorpicker" style="float:left;"></div>
    <?php echo $this->Html->useTag('fieldsetend'); ?>
</p>

<script type="text/javascript">
    $(document).ready(function() {
        $('#demo').hide();
        var f = $.farbtastic('#colorpicker');
        var p = $('#colorpicker').css('opacity', 0.25);
        var selected;
        $('.colorwell')
        .each(function () { f.linkTo(this); $(this).css('opacity', 0.75); })
        .focus(function() {
            if (selected) {
                $(selected).css('opacity', 0.75).removeClass('colorwell-selected');
            }
            f.linkTo(this);
            p.css('opacity', 1);
            $(selected = this).css('opacity', 1).addClass('colorwell-selected');
        });
    });
</script>