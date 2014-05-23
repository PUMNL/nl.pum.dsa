<form method="post" action="/index.php?q=civicrm/dsa/import&action=result" enctype="multipart/form-data">

{*
Debug:<br/>
$action = {$action}<br/>
$user_action = {$user_action}<br/>
*}

{* === block showing existing batches === *}

{if $action == 'upload'
 or $action == 'result' && $user_action == 'upload'
 or $action == 'read'}
<h3>{$labels.existing.header}</h3>
<div id="dsa_available" class="dataTables_wrapper">
	{* ***** show list of existing batches (import-, start- and enddate) ***** *}
	<table>
		<tr>
			<th>{$labels.existing.importdate}</th>
			<th>{$labels.existing.startdate}</th>
			<th>{$labels.existing.enddate}</th>
		</tr>
{assign var="rowClass" value="odd-row"}
{foreach from=$dsaBatch.values key=index item=batch}
		<tr id="row_{$index}" class={$rowClass}>
			<td>{$batch.importdate}</td>
			<td>{$batch.startdate}</td>
			<td>{$batch.enddate}</td>
		</tr>
	{if $rowClass eq "odd-row"}
		{assign var="rowClass" value="even-row"}
	{else}
		{assign var="rowClass" value="odd-row"}                        
	{/if}
{/foreach}
	</table>
</div>
<br/>
{/if}



{* === block showing post-upload results for 'upload' and 'convert' === *}

{if $action == 'result'}
<h3>{$labels.upload.results}</h3>
<div class="crm-block crm-form-block">
	{if $user_action == 'convert'}
	<label for="results">{$labels.upload.results_info_convert}</label><br>
	<div class="resizable-textarea">
		<span>
			<textarea class="form-textarea textarea-processed" id="results" name="results" rows="10" cols="60">{$csv}</textarea>
		</span>
	</div>
	{/if}
	{if $user_action == 'upload'}
	<label for="results">{$labels.upload.results_info_report}</label><br>
	<div>
		batch_id = {$report.batch_id}<br/>
		number of lines read = {$report.num_lines}<br/>
		number of records imported = {$report.num_imported}<br/>
	</div>
	{/if}
</div>
<br/>
{/if}



{* === block showing input form for 'upload' and 'convert' === *}

{if $action == 'convert'
 or $action == 'upload'}
{assign var='mandatory' value="<span class=\"crm-marker\" title=\"This field is required.\">*</span>"}	
<h3>{$labels.upload.header}</h3>
<div class="crm-block crm-form-block">
	<div id="crm-submit-buttons-top" class="crm-submit-buttons">
		<span class="crm-button">
			<input id="upload" class="form-button" type="submit" value="Upload" name="Upload" onclick="CRM.alert('Upload button clicked');">
		</span>
	</div>
	
	<input type="hidden" name="user_action" id="user_action" value="{$action}">

	<table class="form-layout-compressed">
		<tbody>

{if $action == 'convert'}
		<tr>
			<td class="label">{$labels.upload.file_locations}{$mandatory}</td>
			{* <td><input type="file" name="file_locations" id="file_locations" class="form=text" accept="text/plain" value="" /></td> *}
			<td><input type="file" name="file_locations" id="file_locations" class="form=text" value="" /></td>
		</tr>
		<tr>
			<td class="label">{$labels.upload.file_rates}{$mandatory}</td>
			{* <td><input type="file" name="file_rates" id="file_rates" class="form=text" accept="text/plain" value="" /></td> *}
			<td><input type="file" name="file_rates" id="file_rates" class="form=text" value="" /></td>
		</tr>
{/if}{* convert *}

{if $action == 'upload'}
{assign var='elementDate' value="activation_date"}
		<tr>
			<td class="label">&nbsp;</td>
			<td class="form=text">
{foreach from=$labels.upload.convert_info item=line}
				{$line}<br/>
{/foreach}
			</td>
		</tr>
		<tr>
			<td class="label">{$labels.upload.file_dsa}{$mandatory}</td>
			{* <td><input type="file" name="file_dsa" id="file_dsa" class="form=text" accept="*.csv, text/plain" value="" /></td> *}
			<td><input type="file" name="file_dsa" id="file_dsa" class="form=text" value="" /></td>
		</tr>
		<tr>
			<td class="label">{$labels.upload.activation_date}</td>
			<td>
				<input id="{$elementDate}" class="form-text" type="text" value="{$displayStartDate}" 
					name="activation_date" format="{$labels.date_format}" startoffset="1" endoffset="3"
					formattype="searchDate" style="display: none;" />
{include file="CRM/Dsa/Page/pum_jcal.tpl"}
			</td>
		</tr>
{/if} {* upload *}
		
		</tbody>
	</table>

	<div id="crm-submit-buttons-bottom" class="crm-submit-buttons">
	</div>	
</div>
{/if}{* convert or upload *}


{literal}
	<script type="text/javascript">
		cj("#crm-submit-buttons-bottom").html(cj("#crm-submit-buttons-top").html());
		function validateForm() {
			CRM.alert(document.forms[0]);
		}
	</script>
{/literal}

</form>