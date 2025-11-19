moment.locale('es');

const statusIconColor = {
    "Pendiente": { icon: "far fa-clock", color: "warning" },
    "Completado": { icon: "fas fa-check", color: "info" },
    "Finalizado": { icon: "fas fa-check", color: "info" },
    "Agendado": { icon: "fas fa-calendar-alt", color: "lime" },
    "Entregado": { icon: "fas fa-check-double", color: "success" },
    "Cancelado": { icon: "fas fa-times", color: "danger" },
    "En revisión": { icon: "fas fa-spinner", color: "primary" }
};

function statusBadge(status) {
    const { icon, color } = statusIconColor[status] || { icon: "fas fa-question", color: "gray" };
    return `<span class="badge badge-${color}" title="${status}">
                <i class="${icon}"></i>
            </span>`;
}

// Reemplazar el sufijo de clases que empiezan con prefijo (ej badge-success -> badge-warning)
function setColorClass($element, prefix, newClass) {
    $($element).each(function () {
        $(this).removeClass(function (index, className) {
            return (className.match(new RegExp(`\\b${prefix}\\S+`, 'g')) || []).join(" ");
        });
        $(this).addClass(prefix + newClass);
    });
}

window.TIRoles = [
            "ec1407a8-5b4c-4910-b757-9d60a3651a86", //email
            "1676d7ad-c1b2-4fbb-90c9-1ea3ebc348e5", //equipo de cómputo
            "d05ee3c5-2376-4ad8-bd7d-393d9d36555d", //Telefono
            "4b52f81e-e167-48fc-9f0c-d2512839d20b", //Banner
            "4fca4081-2cd7-4a98-ad75-0cfab2321bd3", //SOyLeon
            "ebaf4c76-a146-4f41-8833-8becdbf817a1" //GLPI
];



$(".dropdown-menu").on("click", ".dropdown-item", function () {
    const status = $(this).text().trim();
    const { icon, color } = statusIconColor[status] || { icon: "fas fa-question", color: "gray" };

    const $statusDropdown = $(this).closest(".dropdown-menu").siblings(".status-dropdown");

    if ($statusDropdown.length) {
        let html = `<i class="${icon}"></i>`
        if ($statusDropdown.is("#updateModal-computerstatus")) html += ' ' + status
        $statusDropdown.html(html);
        $statusDropdown.attr("title", status);
        $statusDropdown.attr("data-status", statusIDs[status]);
        serviceReqCancelled = status == "Cancelado";
        let dropdownId = $statusDropdown.attr('id');
        let idsNames = {
            "updateModal-bannerstatus": "Banner",
            "updateModal-soyleonstatus": "Soy León",
            "updateModal-ticketsstatus": "GLPI",
            "updateModal-computerstatus": "equipo de cómputo",
            "updateModal-telstatus": "extensión telefónica"
        }
        cancelledService = idsNames[dropdownId] || "servicio adicional"
        $statusDropdown.closest(".form-group").find("input, select").prop("disabled", status == "Cancelado")
        setColorClass($statusDropdown, 'badge-', color)
    }
    else {
        const toggle = $(this).closest(".dropdown-menu").siblings(".dropdown-toggle")
        toggle.text($(this).text());
    }
});



let currentCard = {}

function getServices(card) {
    return {
        email: card.serviceRequests.find(request => request.service === "Email"),
        computer: card.serviceRequests.find(request => request.service === "Equipo de cómputo"),
        tel: card.serviceRequests.find(request => request.service === "Extensión telefónica"),
        soyleon: card.serviceRequests.find(request => request.service === "Soy León"),
        banner: card.serviceRequests.find(request => request.service === "Banner"),
        tickets: card.serviceRequests.find(request => request.service === "GLPI"),
        vpn: card.serviceRequests.find(request => request.service === "VPN"),
        zoom: card.serviceRequests.find(request => request.service === "Zoom"),
        llamadas: card.serviceRequests.find(request => request.service === "Llamadas internacionales"),
    };
}

async function updateModalFields(card) {
    const modal = document.getElementById("updatecardModal");
    if (!modal) return

    modal.dataset.requestId = card?.requestId;
    currentCard = card

    const copyButton = modal.querySelector('[aria-label="copy"]');
    if (copyButton) copyButton.dataset.obj = JSON.stringify(card);

    const services = getServices(card)

    var idsJson = {
        nombreUsuario: card.name,
        tipoOnboarding: card.type,
        status: card.status,
        nombreReemplazado: card.replacedName ?? "Usuario no existente",
        nombreArea: card.area,
        nombrePuesto: card.position,
        fechaEntrada: moment(card.entryDate, "YYYY-MM-DD").format("DD MMM YYYY"),
        nombreJefe: card.boss,
        idJefe: card.bossId,
        amigoAnahuac: card.anahuacFriend,
        necesitaAmigo: card.needsAnahuacFriend,
        mesesPrueba: card.trialMonths,
        email: card.email,
        sugerenciaCorreo1: services.email?.emailSuggestions?.[0],
        sugerenciaCorreo2: services.email?.emailSuggestions?.[1],
        sugerenciasCorreo: [services.email?.emailSuggestions?.[0], services.email?.emailSuggestions?.[1]].filter(Boolean).join(', '),
        equipoComputo: card.status == "Completado" && !(!!card.computer) 
            || card.status == "Finalizado" && !(!!card.computer) 
            ?  "Equipo no asignado" :  card.computer,
        equipoComputoStatus: services.computer?.status,
        auxiliarEquipoComputo: card.auxiliaryComputer,
        tel: 
        card.status == "Completado" && !(!!card.extensionNumber) 
            || card.status == "Finalizado" && !(!!card.extensionNumber) 
            ?  "Extensión no asignada" :        
        card.extensionNumber?.toString().padStart(4, "0"),
        telTipo: card.isVirtualPhone,
        ubicacionFisica: card.physicalLocation,
        banner: !!services.banner, //services.banner?.bannerModules,
        soyleon: services.soyleon?.soyLeonModules,
        tickets: !!services.tickets,
        pin: card.printPin ?? "0000",
        justificacion: card.computerJustification,
        programas: card.computerApps,
        //Servicios de Smartcampus
        vpn: !!services.vpn,
        zoom: !!services.zoom,
        llamadas: !!services.llamadas,
        emailstatus: services.email?.status,
        computerstatus: services.computer?.status,
        bannerstatus: services.banner?.status,
        soyleonstatus: services.soyleon?.status,
        ticketsstatus: services.tickets?.status,
        telstatus: services.tel?.status,
        telstatus2: services.tel?.status,
        solicitarExt: !!services.tel,
        globalTalentId: card.globalTalentId || "",
        /*   "bannerstatus-options":null,
           "soyleonstatus-options":null,
           "ticketsstatus-options":null,
           "telstatus2-options":null
   */
    }
        window.cardData = currentCard;

    //por cada #updateModal-KEY en el modal ...
    for (var key in idsJson) {
        const element = document.getElementById(`updateModal-${key}`);
        const $element = $(`#updateModal-${key}`)
        if (!element) continue
        let innerHTML = idsJson[key] || 'Pendiente'
        //if (Array.isArray(idsJson[key])) innerHTML = idsJson[key].map(value => `<span class="badge badge-purple">${value}</span>`).join(' ');
        switch (key) {
            case "amigoAnahuac":
                innerHTML = idsJson[key] || "Sin asignar";
                break;
            case "tel":
                innerHTML = `${idsJson.tel ? `${idsJson.tel} (${!!idsJson.telTipo ? 'Virtual' : 'Físico'})` : 'Pendiente'}`
                if (services.tel?.status == "Cancelado") innerHTML = "Cancelado"
                break;
            case "email":
                if (services.email?.status == "Cancelado") innerHTML = "Cancelado"
                break
            case "equipoComputo":
                if (services.computer?.status == "Cancelado") innerHTML = "Cancelado"
                break
            case "tickets":
                innerHTML = idsJson[key] 
                ?  idsJson['status'] == "Completado" || idsJson['status'] == "Finalizado" || idsJson['ticketsstatus'] == "Completado"
                    ? 'Completado' 
                    : 'Solicitado' 
                : 'No solicitado';
                break;
            case "banner":
                innerHTML = idsJson[key] 
                ?  idsJson['status'] == "Completado" || idsJson['status'] == "Finalizado" || idsJson['bannerstatus'] == "Completado" 
                    ? 'Completado' 
                    : 'Solicitado' 
                : 'No solicitado';
                break;
            case "solicitarExt":
                innerHTML = idsJson[key] 
                ?  idsJson['status'] == "Completado" || idsJson['status'] == "Finalizado" || idsJson['telstatus'] == "Completado"
                    ? 'Completado' 
                    : 'Solicitado' 
                : 'No solicitado';
                break;
            case "soyleon":
                let modules = services[key]?.soyLeonModules || [];
                innerHTML = modules.map(value => `<span class="badge badge-purple">${value}</span>`).join(' ')
                if (!modules.length) innerHTML = "No solicitado";
                break;
            case "tipoOnboarding":
                element.className = `badge badge-${typeColor[idsJson.tipoOnboarding] || 'dark'}`;
                break;
            case "equipoComputoStatus":
                innerHTML = statusBadge(idsJson.equipoComputoStatus)
                break;
            case "mesesPrueba":
                const badge = element.parentElement
                const isCH = !!badge.parentElement?.querySelector("select"); //checa si su sibling es un select (si es ch) o un div
                const hideBadge = !isCH && !idsJson.amigoAnahuac;
                badge.className = `badge badge-dark ${hideBadge ? 'd-none' : ''}`
                element.parentElement
                break;
            case "sugerenciasCorreo":
                innerHTML = `Sugeridos: ${idsJson[key] || 'Ninguno'}`;
                break;
            case "emailstatus":
            case "computerstatus":
            case "bannerstatus":
            case "soyleonstatus":
            case "ticketsstatus":
            case "telstatus":
            case "telstatus2":
                let deshabilitar = false;
                if (idsJson[key]) {
                    innerHTML = `<i class="${statusIconColor[idsJson[key]]?.icon}"></i>`

                    const siblingDropdownMenu = $element.siblings(".dropdown-menu");
                    let dropdownHtml = `<a class="dropdown-item" href="#">${idsJson[key]}</a>`
                    if (idsJson[key] == "Agendado") dropdownHtml += '<a class="dropdown-item" href="#">Entregado</a>'
                    if (idsJson[key] == "Pendiente") dropdownHtml += '<a class="dropdown-item" href="#">Cancelado</a>'
                    if (idsJson[key] == "Completado" && key === "computerstatus") dropdownHtml += '<a class="dropdown-item" href="#">Entregado</a>'

                    if (
                        (idsJson[key] == "Pendiente" && !["computerstatus", "telstatus", "telstatus2"].includes(key))
                    ) {
                        dropdownHtml += '<a class="dropdown-item" href="#">Completado</a>'
                    }

                    if (siblingDropdownMenu.length > 0) siblingDropdownMenu.html(dropdownHtml)

                    //innerHTML += ' ' + idsJson[key]
                    setColorClass($element, 'badge-', statusIconColor[idsJson[key]]?.color)
                    element.classList.remove('d-none');
                    element.title = idsJson[key];
                    deshabilitar = "Cancelado" == idsJson[key] || idsJson[key] == "Completado" && key != "computerstatus"
                }
                else {
                    setColorClass($element, 'badge-', 'gray')
                    element.title = "No solicitado";
                    innerHTML = `<i class="fas fa-minus"></i>`
                    deshabilitar = true
                };

                if ($element.is('button')) $element.toggleClass('dropdown-toggle', !deshabilitar).css('pointer-events', !deshabilitar ? 'auto' : 'none');
                break
            default:
                break
        }
        element.innerHTML = innerHTML || "";
    }

    function setModalProgress(percent) {
        const progressBar = modal.querySelector(".progress-bar");
        if (!progressBar) return;
        percent = Math.max(0, Math.min(100, percent));
        progressBar.style.width = `${percent}%`;
        progressBar.setAttribute("aria-valuenow", percent);
        let progressColor = percent < 100 ? "warning progress-bar-striped" : "info progress-bar-striped progress-bar-animated";
        if (card.globalTalentId && percent == 100 || card.status == "Finalizado") progressColor = "success";
        if (card.status == "Cancelado") progressColor = "danger"
        progressBar.className = `progress-bar bg-${progressColor}`;
    }
    setModalProgress(getProgress(card))


    async function populateAnahuacFriend(selectId, url, method, textKey, valueKey, textifEmpty = "No hay opciones disponibles.") {
        const select = $(`#${selectId}`);
        makeAjaxCallWithJWT({
            baseUrl: baseurl,
            url,
            method,
            dataType: "json",
            beforeSend: function () {
                select.empty().append('<option selected value="">Cargando opciones...</option>');
            },
            success: function (data) {
                select.empty()
                if (!data.result || !Array.isArray(data.result)) {
                    select.append('<option selected value="">No pudieron cargarse opciones, por favor contacte a TI.</option>');
                    return;
                }

                if (data.result.length) {
                    select.append('<option selected value="">Seleccione...</option>');
                    data.result.forEach(item => {
                        select.append(`<option value="${item[valueKey]}">${item[textKey]}</option>`);
                    });

                    if (idsJson[selectId]) {
                        const option = select.find(`option:contains(${idsJson[selectId]})`);
                        select.prop('disabled', true);
                        option.prop('selected', true);
                    } else {
                        select.prop('disabled', false);
                    }
                }
                else {
                    select.append(`<option selected value="">${textifEmpty}</option>`);
                }
                select.append('<option value="0">No asignar amigo Anáhuac.</option>');
            },
            error: function (xhr, status, error) {
                let response = JSON.parse(xhr.responseText);
                select.empty().append('<option selected value="">No se pudieron cargar opciones. Intente más tarde.</option>');
            }
        });
    }



    //ocultar servicios adicionales no solicitados y otros campos que no se ocupan
    $("#equipoComputo").closest(".form-group").toggleClass("d-none", !services.computer);
    services.computer && services.computer?.status == "Pendiente" ? $("#equipoComputo").prop("required") : $("#equipoComputo").removeProp("required");
    if(!(!!idsJson.equipoComputo)){
        $("#equipoComputo").removeProp("required");
        $("#equipoComputo").prop("disabled", true);
    }

    if(!!idsJson.equipoComputoStatus){
        $("#auxiliarComputerService").show();
        $("#printPin").show();
    }
    else{
        $("#auxiliarComputerService").hide();
        $("#printPin").hide();
    }

    services.tel && services.tel?.status == "Pendiente" ? $("#telefonoExt").prop("required") : $("#telefonoExt").removeProp("required");
    services.email && services.email?.status=="Pendiente" ? $("#emailAnahuac").prop("required") : $("#emailAnahuac").removeProp("required");
    $("#updateModal-equipoComputo").closest(".col").toggleClass("d-none", !services.computer);
    if (services.computer) $('#updateModal-datesButton').toggleClass('d-none', !(services.computer.status == 'Completado' || services.computer.status == 'Agendado'));
    $("#telefonoExt").closest(".form-group").toggleClass("d-none", !services.tel);
    $("#updateModal-tel").closest(".col").toggleClass("d-none", !services.tel);
    if(idsJson.pin && idsJson.pin != ""){
        $("#pinImpresion").val(idsJson.pin)
    } 

    $('#isReemplazado').toggleClass("d-none", idsJson.tipoOnboarding != "Reemplazo");
  

    //autorrellenar inputs
    //capital humano 
    if (idsJson.amigoAnahuac) $("#amigoAnahuac").empty().append(`<option selected value="">${idsJson.amigoAnahuac}</option>`).prop("disabled", !!idsJson.amigoAnahuac);
    else {
        if (!idsJson.necesitaAmigo) $("#amigoAnahuac").empty().append(`<option selected value=""> Amigo Anáhuac no asignado </option>`).prop("disabled", true);
        else{
            populateAnahuacFriend("amigoAnahuac", `${baseurl}/api/ob-gestionar-amigo-anahuac/consultar?monthsAvailable=${card.trialMonths},0&available=1`, "GET", "name", "anahuacFriendId", `No hay amigos Anáhuac disponibles por ${card.trialMonths} meses`)
            $("#amigoAnahuac").prop("disabled", !!idsJson.amigoAnahuac);
        }
    }

    $("#globalTalentId").val(idsJson.globalTalentId).prop("disabled", card.status == "Finalizado");
    
    if(card.status == "Completado"){
        setTimeout(function() {
            $("#globalTalentId").focus();
        }, 500);
    }
        
    //Ocultar boton guardar cambios cuando se finalizó
    $("#submitButton").prop("hidden", card.status == "Finalizado");
    //Mostrar servicios de SmartCampus
    $('#zoom-row').prop("hidden", !idsJson.zoom);
    $('#vpn-row').prop("hidden", !idsJson.vpn);
    $('#llamadas-row').prop("hidden", !idsJson.llamadas);
    $('#migrated-properties').prop("hidden", !((!!idsJson.pin) || (!!idsJson.programas) || (!!idsJson.justificacion)));
    $('#show-pin').prop("hidden", !(!!idsJson.pin));
    $('#show-programas').prop("hidden", !(!!idsJson.programas));
    $('#show-justificacion').prop("hidden", !(!!idsJson.justificacion));

    //ti
    $("#emailAnahuac").val(idsJson?.email?.split('@')[0] || '').prop("disabled", !services.email);
    $("#emailSuggestions").innerHTML = ""
    services.email?.emailSuggestions?.forEach(email => {
        const option = document.createElement("option");
        option.value = email.split('@')[0];
        $("#emailSuggestions").append(option);
    });
    $("#telefonoExt").val(idsJson.tel).prop("disabled", services.tel?.status == "Cancelado");
    $("#telefonoExtTipo").val(!!idsJson.telTipo ? "Virtual" : "Físico");
    $("#equipoComputo").val(idsJson.equipoComputo).prop("disabled", idsJson.equipoComputoStatus == "Entregado" || idsJson.equipoComputoStatus == "Cancelado" || idsJson.equipoComputoStatus == null);
    $("#pinImpresion").val(idsJson.pin).prop("disabled", idsJson.emailstatus == "Pendiente"  );
    $("#equipoComputoStatus").val(idsJson.equipoComputoStatus == "Entregado" ? 3 : 0)//.prop("disabled", idsJson.equipoComputoStatus != "Agendado"); 
    //jefe  
    let jefeAnswered = !!idsJson.ubicacionFisica //si ya contestó el jefe deshabilitar el input
    $("#ubicacionFisica").val(idsJson.ubicacionFisica)//.prop("disabled", jefeAnswered);
    $("#banner").prop("disabled", ["Completado", "Cancelado"].includes(idsJson.bannerstatus));
    $("#soyleon").prop("disabled", ["Completado", "Cancelado"].includes(idsJson.soyleonstatus));
    $("#tickets").val(idsJson.tickets ? "Sí" : "No").prop("disabled", ["Completado", "Cancelado"].includes(idsJson.ticketsstatus));
    $("#solicitarExtension").val(idsJson.solicitarExt ? "Sí" : "No").prop("disabled", ["Completado", "Cancelado"].includes(idsJson.telstatus) || (idsJson.tipoOnboarding == "Promoción" && idsJson.tel));
    $("#telefonoExtTipo").val(!!idsJson.telTipo ? "Virtual" : "Físico").prop("disabled", ["Completado", "Cancelado"].includes(idsJson.telstatus) || !idsJson.solicitarExt)
    $("#note").prop("hidden", jefeAnswered);

    if (services.banner?.status == "Cancelado") $(`#updateModal-banner`).text("Cancelado")
    if (services.soyleon?.status == "Cancelado") $(`#updateModal-soyleon`).text("Cancelado")
    if (services.tickets?.status == "Cancelado") $(`#updateModal-tickets`).text("Cancelado")
    if (services.tel?.status == "Cancelado") $(`#updateModal-solicitarExt`).text("Cancelado")

    //updateSelectedOptions("banner", services.banner?.bannerModules);
    $("#banner").val(idsJson.banner ? "Sí" : "No")
    updateSelectedOptions("soyleon", services.soyleon?.soyLeonModules);


    const avatar = getAvatarHTML(user_name, user_role);
    $("#createCommentAvatar").css("background", avatar.style.background);
    $("#createCommentAvatar").text(avatar.textContent);

    //Manejo de la vista de jefes:

    //Validamos que tenga el rol de jefe y además que su id sea el mismo que el jefe de la solicitud
    if(window.user_roles.includes("257be0f8-c691-4336-a43e-2d592efdc172") && window.user_id == idsJson.idJefe){
        $('.boss-only').show();
        $('.no-boss-only').hide();
        $('.boss-only-input').prop('required', true);
    }else{
        $('.no-boss-only').show();
        $('.boss-only').hide();
        $('.boss-only-input').prop('required', false);
    }


    //Manejo de validaciones con múltiples roles
    function hasAnyOtherRoleThanEmail (){
        rolesWithoutEmail = window.TIRoles;

        rolesWithoutEmail = rolesWithoutEmail.filter((role) => role !== "ec1407a8-5b4c-4910-b757-9d60a3651a86");

        aditionalRoles = rolesWithoutEmail.reduce((prev, current) => { 
            return window.user_roles.includes(current) ? prev+1 : prev
        }, 0);

        return aditionalRoles;
    }

    
    //Si contiene al mismo tiempo el rol de encargado de email y otro más:
    if(hasAnyOtherRoleThanEmail() >= 1 && window.user_roles.includes("ec1407a8-5b4c-4910-b757-9d60a3651a86") ){
        if(services.email?.status === "Pendiente"){
            $(".inputDifferentFromEmail").prop("disabled", true);
            $(".inputDifferentFromEmail").prop("title", "Complete primero el email");
        }
    }

    //Si no es aquel al que le delegaron la entrega de equipo de computo
    if(idsJson.auxiliarEquipoComputo != window.user_uID){
        $("#computerService").hide();
        $("#equipoComputo").prop("required", false);
        $("#equipoComputo").prop("title", "Sólo el delegado puede entregar el equipo de computo");
    }else{
        $("#computerService").show();
    }

    if(idsJson.auxiliarEquipoComputo == null){
        $("#equipoComputo").prop("disabled", true);
        $("#equipoComputo").prop("title", "Selecciona primero al auxiliar que entregará el equipo de cómputo");
        $("#computerService").hide();
        $("#pinImpresion").prop("disabled", true);
        $("#pinImpresion").prop("title", "Selecciona primero al auxiliar que entregará el equipo de cómputo");
        $("#printPin").hide();
    }
    
    fetchAuxiliars(idsJson.auxiliarEquipoComputo);
    fetchComments();
}

$.fn.modal.Constructor.prototype._enforceFocus = function () { };

$('#updatecardModal').on('hidden.bs.modal', function () {
    $(':focus').blur();
    $(this).attr("aria-hidden", "true");
    serviceReqCancelled = false;
    cancelledService = ""
    $("#typeComment").val("");
}).on("shown.bs.modal", function () {
    $(this).attr("aria-hidden", "false");
});


function updateSelectedOptions(selectId, values = []) {
    const select = document.getElementById(selectId);
    if (!select) return;
    for (const option of select.options) {
        option.selected = values?.includes(option.text.trim());
    }
    select.dispatchEvent(new Event("change"));
}


async function fetchComments() {
    let url = `${baseurl}/api/ob-gestionar-comentario/obtener`;
    const requestId = $("#updatecardModal").attr("data-request-id");
    url += `?requestId=${requestId}`;

    makeAjaxCallWithJWT({
        baseUrl: baseurl,
        url: url,
        method: "GET",
        contentType: "application/json",
        beforeSend: function () {
        },
        success: function (response) {
            renderComments(response[0]?.result)
        },
        error: function (xhr, status, error) {
            let response = JSON.parse(xhr.responseText);
            showSnackbar("Hubo un problema cargando los comentarios", "error")
        }
    });

}

async function fetchAuxiliars(currentAuxUID) {
    window.currentAuxUID = currentAuxUID;

    let url = `${baseurl}/api/ob-gestionar-auxiliar-de-entrega/consultar`

    makeAjaxCallWithJWT({
        baseUrl: baseurl,
        url: url,
        method: "GET",
        contentType: "application/json",
        beforeSend: function () {
        },
        success: function (response) {
            renderAuxiliars(response.result, currentAuxUID)
        },
        error: function (xhr, status, error) {
            let response = JSON.parse(xhr.responseText);
            showSnackbar("Hubo un problema cargando a los auxiliares", "error")
        }
    });
}

async function postComment(comment) {
    let url = `${baseurl}/api/ob-gestionar-comentario/crear`;
    const requestId = $("#updatecardModal").attr("data-request-id");
    commentary = typeof comment === "string" ? comment : $("#typeComment").val().trim();
    const body = {
        requestId,
        commentary,
        createdBy: user_id
    };

    if (!requestId) return
    if (!commentary) return;

    makeAjaxCallWithJWT({
        baseUrl: baseurl,
        url: url,
        method: "POST",
        contentType: "application/json",
        data: JSON.stringify(body),
        beforeSend: function () {
        },
        success: function (response) {
            $("#typeComment").val("");
            fetchComments();
        },
        error: function (xhr, status, error) {
            let response = JSON.parse(xhr.responseText);
            showSnackbar(response.result.message, "error")
        }
    });

}

function getInitials(name) {
    return name.split(" ").map(word => word[0]).join("").toUpperCase().slice(0, 2);
}

function getAvatarHTML(username, role) {
    const avatar = document.createElement("div");
    avatar.classList.add("avatar", "fs-7");
    avatar.style.background = roleColor[role] || roleColor.JF;
    avatar.textContent = getInitials(username);
    return avatar;
}

function getCommentHTML(commentData) {
    function getTimeAgo(date) {
        const diffMs = new Date() - date;
        const diffMinutes = Math.floor(diffMs / (1000 * 60));
        const diffHours = Math.floor(diffMinutes / 60);
        const diffDays = Math.floor(diffHours / 24);

        if (diffMinutes < 1) return "Justo ahora";
        if (diffMinutes < 60) return `${diffMinutes}m`;
        if (diffHours < 24) return `${diffHours}h`;
        return `${diffDays}d`;
    }

    const timeAgo = getTimeAgo(new Date(commentData.createdAt));

    return `<div class="d-flex flex-column gap-1 comment"> 
                <div class="d-flex flex-row gap-2 align-items-center justify-content-between">
                    <div class="d-flex gap-2 flex-row align-items-center">
                        ${getAvatarHTML(commentData.createdBy, commentData.creatorArea).outerHTML}
                        <div class="text-dark text-left small text-truncate">
                            ${commentData.createdBy}
                        </div>
                    </div>
                    <div class="text-muted text-left small">
                        ${timeAgo}
                    </div>
                </div>
                <div class="text-dark text-left small">
                    ${commentData.commentary}
                </div>
            </div> `;
}

function getAuxiliarHTML(auxiliarData, currentAuxUID){
    return `<option ${auxiliarData.usUID == currentAuxUID ? "selected" : ""} value="${auxiliarData.usUID}">${auxiliarData.name}</option>`
}

function renderAuxiliars(auxiliarsArray = [], currentAuxUID){
    const auxiliarsSelect = $("#encargadoEquipoComputo");
    if (!auxiliarsArray.length) {
        auxiliarsSelect.html('<option value=""> No hay auxiliares para seleccionar.</option>')
        return
    }

    selectContent = `<option value="">Selecciona al encargado</option> `;

    selectContent += auxiliarsArray.map((e) => getAuxiliarHTML(e, currentAuxUID)).join('');

    auxiliarsSelect.html(selectContent);
}

function renderComments(commentsArray = []) {
    const $commentsContainer = $("#comments-list");
    if (!$commentsContainer.length) { return };
    if (!commentsArray.length) {
        $commentsContainer.html('No hay comentarios aún.')
        return
    }
    const sortedComments = commentsArray.sort((a, b) => new Date(b.createdAt) - new Date(a.createdAt));
    $commentsContainer.html(sortedComments.map(getCommentHTML).join(''));
}

const statusIDs = {
    Pendiente: 1,
    Completado: 2,
    Entregado: 3,
    Cancelado: 4,
    Agendado: 5,
    "En revisión": 6
}

function sendPut() {
    return new Promise((resolve, reject) => {
        const url = `${baseurl}/api/ob-gestionar-solicitud/modificar`;
        const requestId = $("#updatecardModal").attr("data-request-id");
        let body = {
            requestId,
            update: [],
            updatedBy: user_id
        }

        const keys = {
            "physical_location": $("#ubicacionFisica").val(),
            "anahuac_friend_ID": $("#amigoAnahuac").val(),
            //"email": $("#emailAnahuac").val() ? $("#emailAnahuac").val() + "@anahuac.mx" : "",
            //"computer": $("#equipoComputo").val(),
            //"extensionNumber": $("#telefonoExt").val(),
            "is_virtual_phone": !!$("#telefonoExtTipo").val() ? $("#telefonoExtTipo").val() === "Virtual" : undefined,
            "us_banner_id": $("#globalTalentId").val(),
        }

        if (currentCard.globalTalentId == keys.us_banner_id) delete keys.us_banner_id;

        for (var key in keys) {
            if (!(keys[key] === "" || keys[key] === undefined)) body.update.push({ field: key, value: keys[key] })
        }

        if (body.update.length) {
            makeAjaxCallWithJWT({
                baseUrl: baseurl,
                url: url,
                method: "PUT",
                contentType: "application/json",
                data: JSON.stringify(body),
                beforeSend: function () {
                },
                success: function (response) {
                    resolve(true)
                },
                error: function (xhr, status, error) {
                    let response = JSON.parse(xhr.responseText);
                    Swal.fire({
                        icon: "error",
                        title: "No se pudo guardar la solicitud:",
                        text: response.result.message
                    });
                    reject(error)
                }
            });
        } else {
            resolve(true)
        }
    });
}

function putAuxiliar() {
    return new Promise((resolve, reject) => {
        const url = `${baseurl}/api/ob-gestionar-auxiliar-de-entrega/guardarAuxiliar`;
        const requestID = $("#updatecardModal").attr("data-request-id");

        usUID = $("#encargadoEquipoComputo").val();

        let body = {
            requestID,
            usUID,
            //updatedBy: user_id
        }

        if (!!usUID && window.currentAuxUID != usUID) {
            makeAjaxCallWithJWT({
                baseUrl: baseurl,
                url: url,
                method: "PUT",
                contentType: "application/json",
                data: JSON.stringify(body),
                beforeSend: function () {
                },
                success: function (response) {
                    console.log(response);
                    resolve(true)
                },
                error: function (xhr, status, error) {
                    let response = JSON.parse(xhr.responseText);
                    console.log(response);
                    Swal.fire({
                        icon: "error",
                        title: "No se pudo guardar el auxiliar",
                        text: response.result.message
                    });
                    reject(error)
                }
            });
        } else {
            resolve(true)
        }
    });
}

function postServices() {
    return new Promise((resolve, reject) => {
        const url = `${baseurl}/api/ob-gestionar-servicio/crear`;
        const requestId = $("#updatecardModal").attr("data-request-id");
        let body = {
            "serviceRequests": [],
            "createdBy": user_id
        }

        const services_metadata = {
            "Soy León": {
                id: 1,
                alias: "soyleon",
                requirements: $("#soyleon").val()
            },
            "Banner": {
                id: 2,
                alias: "banner",
                requirements: []// $("#banner").val()
            },
            "Email": {
                id: 3,
                alias: "email",
                requirements: null
            },
            "Extensión telefónica": {
                id: 4,
                alias: "solicitarExtension",
                requirements: [$("#solicitarExtension").val() == "Virtual"]
            },
            "GLPI": {
                id: 5,
                alias: "tickets",
                requirements: []
            },
            "Equipo de cómputo": {
                id: 6,
                alias: "compu",
                requirements: null
            }
        };

        for (var key in services_metadata) {
            let service = services_metadata[key]
            let currentService = currentCard.serviceRequests.find(request => request.service === key)
            if (currentService) continue //saltarse aquellos servicios que ya existen
            if ($("#" + service.alias).val() && $("#" + service.alias).val() == "Sí" || $("#" + service.alias).val() && $("#" + service.alias).val().length > 0 && service.alias === "soyleon") body.serviceRequests.push(//checa que el input con el id correspondiente tenga datos
                {
                    requestId,
                    serviceId: service.id,
                    requerimentsId: service.requirements
                }
            );
        }

        //Agregar el pin de impresión
        if($("#pinImpresion").val() != "" && $("#pinImpresion").val() != "0" && $("#pinImpresion").val() != "0000" && $("#pinImpresion").val() && $("#pinImpresion").val() != currentCard.printPin){
            body.serviceRequests.push({
                    requestId,
                    "serviceId": 0,
                    "requerimentsId": [$("#pinImpresion").val()]
                })
        }

        if (body.serviceRequests.length) {
            makeAjaxCallWithJWT({
                baseUrl: baseurl,
                url: url,
                method: "POST",
                contentType: "application/json",
                data: JSON.stringify(body),
                beforeSend: function () {
                },
                success: function (response) {
                    resolve(true)
                },
                error: function (xhr, status, error) {
                    let response = JSON.parse(xhr.responseText);
                    Swal.fire({
                        icon: "error",
                        title: "No se pudo guardar la solicitud:",
                        text: response.result.message
                    });
                    reject(error)
                }
            });
        } else {
            resolve(true)
        }
    });
}


let serviceReqCancelled = false
let cancelledService = ""

function putServices() {
    return new Promise((resolve, reject) => {
        const url = `${baseurl}/api/ob-gestionar-servicio/actualizar`;

        const services = getServices(currentCard)

        const body = {
            "serviceRequests": [],
            "updatedBy": user_id
        }

        const equipoComputo = $('#equipoComputo').val();
        const emailAnahuac = $("#emailAnahuac").val() ? $("#emailAnahuac").val() + "@anahuac.mx" : "";
        const telefonoExt = $('#telefonoExt').val();
        const soyLeonModules = $("#soyleon").val();
        const soyLeonModulesNames = $('#soyleon option:selected').map(function () {
            return $(this).text();
        }).get();
        let banner = $("#banner").val()
        let tickets = $("#tickets").val()
        let solicitarExtension = $("#solicitarExtension").val()


        const entregado = $('#updateModal-computerstatus').attr("data-status") == 3;

        if (services.computer && equipoComputo && (currentCard.computer != equipoComputo || entregado)) {
            let computerStatus = statusIDs[services.computer.status]

            if ($('#equipoComputo').val()) {
                if (services.computer.status != "Agendado") computerStatus = 2
            }

            if (entregado) computerStatus = 3

            let requirement =
            {
                "serviceRequestId": services.computer.serviceRequestId,
                "serviceRequestStatusId": computerStatus,
                "requeriment": equipoComputo
            }
            if (entregado) requirement.requeriment = null

            body.serviceRequests.push(
                requirement
            )

        }

        if (services.email && currentCard.email != emailAnahuac && emailAnahuac) {
            body.serviceRequests.push(
                {
                    "serviceRequestId": services.email.serviceRequestId,
                    "serviceRequestStatusId": 2,
                    "requeriment": emailAnahuac
                }
            )
        }

        if (services.tel && currentCard.extensionNumber != telefonoExt && telefonoExt !== undefined && cancelledService !== "extensión telefónica" && services.tel.status == "Pendiente") {
            body.serviceRequests.push(
                {
                    "serviceRequestId": services.tel.serviceRequestId,
                    "serviceRequestStatusId": 2,
                    "requeriment": telefonoExt
                }
            )
        }
        else if (solicitarExtension == "No" && services.tel) {
            body.serviceRequests.push(
                {
                    "serviceRequestId": services.tel.serviceRequestId,
                    "serviceRequestStatusId": 4,
                    "requeriment": []
                }
            )
        }

        if (services.banner && banner) {
            if (banner == "No") {
                body.serviceRequests.push(
                    {
                        "serviceRequestId": services.banner.serviceRequestId,
                        "serviceRequestStatusId": 4,
                        "requeriment": []
                    }
                )
            }
        }

        if (services.tickets && tickets) {
            if (tickets == "No") {
                body.serviceRequests.push(
                    {
                        "serviceRequestId": services.tickets.serviceRequestId,
                        "serviceRequestStatusId": 4,
                        "requeriment": []
                    }
                )
            }
        }


       if (services.soyleon &&
            !isArrayIdentical(services.soyleon?.soyLeonModules, soyLeonModulesNames)
            && soyLeonModules !== undefined && services.soyleon?.soyLeonModules) {
            body.serviceRequests.push(
                {
                    "serviceRequestId": services.soyleon.serviceRequestId,
                    "serviceRequestStatusId": soyLeonModules.length == 0 ? 4 : 1, //si soyleon modules es null, cancelar
                    "requeriment": soyLeonModules || []
                }
            )
        }


        const serviceKeys = ["banner", "soyleon", "tickets", "computer", "tel"];

        serviceKeys.forEach(key => {
            const statusElement = $(`#updateModal-${key}status`);
            const status = statusElement.attr("data-status");

            if (services[key] && status && statusElement.attr("title") !== services[key].status) {
                body.serviceRequests.push({
                    "serviceRequestId": services[key].serviceRequestId,
                    "serviceRequestStatusId": status
                })
            }
        });



        if (body.serviceRequests.length) {
            makeAjaxCallWithJWT({
                url: url,
                baseUrl: baseurl,
                method: "PUT",
                contentType: "application/json",
                data: JSON.stringify(body),
                beforeSend: function () {
                    $("#submitModalButton").prop("disabled", true);
                },
                success: function (response) {
                    $("#submitModalButton").prop("disabled", false);
                    resolve(true)
                },
                error: function (xhr, status, error) {
                    let response = JSON.parse(xhr.responseText);
                    $("#submitModalButton").prop("disabled", false);
                    Swal.fire({
                        icon: "error",
                        title: "No se pudo guardar la solicitud:",
                        text: response.result.message
                    });
                    reject(error)
                }
            });
        } else {
            resolve(true)
        }
    });
}

function isArrayIdentical(a, b, ignoreCase = false) {
    if (!Array.isArray(a) || !Array.isArray(b)) return false;
    if (a.length !== b.length) return false;
 
    const normalize = val => ignoreCase ? val.toLowerCase() : val;
 
    const arrA = [...a].map(normalize).sort();
    const arrB = [...b].map(normalize).sort();
 
    return arrA.every((val, i) => val === arrB[i]);
}


async function handleSubmit(event) {
    showSnackbar("Cargando");
    event.preventDefault();
    $("#submitModalButton").prop("disabled");


    if (serviceReqCancelled) {
        $("#updatecardModal").modal("hide").one('hidden.bs.modal', function () {
            $('.modal-backdrop').remove();
        });

        Swal.fire({
            title: `¿Por qué quiere cancelar el servicio adicional?`,
            input: "textarea",
            inputAttributes: {
                maxlength: 200
            },
            inputPlaceholder: "Escriba el motivo de la cancelación aquí...",
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: "Enviar",
            cancelButtonText: "Cancelar",
            inputValidator: value => {
                if (!value.trim()) {
                    return "Debes escribir un comentario.";
                }
            }
        }).then(result => {
            if (result.value) {
                postComment(`Motivo de cancelación de ${cancelledService}: ${result.value}`)
                showSnackbar("Cargando");
                updateRequest()
            }
        });
    }
    else {
        updateRequest()
    }
}

function updateRequest() {


    Promise.all([postServices(), putServices(), sendPut(), putAuxiliar()])
        .then(results => {
            if (results.every(result => result === true)) {
                postComment();
                //updateRequestStatus(currentCard.requestId)
                $("#updatecardModal").modal("hide").one('hidden.bs.modal', function () {
                    $('.modal-backdrop').remove();
                });
            
                fetchCards();
                showSnackbar("Cambios guardados", "success")
            } else {
            }
        })
        .catch(error => {
            console.log(error)
        });
}

function showSnackbar(message, type = "info", delay = 3000) {
    
    swal({
    toast: true,
    position: 'top-center',
    type,
    title: message,
    showConfirmButton: false,
    timer: delay,
    timerProgressBar: true,
    didOpen: function didOpen(toast) {
      toast.addEventListener('mouseenter', Swal.stopTimer);
      toast.addEventListener('mouseleave', Swal.resumeTimer);
    }
  });

}



function showMessagesFromQueue() {
    const messagesQueue = JSON.parse(sessionStorage.getItem('messagesQueue')) || [];

    if (messagesQueue.length > 0) {
        messagesQueue.forEach(item => {
            showSnackbar(item.message, item.type, 2000);
        });

        sessionStorage.removeItem('messagesQueue');
    }
}



$('input[inputmode="numeric"]').on("input", function () {
    $(this).val($(this).val().replace(/\D/g, ""));
});


$('input[name^="email"]').on("input", function () {
    $(this).val($(this).val().toLowerCase());
    $(this).val($(this).val().replace(/[^a-zA-Z0-9.]/g, ""));
});


$('input#ubicacionFisica').on("input", function () {
    $(this).val($(this).val().replace(/[^a-zA-ZÀ-ÿ0-9,. ]/g, ""));
});

$('input#equipoComputo').on("input", function () {
    $(this).val($(this).val().replace(/[^a-zA-ZÀ-ÿ0-9 ]/g, ""));
});



$('input[data-filter]').on("input", function () {
    let pattern = $(this).attr("pattern");
    if (!pattern) return;

    let regex = new RegExp(`^${pattern}$`);
    if (!regex.test($(this).val())) {
        $(this).val($(this).val().slice(0, -1));
    }
});

$("#solicitarExtension").on("change", function () {
    $("#telefonoExtTipo").prop("disabled", $(this).val() != "Sí")
})


$(document).ready(function () {
    showMessagesFromQueue()

    $("#sendComment").on("click", postComment);

    $("#modalForm").on("submit", handleSubmit);

    $("textarea").on("focus", function () {
        let remToPx = parseFloat(getComputedStyle(document.documentElement).fontSize);
        let offset = 6 * remToPx;

        let modalBody = $(this).closest(".modal-body");

        let inputTop = $(this).position().top;
        let maxScroll = modalBody.scrollTop();
        let minScroll = modalBody.innerHeight() - (inputTop + offset) * window.devicePixelRatio;

        modalBody.animate({
            scrollTop:
                Math.max(inputTop, Math.min(minScroll, maxScroll))
        }, 200);
    });


    const solicitudId = new URLSearchParams(window.location.search).get("id");
    if (solicitudId) {
        makeAjaxCallWithJWT({
            baseUrl: baseurl,
            url: `${baseurl}/api/ob-gestionar-solicitud/consultar?requestId=${solicitudId}`,
            method: "GET",
            success: function (response) {
                let request = response.result[0]
                if (request) {
                    $("#updatecardModal").modal("show");
                    updateModalFields(response.result[0]);
                }
            },
            error: function (xhr, status, error) {
                let response = JSON.parse(xhr.responseText);
                showSnackbar(response.result.message, "error")
            }
        });
    }

    var tdWidth = $('#banner').width();

    $(document).on("focus", ".select2-search__field", function () {
        tdWidth = $('#banner').width();
        $('.select2-search__field').css('max-width', tdWidth);
        $('.select2-dropdown.select2-dropdown--below').css('max-width', tdWidth);
    });

    $(document).on("input", ".select2-search__field", function () {
        var tdWidth = $('#banner').closest('td').width();


        let currentVal = $(this).val();
        currentVal = currentVal.replace(/[^a-zA-ZÀ-ÿ0-9 ]/g, "");
        maxlength = 40
        if (currentVal.length > maxlength) currentVal = currentVal.substring(0, maxlength);
        $(this).val(currentVal);
    });



});
