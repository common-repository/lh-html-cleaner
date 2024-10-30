<h1><?php echo esc_html(get_admin_page_title()); ?></h1>
<form name="lh_login_page-backend_form" method="post" action="">
<input type="hidden" name="<?php echo $this->hidden_field_name; ?>" value="Y" />

<p><?php _e("Blacklisted tags;", $this->namespace ); ?> 
<input type="text" name="<?php echo $this->blacklisted_tags_field_name; ?>" id="<?php echo $this->blacklisted_tags_field_name; ?>" value="<?php echo implode(",", $this->options[ $this->blacklisted_tags_field_name ]); ?>" size="60" placeholder="enter a comma separated list of blacklisted tags e.g.: script,object etc" />
</p>

<p><?php _e("Blacklisted attributes;", $this->namespace ); ?> 
<input type="text" name="<?php echo $this->blacklisted_attributes_field_name; ?>" id="<?php echo $this->blacklisted_attributes_field_name; ?>" value="<?php echo implode(",", $this->options[ $this->blacklisted_attributes_field_name ]); ?>" size="60" placeholder="enter a comma separated list of blacklisted attributes e.g.: style,font etc" />
</p>


<p class="submit">
<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
</p>

</form>