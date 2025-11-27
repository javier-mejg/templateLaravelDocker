
$(document).ready(function () {
    $("#loader").hide();
});

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});


function viewFile(type, title, gral_file_uID, url) {

    var urlSite = window.location.hostname;
    var SITEURL = "https://" + urlSite + "/";
    //var SITEURL = "https://soyleonadmin.anahuacqro.edu.mx/";
    var url_file = url;
    var url = SITEURL + "files/" + url;
    var url_decode = SITEURL + "storage/app/storage/" + url_file.replace(/\*/g, '/')

    var text = "";

    if (type == "img") {
        text += '<img src="' + url + '" class="img-fluid" >';

    } else if ('pdf') {
        text +=
            '<object data="' + url + '" type="application/pdf"+ width="100%" style="min-height:80vh">';
        text +=
            '<p>Error al mostrar archivo. <a href="' + url_decode + '">Descargar</a>.</p>';
        text += '</object>';
    }

    $('#FileBody').empty().append(text);
    $('#FileLabel').empty().append(title);
    $('#Filesview').modal('show');
}

function viewFilePDF(type, title, gral_file_uID, url) {

    var urlSite = window.location.hostname;
    var SITEURL = "https://" + urlSite + "/";
    // var SITEURL = "https://soyleonadmin.anahuacqro.edu.mx/";
    var url_file = url;
    var url = SITEURL + "files/" + url;
    var url_decode = SITEURL + "storage/app/storage/" + url_file.replace(/\*/g, '/')

    var text = "";

    if (type == "img") {
        text += '<img src="' + url + '" class="img-fluid" >';

    } else if ('pdf') {
        // Incluir contenedor para el visor de PDF
        text += '<div id="pdf-viewer-container" style="width: 100%; height: 80vh; overflow: auto; display: flex; flex-direction: column; align-items: center;"></div>';

        // Cargar PDF.js
        text += '<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>';
        text += '<script>';
        text += 'var url = "' + url + '";';
        text += 'var loadingTask = pdfjsLib.getDocument(url);';
        text += 'loadingTask.promise.then(function(pdf) {';
        text += '  var viewerContainer = document.getElementById("pdf-viewer-container");';
        text += '  for (var pageNumber = 1; pageNumber <= pdf.numPages; pageNumber++) {';
        text += '    pdf.getPage(pageNumber).then(function(page) {';
        text += '      var scale = viewerContainer.clientWidth / page.getViewport({ scale: 1 }).width;';
        text += '      var viewport = page.getViewport({ scale: scale });';
        text += '      var canvas = document.createElement("canvas");';
        text += '      var context = canvas.getContext("2d");';
        text += '      canvas.height = viewport.height;';
        text += '      canvas.width = viewport.width;';
        text += '      canvas.style.marginBottom = "20px";';  // Espaciado entre páginas
        text += '      viewerContainer.appendChild(canvas);';
        text += '      var renderContext = {';
        text += '        canvasContext: context,';
        text += '        viewport: viewport';
        text += '      };';
        text += '      page.render(renderContext).promise.then(function () {';
        text += '        console.log("Page rendered");';
        text += '      });';
        text += '    });';
        text += '  }';
        text += '}, function (reason) {';
        text += '  console.error(reason);';
        text += '});';
        text += '</script>';
        text +=
            '<p>¿Problemas para ver el archivo? <a href="' + url_decode + '">Descargar</a>.</p>';
        text += '</object>';
    }

    $('#FileBody').empty().append(text);
    $('#FileLabel').empty().append(title);
    $('#Filesview').modal('show');
}
