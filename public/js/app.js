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

window.uploadPicture = function(){

var imageSize = {
    width: 1200,
    height: 630,
    type: 'square'
};
croppie.result({
  size: imageSize,
}).then(function(dataURI){
  var formData = new FormData();
  formData.append("design", jQuery("#fg").data("design"));
  formData.append("image", dataURItoBlob(dataURI));
  formData.append('security', data_ajax.security);
  formData.append('file_name', data_ajax.file_name);
  jQuery.ajax({
    url: '?upload-image='+data_ajax.security,
    data: formData,
    type: "POST",
    contentType: false,
    processData: false,
    success: function(){
      document.getElementById("download").innerHTML = "Share Image";
      window.location.href = 'social-media-frame-share?share-image='+data_ajax.file_name;
    },
    error: function(){
      document.getElementById("download").innerHTML = "Share Image";
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
});
}

window.updatePreview = function(url) {
document.getElementById("crop-area").innerHTML = "";
window.croppie = new Croppie(document.getElementById("crop-area"), {
  "url": url,
  boundary: {
    height: 550,
    width: 550
  },
  viewport: {
    width: 550,
    height: 550
  },
  enableResize: true,
});

jQuery("#fg").on('mouseover touchstart', function(){
  //document.getElementById("fg").style.zIndex = -1;
});
jQuery(".cr-boundary").on('mouseleave touchend', function(){
  document.getElementById("fg").style.zIndex = 10;
});

document.getElementById("download").onclick = function(){
  this.innerHTML = "Uploading... Please wait...";
  uploadPicture();
};
document.getElementById("download").removeAttribute("disabled");
};

window.onFileChange = function(input){
  
  if (input.files && input.files[0]) {
    var reader = new FileReader();

    reader.onload = function (e) {
      image = new Image();
      image.onload = function() {
        var width = this.width;
        var height = this.height;
        
        if (['image/png','image/jpeg'].includes(input.files[0].type)) {
          if(width >= 100 && height >= 100){
            updatePreview(e.target.result);
          }else{
            alert("Image should be atleast have 100px width and 100px height");
          }   
        }else{
          alert('Invalid File type');  
        }

        
      };
      image.src = e.target.result; 
    }

    reader.readAsDataURL(input.files[0]);
  }
}

jQuery(document).ready(function(){
  jQuery(".design").on("click", function(){
    jQuery("#fg").attr("src", jQuery(this).attr("src")).data("design", jQuery(this).data("design"));
    jQuery(".design.active").removeClass("active");
    jQuery(this).addClass("active");
  });
});