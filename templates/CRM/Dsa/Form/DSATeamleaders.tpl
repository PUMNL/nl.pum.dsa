{* HEADER *}
<div class="crm-block crm-form-block">
  <div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="top"}
  </div>

  <div class="crm-section">
    <div class="label"></div>
    <div class="content">
      Please select the teamleaders on the left and press 'Add teamleader >>' to add a users as teamleader. <br />
      To remove a user select the user on the right and press '<< Remove teamleader'.

    </div>
    <div class="clear"></div>
  </div>

  <div class="crm-section">
    <div class="label">{$form.teamleaders.label}</div>
    <div class="content">{$form.teamleaders.html}</div>
    <div class="clear"></div>
  </div>

  {* FOOTER *}
  <div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="bottom"}
  </div>
</div>