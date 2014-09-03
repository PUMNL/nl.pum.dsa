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
{$form.invoice_number.html}
{$form.invoice_dsa.html}
{$form.invoice_briefing.html}
{$form.invoice_airport.html}
{$form.invoice_transfer.html}
{$form.invoice_hotel.html}
{$form.invoice_visa.html}
{$form.invoice_medical.html}
{$form.invoice_other.html}
{$form.restrictEdit.html}
{$form.credit_data.html}
{$form.credit_act_id.html}
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
	<tr class="crm-case-activity-form-block-dsa_days">
        <td class="label">{$form.dsa_days.label}</td>
		<td class="view-value">{$form.dsa_days.html}</td>
	</tr>
	<tr class="crm-case-activity-form-block-dsa_amount">
        <td class="label">{$form.dsa_amount.label}</td>
		<td class="view-value">{$form.dsa_amount.html}</td>
	</tr>
	<tr class="crm-case-activity-form-block-dsa_briefing">
        <td class="label">{$form.dsa_briefing.label}</td>
		<td class="view-value">{$form.dsa_briefing.html}</td>
	</tr>
{*
	<tr class="crm-case-activity-form-block-dsa_debriefing">
        <td class="label">{$form.dsa_debriefing.label}</td>
		<td class="view-value">{$form.dsa_debriefing.html}</td>
	</tr>
*}
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
	<tr class="crm-case-activity-form-block-dsa_medical">
        <td class="label">{$form.dsa_medical.label}</td>
		<td class="view-value">{$form.dsa_medical.html}</td>
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
	<tr class="crm-case-activity-form-block-dsa_total">
        <td class="label">total</td>
		<td class="view-value bold"><span id="dsa_total"></span></td>
	</tr>	
{if $form.dsa_approval.value != ''}
	<tr class="crm-case-activity-form-block-dsa_approval">
        <td class="label">{$form.dsa_approval.label}</td>
		<td class="view-value">{$form.dsa_approval.value}</td>
	</tr>
{/if}
	<tr class="crm-case-activity-form-block-invoice_number">
        <td class="label"></td>
		<td class="view-value"></td>
	</tr>

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
	// remove (hide) assign to
	cj('tr.crm-case-activity-form-block-assignee_contact_id').hide();
	// remove (hide) schedule follow-up
	cj('tr.crm-case-activity-form-block-schedule_followup').hide();
	// remove (hide) duration
	cj('tr.crm-case-activity-form-block-duration').hide();
	// remove (hide) priority
	cj('tr.crm-case-activity-form-block-priority_id').hide();
	// hide subject
	cj('tr.crm-case-activity-form-block-subject').hide();
	// hide medium and location
	cj('tr.crm-case-activity-form-block-medium_id').hide();
	// hide details and spacer row below it
	cj('tr.crm-case-activity-form-block-details').hide();
	cj('tr.crm-case-activity-form-block-details').next('tr').hide
	// process invoice number
	var obj=cj('#invoice_number');
	if (obj.val() == '') {
		cj('tr.crm-case-activity-form-block-invoice_number').hide;
	} else {
		cj('tr.crm-case-activity-form-block-invoice_number > td.label').html(obj.attr('label'));
		cj('tr.crm-case-activity-form-block-invoice_number > td.view-value').html(obj.attr('value'));
		// move tr below status
		cj('tr.crm-case-activity-form-block-invoice_number').insertAfter('tr.crm-case-activity-form-block-status_id');
	}
	
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
	// add onChange event to all amount-fields to calculate the total amount
	cj('#dsa_amount, #dsa_briefing, #dsa_airport, #dsa_transfer, #dsa_hotel, #dsa_visa, #dsa_medical, #dsa_other, #dsa_advance').change(function() { processTotal() }); // #dsa_debriefing

	// trigger onChange on dsa_country to retrieve an initial set of locations
	cj('#dsa_country').trigger('change', ['{$form.dsa_location.value[0]']);
	// trigger calculation of total amount
	processTotal();
	createDisplayFields();
	displayControl();
	// trigger onChange on dsa_participant to set dsa_type and credit activity id
	cj('#dsa_participant').trigger('change');
	
	
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
		processTotal();
		return true;
	}
	
	function processDSAOtherChange(elm) {
		amt = cj('#dsa_other').val();
		hide = (cj.trim(amt)=='' || parseNumber(amt)==0);
		cj('#dsa_other_description').prop('disabled', hide);
		processTotal();
	}
	
	function processDSAParticipantChange(elm) {
		//CRM.alert('Participant changed');
		cj('#subject').val(cj('#dsa_participant option:selected').text());
		var data = (cj('#dsa_participant').val() + '|0|0|0').split('|');
		cj('#dsa_participant_id').val(data[0]);
		cj('#dsa_participant_role').val(data[1]);
		cj('#dsa_type').val(data[2]);
		if (data[2]=='3') {
			cj('#credit_act_id').val(data[3]);
		} else {
			cj('#credit_act_id').val('');
		}
		//data[2] -> toggle display
		displayControl();
		return true;
	}
	
	function processTotal() {
		//CRM.alert('Recalculating total');
		cj('#dsa_total').html(
			(
			parseNumeric(cj('#dsa_amount').val()) +
			parseNumeric(cj('#dsa_briefing').val()) +
			//parseNumeric(cj('#dsa_debriefing').val()) +
			parseNumeric(cj('#dsa_airport').val()) +
			parseNumeric(cj('#dsa_transfer').val()) +
			parseNumeric(cj('#dsa_hotel').val()) +
			parseNumeric(cj('#dsa_visa').val()) +
			parseNumeric(cj('#dsa_medical').val()) +
			parseNumeric(cj('#dsa_other').val()) +
			parseNumeric(cj('#dsa_advance').val())
			).toFixed(2)
		);
		return true;
	}
	
	function parseNumeric(num) {
		result = parseFloat(num);
		return isNaN(result)?0:result;
	}
	
	function createDisplayFields() {
		cj('#source_contact_id').before( '<span id="source_contact_id_dsp"></span>' );
		cj('#activity_date_time').before( '<span id="activity_date_time_dsp"></span>' );
		cj('#activity_date_time_time').before( '<span id="activity_date_time_time_dsp"></span>' );
		cj('#dsa_participant').before( '<span id="dsa_participant_dsp"></span>' );
		cj('#dsa_country').before( '<span id="dsa_country_dsp"></span>' );
		cj('#dsa_location').before( '<span id="#dsa_location_dsp"></span>' );
		cj('#dsa_percentage').before( '<span id="dsa_percentage_dsp"></span>' );
		cj('#dsa_days').before( '<span id="dsa_days_dsp"></span>' );
		cj('#dsa_amount').before( '<span id="dsa_amount_dsp"></span>' );
		cj('#dsa_briefing').before( '<span id="dsa_briefing_dsp"></span>' );
		cj('#dsa_airport').before( '<span id="dsa_airport_dsp"></span>' );
		cj('#dsa_transfer').before( '<span id="dsa_transfer_dsp"></span>' );
		cj('#dsa_hotel').before( '<span id="dsa_hotel_dsp"></span>' );
		cj('#dsa_visa').before( '<span id="dsa_visa_dsp"></span>' );
		cj('#dsa_medical').before( '<span id="dsa_medical_dsp"></span>' );
		cj('#dsa_other').before( '<span id="dsa_other_dsp"></span>' );
		cj('#dsa_other_description').before( '<span id="dsa_other_description_dsp"></span>' );
		cj('#dsa_advance').before( '<span id="dsa_advance_dsp"></span>' );
		cj('#status_id').before( '<span id="status_id_dsp"></span>' );
	}
	
	function displayControl() {
		// disable fields depending on status
		var data = (cj('#dsa_participant').val() + '|0|0|0').split('|');
		var restrict = cj('#restrictEdit').val();
		if ((data[2]=='3') && (restrict=='0')) {
			// no restrictions by status, but creditation
			restrict = '1';
		}
		
		// update display areas
		if ((data[2]=='3') && (restrict != '1') && (restrict != '2')) {
			// creditation selected: display amounts paid
			var cr_data = cj('#credit_data').val().split('#');
			var dsa_data = '';
			for (var i=0; i<cr_data.length; i++) {
				if (cr_data[i].split('|')[0] == data[3]) {
					dsa_data = cr_data[i].split('|');
				}
			}
			if (dsa_data == '') {
				dsa_data = '0|0|0|0|0|0|0|0|0|0|0|0|0|0|0|0|0'.split('|');
			}
			cj('#source_contact_id_dsp').html( cj('#source_contact_id').val() );
			cj('#activity_date_time_dsp').html( cj('#activity_date_time').val() );
			cj('#activity_date_time_time_dsp').html( cj('#activity_date_time_time').val() );
			//cj('#dsa_participant_dsp').html( cj('#dsa_participant option:selected').text() );
			//cj('#dsa_country_dsp').html( cj('#dsa_country option:selected').text() );
			//cj('#dsa_location_dsp').html( cj('#dsa_location option:selected').text() );
			cj('#dsa_percentage_dsp').html( dsa_data[4] );
			cj('#dsa_days_dsp').html( dsa_data[5] );
			cj('#dsa_amount_dsp').html( dsa_data[6] );
			cj('#dsa_briefing_dsp').html( dsa_data[7] );
			cj('#dsa_airport_dsp').html( dsa_data[8] );
			cj('#dsa_transfer_dsp').html( dsa_data[9] );
			cj('#dsa_hotel_dsp').html( dsa_data[10] );
			cj('#dsa_visa_dsp').html( dsa_data[11] );
			cj('#dsa_medical_dsp').html( dsa_data[12] );
			cj('#dsa_other_dsp').html( dsa_data[13] );
			cj('#dsa_other_description_dsp').html( dsa_data[14] );
			cj('#dsa_advance_dsp').html( dsa_data[15] );
			//cj('#status_id_dsp').html( cj('#status_id option:selected').text() );
			
		} else if ((restrict == '1') || (restrict == '2')) {
			// payment selected in a non-editable state: display amounts entered earlier
			cj('#source_contact_id_dsp').html( cj('#source_contact_id').val() );
			cj('#activity_date_time_dsp').html( cj('#activity_date_time').val() );
			cj('#activity_date_time_time_dsp').html( cj('#activity_date_time_time').val() );
			cj('#dsa_participant_dsp').html( cj('#dsa_participant option:selected').text() );
			cj('#dsa_country_dsp').html( cj('#dsa_country option:selected').text() );
			cj('#dsa_location_dsp').html( cj('#dsa_location option:selected').text() );
			cj('#dsa_percentage_dsp').html( cj('#dsa_percentage option:selected').text() );
			cj('#dsa_days_dsp').html( cj('#dsa_days').val() );
			cj('#dsa_amount_dsp').html( cj('#dsa_amount').val() );
			cj('#dsa_briefing_dsp').html( cj('#dsa_briefing').val() );
			cj('#dsa_airport_dsp').html( cj('#dsa_airport').val() );
			cj('#dsa_transfer_dsp').html( cj('#dsa_transfer').val() );
			cj('#dsa_hotel_dsp').html( cj('#dsa_hotel').val() );
			cj('#dsa_visa_dsp').html( cj('#dsa_visa').val() );
			cj('#dsa_medical_dsp').html( cj('#dsa_medical').val() );
			cj('#dsa_other_dsp').html( cj('#dsa_other').val() );
			cj('#dsa_other_description_dsp').html( cj('#dsa_other_description').val() );
			cj('#dsa_advance_dsp').html( cj('#dsa_advance').val() );
			cj('#status_id_dsp').html( cj('#status_id option:selected').text() );
		}

		if (restrict == '0') {
			// with client
			cj('tr#with-clients > td.view-value > br').show();
			cj('tr#with-clients > td.view-value > a.crm-with-contact').show();
			// reported by
			cj('#source_contact_id').show();
			cj('#source_contact_id_dsp').hide();
			// date (and time)
			cj('#activity_date_time_display').show();
			cj('#activity_date_time_dsp').hide();
			cj('#activity_date_time_time').show();
			cj('#activity_date_time_time_dsp').hide();
			cj('span.crm-clear-link').show();
			// participant
			cj('#dsa_participant').show();
			cj('#dsa_participant_dsp').hide();
			// country
			cj('tr.crm-case-activity-form-block-dsa_country').show();
			cj('#dsa_country').show();
			cj('#dsa_country_dsp').hide();
			// location
			cj('tr.crm-case-activity-form-block-dsa_location').show();
			cj('#dsa_location').show();
			cj('#dsa_location_dsp').show();
			// percentage
			cj('#dsa_percentage').show();
			cj('#dsa_percentage_dsp').hide();
			// days
			cj('#dsa_days').show();
			cj('#dsa_days_dsp').hide();
			// amount
			cj('#dsa_amount').show();
			cj('#dsa_amount_dsp').hide();
			// briefing
			cj('#dsa_briefing').show();
			cj('#dsa_briefing_dsp').hide();
			// airport
			cj('#dsa_airport').show();
			cj('#dsa_airport_dsp').hide();
			// transfer
			cj('#dsa_transfer').show();
			cj('#dsa_transfer_dsp').hide();
			// hotel
			cj('#dsa_hotel').show();
			cj('#dsa_hotel_dsp').hide();
			// visa
			cj('#dsa_visa').show();
			cj('#dsa_visa_dsp').hide();
			// medical
			cj('#dsa_medical').show();
			cj('#dsa_medical_dsp').hide();
			// other
			cj('#dsa_other').show();
			cj('#dsa_other_dsp').hide();
			cj('#dsa_other_description').show();
			cj('#dsa_other_description_dsp').hide();
			cj('#dsa_other_description ~ .grippie').show();
			// advance
			cj('#dsa_advance').show();
			cj('#dsa_advance_dsp').hide();
			// status
			cj('#status_id').show();
			cj('#status_id_dsp').hide();
			// submit
			//cj('span.crm-button_qf_Activity_upload').remove();
			// cancel
			//cj('span.crm-button_qf_Activity_cancel').addClass('crm-button-type-upload').removeClass('crm-button-type-cancel');
		}
		
		if ((restrict == '1') || (restrict == '2')) {
			// with client
			cj('tr#with-clients > td.view-value > br').hide();
			cj('tr#with-clients > td.view-value > a.crm-with-contact').hide();
			// reported by
			cj('#source_contact_id').hide();
			cj('#source_contact_id_dsp').show();
			// date (and time)
			cj('#activity_date_time_display').hide();
			cj('#activity_date_time_dsp').show();
			cj('#activity_date_time_time').hide();
			cj('#activity_date_time_time_dsp').show();
			cj('span.crm-clear-link').hide();
			// participant
			cj('#dsa_participant').hide();
			cj('#dsa_participant_dsp').show();
			if (data[2]=='3') {
				cj('tr.crm-case-activity-form-block-dsa_country').hide();
				cj('tr.crm-case-activity-form-block-dsa_location').hide();
			} else {
				// country
				cj('tr.crm-case-activity-form-block-dsa_country').show();
				cj('#dsa_country').hide();
				cj('#dsa_country_dsp').show();
				// location
				cj('tr.crm-case-activity-form-block-dsa_location').show();
				cj('#dsa_location').hide();
				cj('#dsa_location_dsp').hide();
			}
			
			// percentage
			cj('#dsa_percentage').hide();
			cj('#dsa_percentage_dsp').show();
			// days
			cj('#dsa_days').hide();
			cj('#dsa_days_dsp').show();
			// amount
			cj('#dsa_amount').hide();
			cj('#dsa_amount_dsp').show();
			// briefing
			cj('#dsa_briefing').hide();
			cj('#dsa_briefing_dsp').show();
			// airport
			cj('#dsa_airport').hide();
			cj('#dsa_airport_dsp').show();
			// transfer
			cj('#dsa_transfer').hide();
			cj('#dsa_transfer_dsp').show();
			// hotel
			cj('#dsa_hotel').hide();
			cj('#dsa_hotel_dsp').show();
			// visa
			cj('#dsa_visa').hide();
			cj('#dsa_visa_dsp').show();
			// medical
			cj('#dsa_medical').hide();
			cj('#dsa_medical_dsp').show();
			// other
			cj('#dsa_other').hide();
			cj('#dsa_other_dsp').show();
			cj('#dsa_other_description').hide();
			cj('#dsa_other_description_dsp').show();
			cj('#dsa_other_description ~ .grippie').hide();
			// advance
			cj('#dsa_advance').hide();
			cj('#dsa_advance_dsp').show();
		}
		
		if (restrict == '2') {
			// status
			cj('#status_id').hide();
			cj('#status_id_dsp').show();
			// submit
			//cj('span.crm-button_qf_Activity_upload').remove();
			// cancel
			//cj('span.crm-button_qf_Activity_cancel').addClass('crm-button-type-upload').removeClass('crm-button-type-cancel');
		}
		
		return true;
	}
	
</script>
{/literal}