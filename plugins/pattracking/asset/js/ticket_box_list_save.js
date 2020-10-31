jQuery(document).ready(function(){
    Dropzone.autoDiscover = false;
    jQuery(document).ajaxComplete(function (event, xhr, settings) {
        if ('action=wpsc_tickets&setting_action=create_ticket' == settings.data) {

            var dropzoneOptions = {
                url: "test.php",
                autoProcessQueue: false,
                addRemoveLinks: true,
                uploadMultiple: false,
                maxFiles: 1,
                acceptedFiles: '.xlsx',
                accept: function (file, done) {
                    jQuery('#file_upload_cr').val(1);
                    wpsc_spreadsheet_new_upload('attach_16','spreadsheet_attachment', file);
                },
                init: function () {
                    this.on("maxfilesexceeded", function() {
                        if (this.files[1]!=null){
                            this.removeFile(this.files[0]);
                        }
                    });
                    this.on("error", function (file) {
                        if (!file.accepted) this.removeFile(file);
                    });
                }
            };
            var uploader = document.querySelector('#dzBoxUpload');
            var newDropzone = new Dropzone(uploader, dropzoneOptions);            
        }
    });
});


/* Removes data from the box datatable if there is any error */
function clearBoxTable() {
    var datatable = jQuery('#boxinfodatatable').DataTable();
    datatable.clear().draw();
}

/* Converts datatable data into json */
jQuery.fn.toJson = function () {
    try {
        if (!this.is('table')) {
            return;
        }

        var results = [],
        headings = [];

        var table = jQuery('#boxinfodatatable').DataTable();

        /* Fetch heading */
        this.find('thead tr th').each(function (index, value) {
            headings.push(jQuery(value).text());
        });

        /* Fetch data */
        table.rows().every(function (rowIdx, tableLoop, rowLoop) {
            var row = {};
            var data = this.data();
            headings.forEach(function (key, index) {
                var value = data[index];
                row[key] = value;
            });
            results.push(row);
        });

        return results;

    } catch (ex) {
        alert(ex);
    }
}

// Box list file validation on submit
jQuery(document).on('click', '#wpsc_create_ticket_submit', function() {
    if(0 === jQuery('#file_upload_cr').val() || "0" === jQuery('#file_upload_cr').val()){
        alert('Please upload the excel sheet');
        return false;
    }
});


// Upload boxlist document, and create the data table
function wpsc_spreadsheet_new_upload(id, name, fileSS) {

    jQuery('#attachment_upload').unbind('change');

    jQuery.fn.dataTable.ext.errMode = 'none';
    var flag = false;
    var file = fileSS;
    jQuery('#attachment_upload').val('');

    var file_name_split = file.name.split('.');
    var file_extension = file_name_split[file_name_split.length - 1];
    file_extension = file_extension.toLowerCase(); 

    var allowedExtensionSetting = ["xls", "xlsx"];
    if (!flag && (jQuery.inArray(file_extension, allowedExtensionSetting) <= -1)) {
        flag = true;
        alert('Attached file type not allowed!');
    }

    var current_filesize = file.size / 1000000;
    if (current_filesize > attachment_info['max_filesize'] ) {
        flag = true;
        alert('File size exceed allowed limit!');
    }

    //No file
    if( flag == true ) {
        jQuery('#file_upload_cr').val(0);
        var _ref;
        return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
    }

    if ( ! flag ) {

        jQuery('.row.wpsp_spreadsheet').each(function (i, obj) {
            obj.remove();
        });

        // Progress bar
        var html_str = '<div class="row wpsp_spreadsheet">' +
                            '<div class="progress" style="float: none !important; width: unset !important;">' +
                                '<div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%">' + file.name + '</div>' +
                            '</div>' +
                            '<img onclick="attachment_cancel(this);clearBoxTable()" class="attachment_cancel" src="' + attachment_info['close_image'] + '" style="display:none;" />' +
                        '</div>';

        jQuery('#' + id).append(html_str);

        var attachment = jQuery('#' + id).find('.wpsp_spreadsheet').last();

        var data = new FormData();
        data.append('file', file);
        data.append('arr_name', name);
        data.append('action', 'wpsc_tickets');
        data.append('setting_action', 'upload_file');
        data.append('nonce', jQuery('#wpsc_nonce').val().trim());

        // Read file and provide json response
        jQuery.ajax({
            type: 'post',
            url: wpsc_admin.ajax_url,
            data: data,
            xhr: function () {
                var xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener("progress", function (evt) {
                    if (evt.lengthComputable) {
                        var percentComplete = Math.floor((evt.loaded / evt.total) * 100);
                        jQuery(attachment).find('.progress-bar').css('width', percentComplete + '%');
                    }
                }, false);
                return xhr;
            },
            processData: false,
            contentType: false,
            success: function (response) {
                jQuery('#boxinfodatatable').show();
                var return_obj = JSON.parse(response);
                jQuery(attachment).find('.attachment_cancel').show();

                if (parseInt(return_obj.id) != 0) {
                    jQuery(attachment).append('<input type="hidden" name="' + name + '[]" value="' + return_obj.id + '">');
                    jQuery(attachment).find('.progress-bar').addClass('progress-bar-success');

                    //Start of new Datatable code
                    var datatable = jQuery('#boxinfodatatable').DataTable({
                        "scrollX": "100%",
                        "scrollXInner": "110%"
                    });

                    datatable.clear().draw();

                    var FR = new FileReader();
                    FR.onload = function (e) {

                    var data = new Uint8Array(e.target.result);
                    var workbook = XLSX.read(data, {
                        type: 'array'
                    });

                    var firstSheet = workbook.Sheets[workbook.SheetNames[0]];

                    var result = XLSX.utils.sheet_to_json(firstSheet, {
                        header: 1, raw: false
                    });

                    var arrayOfData = JSON.stringify(result);
                    var parsedData = JSON.parse(arrayOfData);
                    var arrayLength = Object.keys(parsedData).length;

                    if (parsedData[1][0] !== undefined && parsedData[1][16] !== undefined) {
                            let prev_box = '';
                            let prev_epa_contact = '';
                            let prev_program_office = '';
                            let prev_record_schedule = '';

                            let arr_fields = ['Box', 'Title', 'Date', 'Close Date', 'Program Office', 'Index Level', 'Essential Records'];
                            let date_time_reg = /^(0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])[\/\-]\d{4} ([0-1]?[0-9]|2[0-4]):([0-5][0-9])(:[0-5][0-9])$/;

                            /* Loop through data */
                            for (var count = 1; count < arrayLength; count++) {

                                if(count > 1 && parsedData[count][0] == null && parsedData[count][15] == null) {
                                    continue;
                                }

                                /* Required fields check. */
                                if ( flag != true && count > 1 && ( ( invalid_index = [parsedData[count][0], parsedData[count][2], parsedData[count][3], parsedData[count][10], parsedData[count][14], parsedData[count][15], parsedData[count][16]].indexOf( null ) ) > -1 ) ) {

                                    alert("Invalid value for column " + arr_fields[invalid_index] + " for record " + (count + 1) + ". This field is required." );
                                    flag = true;
                                
                                }

                                // Validate date
                                if( flag != true && count > 1 && date_time_reg.test( parsedData[count][10] ) == false ) {
                                    alert("Invalid Close Date for record " + (count + 1) );
                                    flag = true;
                                }

                                // Validate date
                                if( flag != true && count > 1 && date_time_reg.test( parsedData[count][3] ) == false ) {
                                    alert("Invalid Date for record " + (count + 1) );
                                    flag = true;
                                }

                                // Box ID validation
                                if( flag != true && count > 1 && (parsedData[count][0] == null || parsedData[count][0] === undefined)) {
                                    alert('Box ID value "'+parsedData[count][0]+'" seems incorrect for the record number '+ (count + 1) );
                                    flag = true;
                                }

                                 // Index level validation
                                if(flag != true && count > 1 && (parsedData[count][15].toLowerCase() != 'file' && parsedData[count][15].toLowerCase() != 'folder')){
                                    alert('Index level value "'+parsedData[count][15]+'" seems incorrect for the record number '+ (count + 1) );
                                    flag = true;
                                }

                                // Epa contact, program office, record no validation
                                if(flag != true && count > 1 && ( prev_box != '' && prev_box === parsedData[count][0] ) && ( prev_epa_contact !== parsedData[count][11] || prev_program_office !== parsedData[count][14] || prev_record_schedule !== parsedData[count][7] ) ) {

                                    _column = ( prev_epa_contact !== parsedData[count][11] ? ' EPA Contact ' : ( prev_program_office !== parsedData[count][14] ? ' Program Office ' : ( prev_record_schedule !== parsedData[count][7] ? ' Record Schedule & Item Number ' : '' ) ) );

                                    alert("Invalid value in column " + _column + " for record " + (count + 1) );
                                    flag = true;
                                }

                                // Clear table if err
                                if( flag == true ) {

                                    datatable.clear().draw();
                                    jQuery('#file_upload_cr').val(0);

                                    jQuery('.row.wpsp_spreadsheet').each(function (i, obj) {
                                        obj.remove();
                                    });

                                    var _ref;
                                    return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
                                }

                                // Add record to datatable if no error
                                if (parsedData[count] !== undefined && parsedData[count].length > 0 && parsedData[count][0].toString().trim() != "Box") {

                                    prev_box = parsedData[count][0];
                                    prev_epa_contact = parsedData[count][11];
                                    prev_program_office = parsedData[count][14];
                                    prev_record_schedule = parsedData[count][7];

                                    datatable.row.add([
                                        parsedData[count][0],
                                        parsedData[count][1],
                                        parsedData[count][2],
                                        parsedData[count][3],
                                        parsedData[count][4],
                                        parsedData[count][5],
                                        parsedData[count][6],
                                        parsedData[count][7],
                                        parsedData[count][8],
                                        parsedData[count][9],
                                        parsedData[count][10],
                                        parsedData[count][11],
                                        parsedData[count][12],
                                        parsedData[count][13],
                                        parsedData[count][14],
                                        parsedData[count][15],
                                        parsedData[count][16],
                                        parsedData[count][17], // For Folder/Filename field value
                                        parsedData[count][18] // For Tags field value
                                    ]).draw().node();
                                }
                            }
                        } else {
                            alert("Spreadsheet is not in the correct format! Please try again.");
                            jQuery('.row.wpsp_spreadsheet').each(function (i, obj) {
                                obj.remove();
                            });
                            flag = true;

                            datatable.clear().draw();

                            jQuery('#file_upload_cr').val(0);
                            var _ref;
                            return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
                        }
                    };
                    FR.readAsArrayBuffer(file);
                    document.getElementById("boxdisplaydiv").style.display = "block";

                    //End of new Datatable code

                } else {
                    jQuery(attachment).find('.progress-bar').addClass('progress-bar-danger');
                }
            }
        });

        // Upload excel
        var form_data = new FormData();
        form_data.append('file', fileSS);
        form_data.append('action', 'move_excel_file');
        jQuery.ajax({
            url: wpsc_admin.ajax_url,
            type: 'post',
            contentType: false,
            processData: false,
            data: form_data,
            success: function (response) {
                console.log("Excel uploaded successfully");
            },  
            error: function (response) {
                console.log("Excel does not uploaded successfully");
            }
        });

    }

}