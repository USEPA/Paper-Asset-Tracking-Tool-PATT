                      /*
                This code will determine when a code has been either entered manually or
                entered using a scanner.
                It assumes that a code has finished being entered when one of the following
                events occurs:
                    • The enter key (keycode 13) is input
                    • The input has a minumum length of text and loses focus
                    • Input stops after being entered very fast (assumed to be a scanner)
            */
            
            var inputStart, inputStop, firstKey, lastKey, timing, userFinishedEntering;
            var minChars = 3;
            
            // handle a key value being entered by either keyboard or scanner
            jQuery("#scanInput").keypress(function (e) {
                // restart the timer
                if (timing) {
                    clearTimeout(timing);
                }
                
                // handle the key event
                if (e.which == 13) {
                    // Enter key was entered
                    
                    // don't submit the form
                    e.preventDefault();
                    
                    // has the user finished entering manually?
                    if (jQuery("#scanInput").val().length >= minChars){
                        userFinishedEntering = true; // incase the user pressed the enter key
                        inputComplete();
                    }
                }
                else {
                    // some other key value was entered
                    
                    // could be the last character
                    inputStop = performance.now();
                    lastKey = e.which;
                    
                    // don't assume it's finished just yet
                    userFinishedEntering = false;
                    
                    // is this the first character?
                    if (!inputStart) {
                        firstKey = e.which;
                        inputStart = inputStop;
                        
                        // watch for a loss of focus
                        jQuery("body").on("blur", "#scanInput", inputBlur);
                    }
                    
                    // start the timer again
                    timing = setTimeout(inputTimeoutHandler, 500);
                }
            });
            
            // Assume that a loss of focus means the value has finished being entered
            function inputBlur(){
                clearTimeout(timing);
                if (jQuery("#scanInput").val().length >= minChars){
                    userFinishedEntering = true;
                    inputComplete();
                }
            }
            
            
            // reset the page
            jQuery("#reset").click(function (e) {
                e.preventDefault();
                resetValues();
            });
            
            function resetValues() {
                // clear the variables
                inputStart = null;
                inputStop = null;
                firstKey = null;
                lastKey = null;
                // clear the results
                inputComplete();
            }
            
            // Assume that it is from the scanner if it was entered really fast
            function isScannerInput() {
                return (((inputStop - inputStart) / jQuery("#scanInput").val().length) < 15);
            }
            
            // Determine if the user is just typing slowly
            function isUserFinishedEntering(){
                return !isScannerInput() && userFinishedEntering;
            }
            
            function inputTimeoutHandler(){
                // stop listening for a timer event
                clearTimeout(timing);
                // if the value is being entered manually and hasn't finished being entered
                if (!isUserFinishedEntering() || jQuery("#scanInput").val().length < 3) {
                    // keep waiting for input
                    return;
                }
                else{
                    reportValues();
                }
            }
            
            // here we decide what to do now that we know a value has been completely entered
            function inputComplete(){
                // stop listening for the input to lose focus
                jQuery("body").off("blur", "#scanInput", inputBlur);
                // report the results
                reportValues();
            }
            
            function reportValues() {
                // update the metrics
                jQuery("#startTime").text(inputStart === null ? "" : inputStart);
                jQuery("#firstKey").text(firstKey === null ? "" : firstKey);
                jQuery("#endTime").text(inputStop === null ? "" : inputStop);
                jQuery("#lastKey").text(lastKey === null ? "" : lastKey);
                jQuery("#totalTime").text(inputStart === null ? "" : (inputStop - inputStart) + " milliseconds");
                if (!inputStart) {
                    // clear the results
                    jQuery("#resultsList").html("");
                    jQuery("#scanInput").focus().select();
                } else {
                    // prepend another result item
                    var inputMethod = isScannerInput() ? "Scanner" : "Keyboard";
                    jQuery("#resultsList").prepend("<div class='resultItem " + inputMethod + "'>" +
                        "<span>Value: " + jQuery("#scanInput").val() + "<br/>" +
                        "<span>ms/char: " + ((inputStop - inputStart) / jQuery("#scanInput").val().length) + "</span></br>" +
                        "<span>InputMethod: <strong>" + inputMethod + "</strong></span></br>" +
                        "</span></div></br>");
                    jQuery("#scanInput").focus().select();
                    inputStart = null;
                }
            }
            
            jQuery("#scanInput").focus();