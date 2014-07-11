{**
 * FILE: dsa/templates/dsa_section.tpl
 *}
 
{* template block that contains the new field *}
{$form.dsa_ref_dt.html}
{$form.dsa_type.html}
{$form.dsa_load_location.html}
{$form.dsa_location_lst.html}
{$form.dsa_location_id.html}
{$form.dsa_participant_id.html}
{$form.dsa_participant_role.html}
<table id="dsa-temp">
	<tr>
		<td colspan="2">
			<div id="preDsaSpacer"></div>
		</td>
	</tr>
	<tr class="crm-case-activity-form-block-dsa_participant">
        <td class="label">{$form.dsa_participant.label}</td>
		<td class="view-value">{$form.dsa_participant.html}</td>
	</tr>
	<tr class="crm-case-activity-form-block-dsa_country">
        <td class="label">{$form.dsa_country.label}</td>
		<td class="view-value">{$form.dsa_country.html}</td>
	</tr>
	<tr class="crm-case-activity-form-block-dsa_location">
        <td class="label">{$form.dsa_location.label}</td>
		<td class="view-value">{$form.dsa_location.html}</td>
	</tr>
	<tr class="crm-case-activity-form-block-dsa_percentage">
        <td class="label">{$form.dsa_percentage.label}</td>
		<td class="view-value">{$form.dsa_percentage.html}</td>
	</tr>
	<tr class="crm-case-activity-form-block-dsa_percentage">
        <td class="label">{$form.dsa_days.label}</td>
		<td class="view-value">{$form.dsa_days.html}</td>
	</tr>
	<tr class="crm-case-activity-form-block-dsa_percentage">
        <td class="label">{$form.dsa_amount.label}</td>
		<td class="view-value">{$form.dsa_amount.html}</td>
	</tr>
	<tr class="crm-case-activity-form-block-dsa_briefing">
        <td class="label">{$form.dsa_briefing.label}</td>
		<td class="view-value">{$form.dsa_briefing.html}</td>
	</tr>
	<tr class="crm-case-activity-form-block-dsa_debriefing">
        <td class="label">{$form.dsa_debriefing.label}</td>
		<td class="view-value">{$form.dsa_debriefing.html}</td>
	</tr>
	<tr class="crm-case-activity-form-block-dsa_airport">
        <td class="label">{$form.dsa_airport.label}</td>
		<td class="view-value">{$form.dsa_airport.html}</td>
	</tr>
	<tr class="crm-case-activity-form-block-dsa_transfer">
        <td class="label">{$form.dsa_transfer.label}</td>
		<td class="view-value">{$form.dsa_transfer.html}</td>
	</tr>
	<tr class="crm-case-activity-form-block-dsa_hotel">
        <td class="label">{$form.dsa_hotel.label}</td>
		<td class="view-value">{$form.dsa_hotel.html}</td>
	</tr>
	<tr class="crm-case-activity-form-block-dsa_visa">
        <td class="label">{$form.dsa_visa.label}</td>
		<td class="view-value">{$form.dsa_visa.html}</td>
	</tr>
	<tr class="crm-case-activity-form-block-dsa_outfit">
        <td class="label">{$form.dsa_outfit.label}</td>
		<td class="view-value">{$form.dsa_outfit.html}</td>
	</tr>
	<tr class="crm-case-activity-form-block-dsa_other">
        <td class="label">{$form.dsa_other.label}</td>
		<td class="view-value">{$form.dsa_other.html}</td>
	</tr>
	<tr class="crm-case-activity-form-block-dsa_other_description">
        <td class="label">{$form.dsa_other_description.label}</td>
		<td class="view-value">{$form.dsa_other_description.html}</td>
	</tr>
	<tr class="crm-case-activity-form-block-dsa_advance">
        <td class="label">{$form.dsa_advance.label}</td>
		<td class="view-value">{$form.dsa_advance.html}</td>
	</tr>
{if $form.dsa_approval.value != ''}
	<tr class="crm-case-activity-form-block-dsa_approval">
        <td class="label">{$form.dsa_approval.label}</td>
		<td class="view-value">{$form.dsa_approval.value}</td>
	</tr>
{/if}
	{*
	<tr>
		<td colspan="2">
			<div id="postDsaSpacer"></div>
		</td>
	</tr>
	*}
</table>

{* reposition the above attachments *}
{literal}
<script type="text/javascript">	
	// move newly created rows
	cj('#dsa-temp tr').insertBefore('tr.crm-case-activity-form-block-attachment');
	// remove temporary dsa container table
	cj('#dsa-temp').remove();
	// remove (hide) attachment
	cj('tr.crm-case-activity-form-block-attachment').hide();
	// remove (hide) send a copy
	cj('tr.crm-case-activity-form-block-send_copy').hide();
	// remove (hide) schedule follow-up
	cj('tr.crm-case-activity-form-block-schedule_followup').hide();
	// remove (hide) duration
	cj('tr.crm-case-activity-form-block-duration').hide();
	// remove (hide) priority
	cj('tr.crm-case-activity-form-block-priority_id').hide();
	// hide subject
	cj('tr.crm-case-activity-form-block-subject').hide();
	
	var ratesData = cj.parseJSON(cj('#dsa_location_lst').val()); {/literal}{* nl.pum.dsa/CRM/Dsa/Page/DSAImport.php function getAllActiveRates(dt) *}{literal}
	cj('#dsa_location_lst').remove(); // avoids submitting JSON data, causing the infamous IDS->kick error. DOWNSIDE: location list is lost in validation failures
	//cj('#dsa_location_lst').val(json_encode(cj('#dsa_location_lst').val(), JSON_HEX_TAG));
	
	// add onChange event to dsa_country field to build a list of selectable locations
	cj('#dsa_participant').change(function() { processDSAParticipantChange(this) });
	// add onChange event to dsa_country field to build a list of selectable locations
	cj('#dsa_country').change(function() { processDSACountryChange(this) });
	// add onChange event to dsa_location field to update dsa amount
	cj('#dsa_location').change(function() { processDSALocationChange(this) });
	// add onChange event to dsa_percentage field to update dsa amount
	cj('#dsa_percentage').change(function() { processDSAPercentageChange(this) });
	// add onChange event to dsa_days field to update dsa amount
	cj('#dsa_days').change(function() { processDSADaysChange(this) });
	// add onChange event to dsa_other field to enable/disable dsa_other_description
	//cj('#dsa_other').change(function() { processDSAOtherChange(this) });

	// trigger onChange on dsa_country to retrieve an initial set of locations
	cj('#dsa_country').trigger('change', ['{$form.dsa_location.value[0]']);


	
	function processDSACountryChange(elm) {
		// exit function if no country is selected
		if (elm.value=='') {
			return true;
		}
		// clear all options for dsa_locations
		cj('#dsa_location option:gt(0)').remove();
		var loc = cj('#dsa_location');
		
		// query for new locations based on the selected dsa_country
		//var ratesData = cj.parseJSON(cj('#dsa_location_lst').val()); {/literal}{* nl.pum.dsa/CRM/Dsa/Page/DSAImport.php function getAllActiveRates(dt) *}{literal}
		var dt = ratesData.ref_date;
		cj('#dsa_ref_dt').val(dt);
		if (ratesData.countries[elm.value]) {
			cj.each(ratesData.countries[(elm.value)].locations, function(index, value) {
				loc.append('<option value="' + value.id + '|' + value.rate + '">' + value.location + ' (' + parseFloat(value.rate).toFixed(2) + ')</option>');
			});
		} else {
			CRM.alert('No locations found');
		}
		//CRM.alert('Preset location\n' + cj('#dsa_load_location').val());
		loc.val(cj('#dsa_load_location').val()); // apply default value after initial load
		//cj('#dsa_load_location').val(''); // clear default value for location -> DON'T: need it again after validation failure? Or update on location change?
		return true;
	}
	
	function processDSALocationChange(elm) {
		//CRM.alert('Location changed');
		processAmountUpdate();
		// make sure the location is set when the page reopens after validation failure
		cj('#dsa_load_location').val(cj('#dsa_location').val());
		// store location id in separate field
		var data = (cj('#dsa_location').val() + '|0').split('|');
		cj('#dsa_location_id').val(data[0]);
		return true;
	}
	
	function processDSAPercentageChange(elm) {
		//CRM.alert('Percentage changed');
		processAmountUpdate();
		return true;
	}
	
	function processDSADaysChange(elm) {
		//CRM.alert('Days changed');
		processAmountUpdate();
		return true;
	}
	
	function processAmountUpdate() {
		rate = (cj('#dsa_location').val() + '|0').split('|')[1];
		pct = cj('#dsa_percentage').val();
		dur = cj('#dsa_days').val();
		amt = ((rate * pct * dur) / 100).toFixed(2);
		cj('#dsa_amount').val(amt);
		return true;
	}
	
	function processDSAOtherChange(elm) {
		amt = cj('#dsa_other').val();
		hide = (cj.trim(amt)=='' || parseFloat(amt)==0);
		cj('#dsa_other_description').prop('disabled', hide);
	}
	
	function processDSAParticipantChange(elm) {
		//CRM.alert('Participant changed');
		cj('#subject').val(cj('#dsa_participant option:selected').text());
		var data = (cj('#dsa_participant').val() + '|0|0').split('|');
		cj('#dsa_participant_id').val(data[0]);
		cj('#dsa_participant_role').val(data[1]);
		cj('#dsa_type').val(data[2]);
		return true;
	}
	
</script>
{/literal}