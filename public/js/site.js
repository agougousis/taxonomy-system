jQuery.fn.center = function ()
{
    this.css("position","fixed");
    this.css("top", (jQuery(window).height() / 2) - (this.outerHeight() / 2));
    this.css("left", (jQuery(window).width() / 2) - (this.outerWidth() / 2));
    return this;
}

function handleAjaxErrorWithToastr(jqXHR){
    $('#loading-image').hide();
    switch (jqXHR.status) {
        case 401: 
            if(jqXHR.statusText.indexOf('expired') >= 0){
                toastr.error('This page or session has expired! Please refresh your page!',{timeOut: 5000});
            } else {
                toastr.error('Yoor are not authorized for this action!',{timeOut: 5000});
            }
            break;
        default:
            response = JSON.parse(jqXHR.responseText);
            var errorMessage = "";
            for(var j=0; j < response.errors.length; j++){
                errorItem = response.errors[j];
                errorMessage = errorMessage+"<strong>"+errorItem.field+"</strong>: "+errorItem.message+"<br>";
            }
            toastr.error(errorMessage,{timeOut: 5000});
            break;
    }   
}

function handleAjaxErrorWithResultDiv(jqXHR){
    $('#loading-image').hide();
    switch (jqXHR.status) {
        case 401: 
            if(jqXHR.statusText.indexOf('expired') >= 0){
                toastr.error('This page or session has expired! Please refresh your page!',{timeOut: 5000});
            } else {
                toastr.error('Yoor are not authorized for this action!',{timeOut: 5000});
            }
            break;
        default:
            response = JSON.parse(jqXHR.responseText);
            var messages = "";
            for(var j=0; j < response.errors.length; j++){
                errorItem = response.errors[j];
                $('#result_div').append("<span style='color: red'>"+errorItem.field+": "+errorItem.message+"</span>");
            }
            break;
    }   
}