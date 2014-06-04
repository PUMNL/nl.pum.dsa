{**
 * FILE: dsa/templates/dsa_section.tpl
 *}
 
{* template block that contains the new field *}
{$form.dsa_ref_dt.html}
{$form.dsa_load_location.html}
{$form.dsa_location_lst.html}
<table id="dsa-temp">
	<tr>
		<td colspan="2">
			<div id="preDsaSpacer"></div>
		</td>
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
	{* approver cid to name ? *}
	{* approval date/time ? *}
	<tr>
		<td colspan="2">
			<div id="postDsaSpacer"></div>
		</td>
	</tr>
</table>

{* reposition the above attachments *}
{literal}
<script type="text/javascript">
	// move newly created rows
	cj('#dsa-temp tr').insertBefore('tr.crm-case-activity-form-block-attachment');
	// remove temporary dsa container table
	cj('#dsa-temp').remove();

	// add onChange event to dsa_country field to build a list of selectable locations
	cj('#dsa_country').change(function() { processDSACountryChange(this) });
	// add onChange event to dsa_location field to update dsa amount
	cj('#dsa_location').change(function() { processDSALocationChange(this) });
	// add onChange event to dsa_percentage field to update dsa amount
	cj('#dsa_percentage').change(function() { processDSAPercentageChange(this) });
	// add onChange event to dsa_days field to update dsa amount
	cj('#dsa_days').change(function() { processDSADaysChange(this) });
	// add onChange event to dsa_other field to enable/disable dsa_other_description
	cj('#dsa_other').change(function() { processDSAOtherChange(this) });

	// trigger onChange on dsa_country to retrieve an initial set of locations
	cj('#dsa_country').trigger('change', ['{$form.dsa_location.value[0]']);
	// trigger onChange on dsa_other to retrieve an initial set of locations
	cj('#dsa_other').trigger('change');
	
	function processDSACountryChange(elm) {
		// exit function if no country is selected
		if (elm.value=='') {
			return true;
		}
		// clear all options for dsa_locations
		cj('#dsa_location option:gt(0)').remove();
		var loc = cj('#dsa_location');
		
		// query for new locations based on the selected dsa_country
		var ratesData = cj.parseJSON(cj('#dsa_location_lst').val()); {/literal}{* nl.pum.dsa/CRM/Dsa/Page/DSAImport.php function getAllActiveRates(dt) *}{literal}
		var dt = ratesData.ref_date;
		cj('#dsa_ref_dt').val(dt);
		if (ratesData.countries[elm.value]) {
			cj.each(ratesData.countries[(elm.value)].locations, function(index, value) {
				loc.append('<option value="' + value.id + '|' + value.rate + '">' + value.location + ' (' + parseFloat(value.rate).toFixed(2) + ')</option>');
			});
		} else {
			CRM.alert('No locations found');
		}
		loc.val(cj('#dsa_load_location').val()); // apply default value after initial load
		cj('#dsa_load_location').val(''); // clear default value for location
		return true;
	}
	
	function processDSALocationChange(elm) {
		//CRM.alert('Location changed');
		processAmountUpdate();
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
	
</script>
{/literal}