PATT v0.2.1 Beta
==========
PATT - Paper Asset Tracking Tool Plugin for Wordpress

The Paper Asset Tracking Tool (PATT) developed by (ERMD) in collaboration with other Agency records management experts will further enhance Records Management capabilities of EPA through digitization.
PATT aims to assist the record digitization initiative by implementing a robust asset management tool. This asset management tool will be critical to the ongoing success of the centralized records digitization centers being stood up by ERMD. This asset management tool (PATT) will allow custodians of paper records to submit new digitization request. Once these requests are submitted, the records custodian will be able to track the status of the request from receipt to digitization to storage and eventual destruction. The asset management tool will allow digitization center managers to prioritize and assign digitization tasks to scanner operators. In addition, the asset management tool PATT will have the capability to send digitized records directly to ECMS where they will ultimately reside. 

## PATT version 0.2.1 Beta Overview:
PATT version 0.2.1 introduces a patch to the ECMS content ingestion services cron job. This patch enables multipart sending of records to ECMS. In addition, this patch saves the object ID that is outputed by the endpoint to the PATT database. The object ID is used to generate a direct download link on the folder/file details page of PATT.

## Previous PATT Versions
Version 0.2.0

* Box status and box user auto-assignment.
* Associated Documents widget allows for upload of Destruction Approval, FOIA, Litigation hold and Congressional documents.
* Buildout of Shipping Status Editor.
* Fixed unauthorized destruction bug

* Fixed freeze approval multiselect bug

* Added 10 year retention filters to record schedule dropdowns and Excel Spreadsheet

* Added schedule title to program office and record schedule dropdown

* Critical priority restricted to admin only

* Accordion added to box details editor to restrict view of program office/record schedule editor

* Insert function to change Media to Documents in left navigation

* Changed Approval to Assoc. Documents including updating icon

* Added ability to edit tracking numbers in Shipping Status Editor

* Added ID column to Shipping Status Editor dashboard

* Restricted deletion to request in cancelled status or request that contain boxes that are all in the dispositioned status

* Shortened tracking number on return, recall and shipping editor dashboard to resolve width issues

* Converted Program office to typeahead dropdown in recall and return dashboard.

* Fixed search bug on recall and return dashboard.

Version 0.1.0


* Initial release of PATT for user preview testing. Includes the initial buildout of the Box Dashboard, Folder/File Dashboard, Recall Dashboard, Return Dashboard, RFID Dashboard, Barcode Scanning.

## EPA Disclaimer

The United States Environmental Protection Agency (EPA) GitHub project code is provided on an "as is" basis and the user assumes responsibility for its use. EPA has relinquished control of the information and no longer has responsibility to protect the integrity , confidentiality, or availability of the information. Any reference to specific commercial products, processes, or services by service mark, trademark, manufacturer, or otherwise, does not constitute or imply their endorsement, recommendation or favoring by EPA. The EPA seal and logo shall not be used in any manner to imply endorsement of any commercial product or activity by EPA or the United States Government.    [<img src="https://licensebuttons.net/p/mark/1.0/88x31.png" width="50" height="15">](https://creativecommons.org/publicdomain/zero/1.0/)