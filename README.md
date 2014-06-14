nl.pum.dsa
==========

Extension for DSA implementation at PUM Netherlands senior experts

Adds 5 tables
- civicrm_dsa_convert
- civicrm_dsa_batch
- civicrm_dsa_rate
- civicrm_country_pum
- civicrm_dsa_compose

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


** Important: **
* Requires a case activity called "DSA" (NOT automatically added yet)
* Requires an option group "DSA Rate" (name "dsa_rate"), containing e.g. "0%" (value 0), "20%" (value 20) and "100%" (value 100).
* This option group is NOT automatically added yet.
* Make sure to link the DSA activity to your cases in your case management xml files.
* Requires additional values for option group "Activity Status": "Payable" (mandatory name "dsa_payable", unique value e.g. 1501), "Paid" (mandatory name "dsa_paid", unique value e.g. 1502).
* Make sure both statusses are set active: they will be removed from any non-DSA activity status list by this extension.
* These options are NOT automatically added yet.
***


Adds a set of additional fields to the activity "DSA".
This set of fields is stored in civicrm_dsa_compose. If present, the field "original_id" in the case is used to tie the fields to the case, otherwise the case' id is used.

A user is offered to add a DSA activity (if defined in xml). However, if a DSA activity is present in any status other than 'dsa_paid', 'Cancelled' or 'Not Required',
The option to create a new DSA activity is withdrawn from the list of options.
Once processed (status is set to 'dsa_paid', another DSA activity (likely for creditation or additional paiment) can be made.
If multiple DSA activities are being edited into a state that would lead to having more than one 'open' DSA activity, validation will block the ones causing a violation.

** TO DO **
* scheduled export of DSA activities / promotion to status "dsa_paid'
* form adjustments regarding creditation
* white overlay screens for activity, status change etc.
* batch management (start- / end dates)
* disable move to case, copy to case, delete etc.
***


KNOWN ISSUES:
Expected upload directory: <site root>/upload (like <site root>/modules)
