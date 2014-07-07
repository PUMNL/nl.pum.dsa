nl.pum.dsa
==========

Extension for DSA implementation at PUM Netherlands senior experts

Adds case activity type "DSA". Make sure to link the DSA activity to your cases in your case management xml files.
Adds option group "DSA Percentage" (name "dsa_percentage"), containing "0%" (value 0), "20%" (value 20) and "100%" (value 100).
Adds additional activity status "Payable" (name "dsa_payable") and "Paid" ("dsa_paid").

Adds 5 tables
- civicrm_dsa_convert
- civicrm_dsa_batch
- civicrm_dsa_rate
- civicrm_country_pum
- civicrm_dsa_compose
- civicrm_dsa_payment (update 1003)

Adds "DSA" submenu under "Administer" menu
DSA submenu contains 3 buttons for DSA-rate control
- Convert UN files to CSV 
- Import locations and rates
- View imported batches

Adds a page "DSAimport"
This page can be called using ?action=convert, upload, result or read:

"convert"
imports the original UN locations and rates files (country in ICSC format, rate in USD) into civicrm_dsa_convert and
presents the data as CSV in a return field (country in ISO2 format, rate in USD)
country conversion is based on civicrm_country_pum

"import"
imports filtered/converted/reviewed CSV (country in ISO2 format, rate in EUR) as a batch into civicrm_dsa_batch and civicrm_dsa_rate

"result"
acts as handler and return screen after submitting "convert" or "import" data

"read"
displays imported DSA batches (maintenance not implemented yet)

Adds a set of additional fields to the activity "DSA". These are not "custom fields" as known in CiciCRM.
This set of fields is stored in civicrm_dsa_compose. If present, the field "original_id" in the case is used to tie the fields to the case, otherwise the case' id is used.

A user is offered to add a DSA activity (if defined in xml).
On the activity the user is supposes to select a participant (implicit selection of payment type and participant role).
Only one DSA activity per participant (!) will be allowed in any status other than 'dsa_paid', 'Cancelled' or 'Not Required'.
Once processed (status is automatically set to 'dsa_paid', another DSA activity (likely for creditation or additional payment) can be made.
If multiple DSA activities are being edited into a state that would lead to having more than one 'open' DSA activity, for a single participant, validation will block the ones causing a violation.



** TO DO **
* test install process
* scheduled export of DSA activities / promotion to status "dsa_paid"
* control feature to export only on certain days on/after a specified date
* form adjustments regarding creditation
* white overlay screens for activity, status change etc.
* batch management (filling out start- / end dates)
* disable move to case, copy to case, delete / conditional disabling edit.
* maintenance option for civicrm_pum_country: create / remove / edit additional country info (unless fully replaced by a country card feature)
***


KNOWN ISSUES:
Expected upload directory: <site root>/upload (like <site root>/modules)
