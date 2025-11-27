function makeAjaxCallWithJWT({ 
    baseUrl,
    url, 
    method, 
    contentType, 
    dataType, 
    beforeSend, 
    success, 
    error, 
    loader = "", 
    complete,
    data,
    always
}) {

    let token;

    return $.ajax({
        url: baseUrl+"/api/generar-jwt",
        type: "POST",
        success: function(response){
            token = response.token

            $.ajax({
                url: url,
                method: method,
                contentType: contentType,
                dataType: dataType,
                headers: {
                    'Authorization': 'Bearer ' + token
                },
                data: data,
                beforeSend: function (jqXHR, settings) {
                    $(loader).show();
                    if (typeof beforeSend === 'function') {
                        beforeSend(jqXHR, settings);
                    }
                },

                success: function (response) {
                    $(loader).hide();
                    if (typeof success === 'function') {
                            return success(response);
                    }
                },

                error: function (xhr, status, err) {
                    let response = xhr.responseText
                    try {
                        response = JSON.parse(xhr.responseText);
                    } catch (e) {
                        console.warn("No se pudo parsear response como JSON:", e);
                    }
                    console.error(status, err, response);
                    $(loader).hide();
                    if (typeof error === 'function') {
                        error(xhr, status, err);
                    }
                },
                complete: function (){
                    if(typeof complete === 'function'){
                        return complete();
                    }
                }
            }).always(always);

        }
    })

}

