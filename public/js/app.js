function dataURItoBlob(dataURI) {
    // convert base64/URLEncoded data component to raw binary data held in a string
    var byteString;
    if (dataURI.split(',')[0].indexOf('base64') >= 0)
        byteString = atob(dataURI.split(',')[1]);
    else
        byteString = unescape(dataURI.split(',')[1]);

    // separate out the mime component
    var mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0];

    // write the bytes of the string to a typed array
    var ia = new Uint8Array(byteString.length);
    for (var i = 0; i < byteString.length; i++) {
        ia[i] = byteString.charCodeAt(i);
    }

    return new Blob([ia], {type:mimeString});
}

jQuery(document).ready(function(){
  jQuery(".design").on("click", function(){
    jQuery("#fg").attr("src", jQuery(this).attr("src")).data("design", jQuery(this).data("design"));
    jQuery(".design.active").removeClass("active");
    jQuery(this).addClass("active");
  });

  var $modal = jQuery('#modal');
  var image = document.getElementById('sample_image');
  var cropper;

  $modal.on('shown.bs.modal', function() {
    cropper = new Cropper(image, {
      aspectRatio: 1,
      viewMode: 3,
      preview: '.preview'
    });
  }).on('hidden.bs.modal', function() {
     cropper.destroy();
     cropper = null;
  });

  jQuery('#upload_image').change(function(event){
    var files = event.target.files;
    var done = function (url) {
      image.src = url;
      $modal.modal('show');
    };
    
    if (files && files.length > 0){
       
      reader = new FileReader();

      reader.onload = function (event) {
        done(reader.result);
      };
      reader.readAsDataURL(files[0]);

    }
  });

  jQuery("#crop").click(function(){

    document.getElementById("download").innerHTML = "Uploading... Please wait...";

    canvas = cropper.getCroppedCanvas({
      width: 1080,
      height: 1080,
    });
    
    canvas.toBlob(function(blob) {
      var reader = new FileReader();
      reader.readAsDataURL(blob); 
      reader.onloadend = function() {
        var base64data = reader.result;

        var formData = new FormData();
        formData.append("design", jQuery("#fg").data("design"));
        formData.append("image", dataURItoBlob(base64data));
        jQuery.ajax({
          url: action_url_ajax.ajaxurl,
          data: formData,
          type: "POST",
          contentType: false,
          processData: false,
          success: function(data){
            console.log(data);
            $modal.modal('hide');
            jQuery('#uploaded_image').attr('src', data);
            document.getElementById("download").innerHTML = "Uploaded";
          },
          error: function(){
            document.getElementById("download").innerHTML = "Download Picture";
          },
          xhr: function() {
            var myXhr = jQuery.ajaxSettings.xhr();
            if(myXhr.upload){
                myXhr.upload.addEventListener('progress', function(e){
                  if(e.lengthComputable){
                    var max = e.total;
                    var current = e.loaded;
    
                    var percentage = Math.round((current * 100)/max);
                    document.getElementById("download").innerHTML = "Uploading... Please Wait... " + percentage + "%";
                  }
                }, false);
            }
            return myXhr;
          },
        });
            
      }
    });

  });
});