function loginVisibility(evt) {
  if(rcmail.env.maintenance_is_maint)
    $('#login-form').hide();
  else
    $('#login-form').show();
}

rcmail.addEventListener('init', loginVisibility);
