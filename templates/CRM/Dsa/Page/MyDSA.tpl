{include file="CRM/common/activityView.tpl"}
<div id="view-activity" style="display:none;">
     <div id="activity-content"></div>
</div>
{literal}
<script type="text/javascript">
function {/literal}{$list}{literal}viewActivity(activityID, contactID, list) {
  if (list) {
    list = "-" + list;
  }

  cj("#view-activity" + list ).show( );

  cj("#view-activity" + list ).dialog({
    title: {/literal}"{ts escape="js"}View Activity{/ts}"{literal},
    modal: true,
    width : "680px", // don't remove px
    height: "560",
    resizable: true,
    bgiframe: true,
    overlay: {
      opacity: 0.5,
      background: "black"
    },

    beforeclose: function(event, ui) {
      cj(this).dialog("destroy");
    },

    open:function() {
      cj("#activity-content" + list , this).html("");
      var viewUrl = {/literal}"{crmURL p='civicrm/case/activity/view' h=0 q="snippet=4" }"{literal};
      cj("#activity-content" + list , this).load( viewUrl + "&cid="+contactID + "&aid=" + activityID + "&type="+list);
    },

    buttons: {
      "{/literal}{ts escape="js"}Done{/ts}{literal}": function() {
        cj(this).dialog("destroy");
      }
    }
  });
}
</script>
{/literal}

<div class="crm-content-block crm-block">
  <div id="help">
    {ts}This overview lists all payable DSAs that have an amount of over 2000 euro and are not approved yet.{/ts}
    <br />
    {ts}Please use the buttons Approve or Reject to reject that specific DSA amount.{/ts}
    <br />
    {ts}If you reject a DSA it will be put back on scheduled for correction by the project officer and the project officer will be informed.{/ts}
  </div>
  {include file="CRM/common/pager.tpl" location="top"}
  {include file='CRM/common/jsortable.tpl'}
  <div id="my_claims-wrapper" class="dataTables_wrapper">
    <table id="my_claims-table" class="display">
      <thead>
      <tr>
        <th>{ts}DSA Type{/ts}</th>
        <th>{ts}DSA Activity ID{/ts}</th>
        <th>{ts}DSA Case ID{/ts}</th>
        <th>{ts}DSA Total Amount{/ts}</th>
        <th>{ts}DSA Contact{/ts}</th>
        <th>{ts}Client Country{/ts}</th>
        <th>{ts}Project Officer{/ts}</th>
        <th>{ts}DSA Activity{/ts}</td>
        <th>{ts}Actions{/ts}</th>
        <th>{ts}Approve{/ts}</th>
        <th>{ts}Reject{/ts}</th>
      </tr>
      </thead>
      <tbody>
      {assign var="rowClass" value="odd-row"}
      {assign var="rowCount" value=0}
      {if $myDSAs|@count gt 0}
        {foreach from=$myDSAs key=dsaActivityId item=myDSA}
          {assign var="rowCount" value=$rowCount+1}
          <tr id="row{$rowCount}" class="{cycle values="odd,even"}">
            <td>{$myDSA.dsa_or_creditdsa}</td>
            <td>{$myDSA.activity_id}</td>
            <td>{$myDSA.case_id}</td>
            <td>{$myDSA.total_dsa_amount}</td>
            <td>
              <a href="{$myDSA.dsa_contact_name_url}">{$myDSA.dsa_contact_name}</a>
            </td>
            <td>{$myDSA.client_country}</td>
            <td>
              <a href="{$myDSA.project_officer_url}">{$myDSA.project_officer_name}</a></td>
            <td><a href="/civicrm/case/activity?reset=1&cid={$myDSA.dsa_contact_id}&caseid={$myDSA.case_id}&id={$myDSA.activity_id}&action=update">DSA details</a> | <a href="javascript:viewActivity({$myDSA.activity_id},{$myDSA.client_contact_id},'');">Activity Details</a></td>
            <td>
                <span>
                  {foreach from=$myDSA.actions item=actionLink}
                    {$actionLink}
                  {/foreach}
                </span>
            </td>
            <td>
                <a href="{$myDSA.url_dsa_approve}">Approve</a>
            </td>
            <td>
                <a href="{$myDSA.url_dsa_reject}">Reject</a>
            </td>
          </tr>
          {if $rowClass eq "odd-row"}
            {assign var="rowClass" value="even-row"}
          {else}
            {assign var="rowClass" value="odd-row"}
          {/if}
        {/foreach}
      {/if}
      </tbody>
    </table>
  </div>
  {include file="CRM/common/pager.tpl" location="bottom"}
</div>