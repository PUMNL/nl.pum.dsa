{**
 * FILE: dsa/templates/representative_payment_section.tpl
 *}

{* template block that contains the new field *}
{$form.dsa_type.html}
{$form.dsa_participant_id.html}
{$form.dsa_participant_role.html}
{$form.invoice_number.html}
{$form.invoice_rep.html}
{$form.restrictEdit.html}
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
	<tr class="crm-case-activity-form-block-dsa_amount">
        <td class="label">{$form.dsa_amount.label}</td>
		<td class="view-value">{$form.dsa_amount.html}</td>
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
	// hide tag
	cj('tr.crm-case-activity-form-block-tag').hide();
	
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
	
	// add onChange event to dsa_participant field to build a list of selectable locations
	cj('#dsa_participant').change(function() { processDSAParticipantChange(this) });
	
	createDisplayFields();
	displayControl();
	
	// trigger onChange on dsa_participant to set dsa_type and credit activity id
	cj('#dsa_participant').trigger('change');
	
	
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
	
	function createDisplayFields() {
		cj('#source_contact_id').before( '<span id="source_contact_id_dsp"></span>' );
		cj('#activity_date_time').before( '<span id="activity_date_time_dsp"></span>' );
		cj('#activity_date_time_time').before( '<span id="activity_date_time_time_dsp"></span>' );
		cj('#dsa_participant').before( '<span id="dsa_participant_dsp"></span>' );
		cj('#dsa_amount').before( '<span id="dsa_amount_dsp"></span>' );
		cj('#status_id').before( '<span id="status_id_dsp"></span>' );
	}
	
	function displayControl() {
		/* note:
		 * data[2] = '1' for normal payments
		 * data[2] = '3' for creditation (never used in Representative payments)
		 * data[2] = '2' for settlement  (never used in Representative payments)
		 * restrict = 0 when all fields can be edited (no restriction)
		 * restrict = 1 when only status can be edited (status dsa_payable, or creditation)
		 * restrict = 2 when no fields can be edited (status dsa_paid)
		 */
		// disable fields depending on status
		var data = (cj('#dsa_participant').val() + '|0|0|0').split('|');
		var restrict = cj('#restrictEdit').val();
		if ((data[2]=='3') && (restrict=='0')) {
			// no restrictions by status, but creditation
			restrict = '1';
		}
		
		// update display areas
		if ((data[2]=='3') && (restrict != '2')) { // && (restrict != '1')
			// creditation selected: display amounts paid
/*
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
			cj('#dsa_amount_dsp').html( dsa_data[6] );
			//cj('#status_id_dsp').html( cj('#status_id option:selected').text() );
*/
		} else if ((restrict == '1') || (restrict == '2')) {
			// payment selected in a non-editable state: display amounts entered earlier
			cj('#source_contact_id_dsp').html( cj('#source_contact_id').val() );
			cj('#activity_date_time_dsp').html( cj('#activity_date_time').val() );
			cj('#activity_date_time_time_dsp').html( cj('#activity_date_time_time').val() );
			cj('#dsa_participant_dsp').html( cj('#dsa_participant option:selected').text() );
			cj('#dsa_amount_dsp').html( cj('#dsa_amount').val() );
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
			// amount
			cj('#dsa_amount').show();
			cj('#dsa_amount_dsp').hide();
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
			// amount
			cj('#dsa_amount').hide();
			cj('#dsa_amount_dsp').show();
		}
		
		if (restrict == '1') {
			// status
			cj('#status_id').show();
			cj('#status_id_dsp').hide();
		} else if (restrict == '2') {
			// status
			cj('#status_id').hide();
			cj('#status_id_dsp').show();
			// submit
			cj('span.crm-button_qf_Activity_upload').remove();
			// cancel
			//cj('span.crm-button_qf_Activity_cancel').addClass('crm-button-type-upload').removeClass('crm-button-type-cancel');
		}
		
		return true;
	}
	
	
	
</script>
{/literal}