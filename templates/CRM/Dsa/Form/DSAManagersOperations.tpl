{* HEADER *}
<div class="crm-block crm-form-block">
  <div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="top"}
  </div>

  <div class="crm-section">
    <div class="label"></div>
    <div class="content">
      Please select the manager operations on the left and press 'Add manager operations >>' to add a users as manager operations. <br />
      To remove a user select the user on the right and press '<< Remove manager operations'.

    </div>
    <div class="clear"></div>
  </div>

  <div class="crm-section">
    <div class="label">{$form.managers_operations.label}</div>
    <div class="content">{$form.managers_operations.html}</div>
    <div class="clear"></div>
  </div>

  {* FOOTER *}
  <div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="bottom"}
  </div>
</div>