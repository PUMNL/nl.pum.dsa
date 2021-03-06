nl.pum.dsa
==========

Extension for DSA implementation at PUM Netherlands senior experts

Adds case activity type "DSA".

**Make sure to link the DSA activity to your cases in your case management xml files**.

Adds additional activity status "Payable" (name "dsa_payable") and "Paid" ("dsa_paid").

Adds tables

- civicrm_dsa_convert

- civicrm_dsa_batch

- civicrm_dsa_rate

- civicrm_country_pum

- civicrm_dsa_compose

- civicrm_representative_compose

- civicrm_dsa_payment


Adds "DSA" submenu under "Administer" menu
DSA submenu contains 3 buttons for DSA-rate control
- Convert UN files to CSV
- Import locations and rates
- View imported batches

Adds a page "DSAimport"
This page can be called using ?action=convert, upload, result or read:

Adds following option groups:

- "dsa\_percentage" (names/values non-critical),

- "general\_ledger" (names for the values are mandatory in the code) and

- "dsa_configuration" (names for the values are mandatory in the code)

**Please review these added option values anter install / upgrade**


Adds additional actiovity statusses to option_group "activity_status":

- "dsa\_payable" (name is mandatory in the code)

- "dsa\_paid" (name is mandatory in the code)


**DSAimport actions:**

*"convert"* imports the original UN locations and rates files (country in ICSC format, rate in USD) into civicrm\_dsa\_convert and
presents the data as CSV in a return field (country in ISO2 format, rate in USD)
country conversion is based on civicrm\_country\_pum

*"import"* imports filtered/converted/reviewed CSV (country in ISO2 format, rate in EUR) as a batch into civicrm\_dsa\_batch and civicrm\_dsa\_rate

*"result"* acts as handler and return screen after submitting "convert" or "import" data

*"read"* displays imported DSA batches (maintenance not implemented yet)

**DSA activity** / **Representative payment**

Additional sets of fields are added to the activity "DSA" and Representative payment. These are no "custom fields" as known in CiviCRM! The additional fields are stored in civicrm\_dsa\_compose or civicrn\_representative\_compose. If present, the field "original\_id" in the activity is used to tie the fields to the activity, otherwise the activities id is used.

If the activity status is set to payable (dsa\_payable) a scheduled job may pick it up for further processing. Once processed (status is automatically set to 'dsa\_paid'), another DSA activity can be made for creditation of the very same amounts. Representative payments can not be credited.
If multiple DSA activities are being edited into a state that would lead to having more than one 'open' DSA activity for a single participant, validation will block the ones causing a violation.

CONFIGURATION
=============

Please make adjustments to the option groups after installation:

- DSA Configuration

- DSA Percentage

- Representative Payment Configuration

- Representative Payment Relationships

In the CiviCRM section of the permissions overview, please set/review:

- CiviCRM DSA: create DSA activity

- CiviCRM DSA: edit DSA activity

- CiviCRM DSA: approve DSA activity

- CiviCRM DSA: create Representative payment activity

- CiviCRM DSA: edit Representative payment activity

- CiviCRM DSA: approve Representative payment activity

Under Rosters in the top menu bar, please adjust the scheduling for DSA- and Representative payments. Note that you will need the privileges registered in the roster definitions!



WARNING
=======
Extension relies on custom fields, generated by nl.pum.generic.
2 Of these fields (Bank ISO Country code and Accountholder country) appeared useless
The current code assumes presence of Bank Country ISO code and Account holder country as country selection fields.
Please adjust line 200 and 208 in API DSA:ProcessPayments if put to use before the fields have been properly implemented

***

**TO DO**

* test install process
* scheduled export of DSA activities / promotion to status "dsa_paid"
* control feature to export only on certain days on/after a specified date
* white overlay screens for activity, status change etc.
* batch management (filling out start- / end dates)
* disable move to case, copy to case, delete / conditional disabling edit.
* maintenance option for civicrm_pum_country: create / remove / edit additional country info (unless fully replaced by a country card feature)
* amount overviews (screen, B.I. purposes?)
* payment overview per expert (mail)
* payment overview per payment run (mail)
* fill out missing columns (e.g. account number, account holder details, bank details, 'FactuurNumberYear')
* auto-creation of a scheduled job to trigger ProcessPayment. Currently we rely on manual creation / manual trigger

* Add configuration field under Administer -> DSA -> DSA Managers Operations to specify the minimum approval amount for DSA Managers (currently hardcoded set to 2000 euro in:
    * api/v3/Dsa/ProcessPayments.php
    * CRM/Dsa/Page/MyDSA.php
    * templates/CRM/Dsa/Page/MyDSA.tpl
***


**Additional notes:**

As of release 1008, the term "outfit allowance" is no longer used and replaced my "medical" expense. However, general ledger code "gl_outfit" is still in use for that purpose (in both the API "DSA/ProcessPayment" and installation script "dsa.optiongroup.inc.php")
As of release 1015, the extension requires extension nl.pum.roster to be active. It is used to verify if scheduled jobs DSA- and Representative payments are allowed to extecute.
As of version 1.1, the extension relies on the permisions CiviCRM DSA create/edit/approve DSA activity
Any errors raised in ProcessPayment (both DSA and Representative payments) are written to the standard civicrm log files.

**Known issues:**

Expected upload directory: <site root>/upload (like <site root>/modules)

