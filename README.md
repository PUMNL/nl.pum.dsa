nl.pum.dsa
==========

Extension for DSA implementation at PUM Netherlands senior experts

Adds 3 tables
- civicrm_dsa_convert
- civicrm_dsa_batch
- civicrm_dsa_rate
- civicrm_country_pum

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
