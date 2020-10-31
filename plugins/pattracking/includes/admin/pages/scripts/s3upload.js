
var obj_s3link;
var obj_key;
var obj_size;
var obj_type;
var obj_name;


function S3MultiUpload(file) {
    //Specifies 22 mb chunks convert to bytes
    this.PART_SIZE = 22 * 1024 * 1024;
//     this.SERVER_LOC = '?'; // Location of the server
    this.SERVER_LOC = 'https://086.info/wordpress3/wp-content/plugins/pattracking/includes/admin/pages/scripts/index.php'; // Location of the server // working
//     this.SERVER_LOC = 'https://086.info/wordpress3/wp-content/plugins/pattracking/includes/admin/pages/scripts/s3_upload_wrapper.php'; 
//    this.SERVER_LOC = '/home/acy3/public_html/wordpress3/wp-content/plugi…tracking/index.php'; // Location of the server    
	console.log('SETTINGS');
	const url = window.location.pathname;
	console.log({url:url});
    this.completed = false;
    this.file = file;
    this.fileInfo = {
        name: this.file.name,
        type: this.file.type,
        size: this.file.size,
        lastModifiedDate: this.file.lastModifiedDate
    };
    this.sendBackData = null;
    this.uploadXHR = [];
    // Progress monitoring
    this.byterate = []
    this.lastUploadedSize = []
    this.lastUploadedTime = []
    this.loaded = [];
    this.total = [];
    this.parts = []; // pre-partsCompleted parts
    this.partsCompleted = [false];
    this.partsInProgress = [false];
    
    obj_name = this.file.name;
}

Array.prototype.remove = function() {
    var what, a = arguments, L = a.length, ax;
    while (L && this.length) {
        what = a[--L];
        while ((ax = this.indexOf(what)) !== -1) {
            this.splice(ax, 1);
        }
    }
    return this;
};

console.log('s3upload.js loaded');
/**
 * Creates the multipart upload
 */
S3MultiUpload.prototype.createMultipartUpload = function() {
    var self = this;
    var unixnow = Math.floor(Date.now() / 1000);
    
    // D E B U G
    console.log({unixnow:unixnow});
    console.log({self_fileInfo:self.fileInfo});
    console.log({SERVER_LOC:self.SERVER_LOC});
    obj_size = self.fileInfo.size;
    obj_type = self.fileInfo.type;
    
    $.post(self.SERVER_LOC, {
        command: 'create',
        fileInfo: self.fileInfo,
        //key: self.file.lastModified + '_' + self.file.name
        key: unixnow + '_' + self.file.name.replace(/\s/g, '')
    }).done(function(data) {
        console.log('Done');
        console.log({data:data});
        self.sendBackData = data;
        document.getElementById("uploadId").value = self.sendBackData.uploadId;
        document.getElementById("objectkey").value = 'Object key: '+self.sendBackData.key;
        console.log(self.sendBackData.uploadId);
        console.log(self.sendBackData.key);
        obj_key = self.sendBackData.key;
        self.uploadParts();
    }).fail(function(jqXHR, textStatus, errorThrown) {
	    console.log('FAILED create');
        self.onServerError('create', jqXHR, textStatus, errorThrown);
    });
};


/** private */
S3MultiUpload.prototype.resumeMultipartUpload = function(uploadId) {
    var self = this;
    self.sendBackData = {
        uploadId: uploadId,
        key: self.file.lastModified + self.file.name
    };

    $.post(self.SERVER_LOC, {
        command: 'listparts',
        sendBackData: self.sendBackData
    }).done(function(data) {
        
        if (data.parts) {
            var parts = data.parts
            console.log(parts)
        }

        for (var i = 0; i < parts.length; i++) {
            self.loaded[parts[i].PartNumber] = parts[i].Size
            self.total[parts[i].PartNumber] = parts[i].Size
            self.partsCompleted[parts[i].PartNumber] = true
        }

        self.uploadParts();

    }).fail(function(jqXHR, textStatus, errorThrown) {
        self.onServerError('listparts', jqXHR, textStatus, errorThrown);
    });
};

/** private */
S3MultiUpload.prototype.uploadParts = function() {
    var blobs = this.blobs = [], promises = [];
    var partNumbers = this.partNumbers = []
    var start = 0;
    var end, blob;
    var partNum = 0;


    while(start < this.file.size) {
        end = Math.min(start + this.PART_SIZE, this.file.size);
		filePart = this.file.slice(start, end);
        
        // this is to prevent push blob with 0Kb
        if (filePart.size > 0) {
            this.partsInProgress.push(false)
            partNumbers.push(partNum+1)
        }

        if (filePart.size > 0 && !this.partsCompleted[partNum+1]) {
            
            blobs.push(filePart);

            //console.log('Getting presigned URL for part ' + (partNum+1))
            promises.push(this.uploadXHR[filePart]=$.post(this.SERVER_LOC, {
                command: 'part',
                sendBackData: this.sendBackData,
                partNumber: partNum+1,
                contentLength: filePart.size
            }));

        }
        start = this.PART_SIZE * ++partNum;
    }
    $.when.apply(null, promises)
     .then(this.sendAll.bind(this), this.onServerError)
     .done(this.onPrepareCompleted);

     console.log(this.partsInProgress)
     console.log(this.partNumbers)
};

/**
 * Sends all the created upload parts in a loop
 */
S3MultiUpload.prototype.sendAll = function() {
    var blobs = this.blobs;
    var length = blobs.length;
    var data = Array.from(arguments)

    if (length==1) {
        //console.log("Sending object")
        this.sendToS3(data[0], blobs[0], 0, 1);
    } else {
        for (var i = 0; i < length; i++) {
            //console.log("Sending part " + this.partNumbers[i])
            this.sendToS3(data[i][0], blobs[i], i, this.partNumbers[i]);
        }
    }
};
/**
 * Used to send each uploadPart
 * @param  array data  parameters of the part
 * @param  blob blob  data bytes
 * @param  integer index part index (base zero)
 */
S3MultiUpload.prototype.sendToS3 = function(data, blob, index, partNumber) {
    var self = this;
    var url = data['url'];
    var size = blob.size;
    var request = self.uploadXHR[index] = new XMLHttpRequest();
    request.onreadystatechange = function() {
        if (request.readyState === 4) { // 4 is DONE
            // self.uploadXHR[index] = null;
            if (request.status !== 200) {
                self.updateProgress();
                self.onS3UploadError(request);
                return;
            }
            console.log('Finished part '+partNumber)
            self.partsCompleted[partNumber] = true
            self.partsInProgress[partNumber] = false
            self.updateProgress();
        }
    };

    request.upload.onprogress = function(e) {

        if (e.lengthComputable) {

            if (!self.partsInProgress[partNumber]) {
                    self.partsInProgress[partNumber] = true
            }

            self.total[partNumber] = size;
            self.loaded[partNumber] = e.loaded;
            if (self.lastUploadedTime[partNumber])
            {
                var time_diff=(new Date().getTime() - self.lastUploadedTime[partNumber])/1000;
                if (time_diff > 0.005) // 5 miliseconds has passed
                {
                    var byterate=(self.loaded[partNumber] - self.lastUploadedSize[partNumber])/time_diff;
                    self.byterate[partNumber] = byterate; 
                    self.lastUploadedTime[partNumber]=new Date().getTime();
                    self.lastUploadedSize[partNumber]=self.loaded[partNumber];
                }
            }
            else 
            {
                self.byterate[partNumber] = 0; 
                self.lastUploadedTime[partNumber]=new Date().getTime();
                self.lastUploadedSize[partNumber]=self.loaded[partNumber];
            }
            // Only send update to user once, regardless of how many
            // parallel XHRs we have (unless the first one is over).
            if (index==0 || self.total[0]==self.loaded[0]) {
                self.updateProgress();
            }
        }
    };
    request.open('PUT', url, true);
    request.send(blob);
};

/**
 * Abort multipart upload
 */
S3MultiUpload.prototype.cancel = function() {
    var self = this;
    for (var i=0; i<this.uploadXHR.length; ++i) {
        this.uploadXHR[i].abort();
    }
    $.post(self.SERVER_LOC, {
        command: 'abort',
        sendBackData: self.sendBackData
    }).done(function(data) {

    });
};

/**
 * Complete multipart upload
 */
S3MultiUpload.prototype.completeMultipartUpload = function() {
    var self = this;
    if (this.completed) return;
    this.completed=true;
    $.post(self.SERVER_LOC, {
        command: 'complete',
        sendBackData: self.sendBackData
    }).done(function(data) {
        self.onUploadCompleted(data);
        document.getElementById("objectlocation").value = 'Object Location: '+data.locationinfo;
        
        
        
        
        
        // PATT New Addition - START
        console.log(data);
        console.log(data.locationinfo);
        console.log('Final');
        obj_s3link = data.locationinfo;
        //console.log({obj_key:obj_key, obj_size:obj_size, obj_type:obj_type, obj_s3link:obj_s3link});
        
        //create_mld_post_from_s3_data( data );
        
        // PATT New Addition - END
        
        
        
    }).fail(function(jqXHR, textStatus, errorThrown) {
        self.onServerError('complete', jqXHR, textStatus, errorThrown);
    });
};

// PATT START
/**
 * Complete multipart upload
 */
// function create_mld_post_from_s3_data( input ) {
function create_mld_post_from_s3_data(  ) {	
	
	console.log( 'create_mld_post_from_s3_data' );
	
	//obj_s3link = input.locationinfo;
	
	// Set variables from MLD upload modal
	let mduff = $('#mdocs-upload-file-field').val();
	let mdocs_name = $('#mdocs-name').val();	
	let mdocs_tags = $('#mdocs-tags').val();
	
	let mdocs_type = $('input[name=mdocs-type]').val(); 
	
	
	let mdocs_last_modified = $('input[name=mdocs-last-modified]').val();
	let mdocs_version = $('input[name=mdocs-version]').val();
	let mdocs_cat = $('input[name=mdocs-cat]').val(); 
	let mdocs_social = $('input[name=mdocs-social]').val();
	let mdocs_non_members = $('input[name=mdocs-non-members]').val();
	let mdocs_index = $('input[name=mdocs-index]').val();
	let mdocs_pname = $('input[name=mdocs-pname]').val();
	let mdocs_file_status = $('input[name=mdocs-file-status]').val();
	let mdocs_post_status = $('input[name=mdocs-post-status]').val();
	let mdocs_add_contributors = $('input[name=mdocs-add-contributors]').val();
	let mdocs_real_author = $('input[name=mdocs-real-author]').val();
	let mdocs_categories = $('input[name=mdocs-categories]').val();
	let mdocs_desc = $('input[name=mdocs-desc]').val();	

	let mdocs_permalink = $('input[name=mdocs-permalink]').val();	
	
	if( mdocs_cat == '' ) {
		let searchstr = "mdocs-cat=";
		let len = searchstr.length;
		let n = mdocs_permalink.indexOf(searchstr);
		
		mdocs_cat = mdocs_permalink.substring(n+len);
	}
	
	console.log('I care about this.');
	console.log({obj_key:obj_key, obj_size:obj_size, obj_type:obj_type, obj_s3link:obj_s3link, obj_name:obj_name, mduff:mduff});
	
	
	
	let data = {
		action: 'wppatt_create_mld_post',
		//input: input,
		obj_s3link: obj_s3link,
		obj_key: obj_key, 
		obj_size: obj_size,
		obj_type: obj_type,
		obj_name: obj_name,
		mduff: mduff,
		mdocs_name: mdocs_name,
		mdocs_tags: mdocs_tags,
		mdocs_type: mdocs_type,
		mdocs_last_modified: mdocs_last_modified,
		mdocs_version: mdocs_version,
		mdocs_cat: mdocs_cat,
		mdocs_social: mdocs_social,
		mdocs_non_members: mdocs_non_members,
		mdocs_index: mdocs_index,
		mdocs_pname: mdocs_pname,
		mdocs_file_status: mdocs_file_status,
		mdocs_post_status: mdocs_post_status,
		mdocs_add_contributors: mdocs_add_contributors,
		mdocs_real_author: mdocs_real_author,
		mdocs_categories: mdocs_categories,
		mdocs_desc: mdocs_desc
	};
	
	console.log({data:data});
	
// 	$.post(wpsc_admin.ajax_url, data).done( function(data) {
	return $.post(wpsc_admin.ajax_url, data).done( function(data) {
        
        console.log('AJAX wppatt_create_mld_post Successful');
        console.log(data);
        location.reload();
                
//     }).fail(function(jqXHR, textStatus, errorThrown) {
    });
/*
	$.post(wpsc_admin.ajax_url, data).done( function(data) {
        
        console.log('AJAX wppatt_create_mld_post Successful');
        console.log(data);
                
//     }).fail(function(jqXHR, textStatus, errorThrown) {
    }).fail(function( response) {	
	    console.log('AJAX wppatt_create_mld_post FAILED');
	    console.log({response:response});
	    //console.log({jqXHR:jqXHR, textStatus:textStatus, errorThrown:errorThrown});
	    
//         self.onServerError('complete', jqXHR, textStatus, errorThrown);
		//this.onServerError('complete', jqXHR, textStatus, errorThrown);
    });
*/
	
}

// PATT END

/**
 * Track progress, propagate event, and check for completion
 */
S3MultiUpload.prototype.updateProgress = function() {
    var total=0;
    var loaded=0;
    var byterate=0.0;
    var complete=1;
    for (var i=0; i<this.total.length; ++i) {
        loaded += +this.loaded[i] || 0;
        total += this.total[i];
        if (this.loaded[i]!=this.total[i])
        {
            // Only count byterate for active transfers
            byterate += +this.byterate[i] || 0;
            complete=0;
        }
    }
    if (complete) {
        this.completeMultipartUpload();
    }
    total=this.fileInfo.size;
    this.onProgressChanged(loaded, total, byterate, this.partsInProgress, this.partsCompleted);
};

// Overridable events: 

/**
 * Overrride this function to catch errors occured when communicating to your server
 *
 * @param {type} command Name of the command which failed,one of 'CreateMultipartUpload', 'SignUploadPart','CompleteMultipartUpload'
 * @param {type} jqXHR jQuery XHR
 * @param {type} textStatus resonse text status
 * @param {type} errorThrown the error thrown by the server
 */
S3MultiUpload.prototype.onServerError = function(command, jqXHR, textStatus, errorThrown) {};

/**
 * Overrride this function to catch errors occured when uploading to S3
 *
 * @param XMLHttpRequest xhr the XMLHttpRequest object
 */
S3MultiUpload.prototype.onS3UploadError = function(xhr) {};

/**
 * Override this function to show user update progress
 *
 * @param {type} uploadedSize is the total uploaded bytes
 * @param {type} totalSize the total size of the uploading file
 * @param {type} speed bytes per second
 */
S3MultiUpload.prototype.onProgressChanged = function(uploadedSize, totalSize, bitrate, partsInProgress, partsCompleted) {};

/**
 * Override this method to execute something when upload finishes
 *
 */
S3MultiUpload.prototype.onUploadCompleted = function(serverData) {};
/**
 * Override this method to execute something when part preparation is completed
 *
 */
S3MultiUpload.prototype.onPrepareCompleted = function() {};


$(document).ready(function(){
	
	
	
	// Cover up color issue with MLD plugin
	$("label[for='mdocs-name']").css( 'color', '#444' );
	
	// Adds 'required' to MLD Name field. 
	$("label[for='mdocs-name']").append('<span style="color:red;">*</span>');
	
	
	//$(".modal-footer").append('<input type="submit" class="btn btn-primary" id="wppatt-mdocs-save-doc-btn" value="PATT Add/Update" />');
	
	
	//
	// Adjust modal window to look like Support Candy's.
	//
	$("#mdocs-add-update-container > .page-header").attr( "id", "wpsc_popup_title" );
	$("#mdocs-add-update .close").hide();
	$("#mdocs-add-update .modal-footer").hide();	
	
	
	let style_override = '<style> .modal-body{ padding: 0px !important; } h1{ color: #fff !important; } .page-header{ margin: 0px !important; } .well{ background-color: #fff !important; border: none !important; border-radius: 0px !important; } .well-lg{ border-radius: 0px !important; padding: 15px !important; } #wppatt_popup_footer{ padding: 15px; background-color: #F6F6F6; } .patt-button{ width: 145px; height: 40px; border-radius: 0px !important; border: 0px; cursor: pointer;} .patt-button-close{ background-color:#ffffff !important;color:#000000 !important; } .patt-button-save{ background-color:#0473AA !important;color:#FFFFFF !important; }</style>';
	
	$("#mdocs-add-update").append(style_override);
	
	
	// Inital set save button to hide
	$("#wppatt-mdocs-save-doc-btn").hide();
/*
	$("#mdocs-add-update").on( "ready", "#wppatt-mdocs-save-doc-btn", function() {
		$(this).hide();
	});
*/
	//document.getElementById('wppatt-mdocs-save-doc-btn').style.display = "none";
	
	
	// Submit button for PATT MLD integration. Validates and initiates save. 
	$("#wppatt-mdocs-save-doc-btn").click(function(){
		console.log('clickity click');
		let validation = true;
		
		let name = $("#mdocs-name").val();
		name = name.trim();
		let tags = $("#mdocs-tags").val();
		tags = tags.trim();
// 		let s3_upload_status = $("#result").html(); 
		let s3_upload_status = $("#upload-alert").html(); 
		s3_upload_status = s3_upload_status.trim();
		
		console.log({name:name, tags:tags, s3_upload_status:s3_upload_status});
		
		// Validation checks
		if( name == '' ) {
			validation = false;
			set_alert( 'danger', 'Submission Error: Name cannot be blank.' );
		}
		
		if( tags == '' ) {
			//validation = false;
			//set_alert( 'danger', 'Tags cannot be blank.' );
		}
		
		if( s3_upload_status != 'Upload successful.' ) {
			validation = false;
			
			if( s3_upload_status == 's3_upload_status' ) {
				set_alert( 'danger', 'Submission Error: S3 upload not complete.' );
			} else {
				set_alert( 'danger', 'Submission Error: No file uploaded.' );
			}
			
			
		}
		
		// Submit
		if( validation ) {
			console.log('Validated. Do it.');
			
// 			$.when( create_mld_post_from_s3_data() ).then( console.log( 'This is it.') ); //location.reload()
 			$.when( create_mld_post_from_s3_data() ).then( location.reload() ); // working
//			$.when( create_mld_post_from_s3_data() ).then( console.log( 'finished create_mld_post_from_s3_data' ) ); 
			
			//create_mld_post_from_s3_data();
			//mdocs_modal_close();
			//setTimeout( location.reload() , 2200);
		}
		
	});
  
}); // jquery doc ready


// Simple hash function based on java's. Used for set_alert.
String.prototype.hashCode = function(){
    var hash = 0;
    for (var i = 0; i < this.length; i++) {
        var character = this.charCodeAt(i);
        hash = ((hash<<5)-hash)+character;
        hash = hash & hash; // Convert to 32bit integer
    }
    return hash;
}


// Sets an error message notificaiton
function set_alert( type, message ) {
	
	let alert_style = '';
	let hash = message.hashCode();
	console.log({hash:hash});
	
	switch( type ) {
		case 'success':
			alert_style = 'alert-success';		
			break;
		case 'warning':
			alert_style = 'alert-warning';
			break;
		case 'danger':
			alert_style = 'alert-danger';
			break;		
	}
	jQuery('#alert_status_modal').show();
	//jQuery('#alert_status_modal').html('<div id="alert-' + hash + '" class=" alert '+alert_style+'">'+message+'</div>'); //badge badge-danger
	jQuery('#alert_status_modal').append('<div id="alert-' + hash + '" class=" alert '+alert_style+'">'+message+'</div>'); // shows more notificaitons than desired. 
	jQuery('#alert_status_modal').addClass('alert_spacing');
	
	alert_dismiss( hash );
}

// Sets the time for dismissing the error notification
function alert_dismiss( hash ) {
// 		setTimeout(function(){ jQuery('#alert_status').fadeOut(1000); }, 9000);	
	setTimeout( function(){ jQuery( '#alert-'+hash ).fadeOut( 1000 ); }, 9000 );	
}

// Upload Notification
function set_upload_notification( type, message ) {
	
	let alert_style = '';
	
	switch( type ) {
		case 'success':
			alert_style = 'alert-success';		
			break;
		case 'warning':
			alert_style = 'alert-warning';
			break;
		case 'danger':
			alert_style = 'alert-danger';
			break;		
	}
	jQuery('#upload_alert_status_modal').show();
	jQuery('#upload_alert_status_modal').html('<div id="upload-alert' + '" class=" alert '+alert_style+'">'+message+'</div>'); //badge badge-danger
	//jQuery('#alert_status_modal').append('<div id="alert-' + hash + '" class=" alert '+alert_style+'">'+message+'</div>'); // shows more notificaitons than desired. 
	jQuery('#upload_alert_status_modal').addClass('alert_spacing');
	
}


