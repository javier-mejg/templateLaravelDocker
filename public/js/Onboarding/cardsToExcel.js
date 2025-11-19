let selectedCardIds = new Set();



$(document).on("click", ".onboardingCard", function (event) {
    const requestId = $(this).data("requestid");
    if (!event.ctrlKey && !selectedCardIds.has(requestId)) selectedCardIds.clear();
    selectCard(requestId);
});

function selectCard(requestId) { 

    if (selectedCardIds.has(requestId)) { 
        selectedCardIds.delete(requestId);
    } else { 
        selectedCardIds.add(requestId);
    }
 

    $(".onboardingCard").toArray().forEach(element => {
        const $element = $(element)
        const id = $($element).data("requestid")
        $element.toggleClass("selected", selectedCardIds.has(id))
    })
}

async function copyToClipboard(string) {
    try {
        await navigator.clipboard.writeText(string);
        showSnackbar("Copiado al portapapeles!", "info");
    } catch (err) {
        console.error("Error al copiar:", err);
        showSnackbar("No se pudo copiar, revisa permisos del navegador.", "warning");
    }
}


$(document).on("click", ".copyButton", function () {
    if (selectedCardIds.size > 1) {
        const selectedCards = Array.from(selectedCardIds).map(id =>
            $(`.onboardingCard[data-requestid='${id}']`).data("card")
        );
        copyToClipboard(cardsToExcel(selectedCards));
    } else {
        const cardData = $(this).closest(".onboardingCard").data("card");
        copyToClipboard(cardsToExcel(cardData));
    }
});


$(document).on("keydown", function (event) {
    if (event.ctrlKey && event.key === "c" && selectedCardIds.size > 0) {
        const selectedCards = Array.from(selectedCardIds).map(id =>
            $(`.onboardingCard[data-requestid='${id}']`).data("card")
        );
        copyToClipboard(cardsToExcel(selectedCards));
    }
});


function formatCard(card) {
    const services = {
        email: card.serviceRequests.find(request => request.service === "Email"),
        computer: card.serviceRequests.find(request => request.service === "Equipo de cómputo"),
        tel: card.serviceRequests.find(request => request.service === "Extensión telefónica"),
        soyleon: card.serviceRequests.find(request => request.service === "Soy León"),
        banner: card.serviceRequests.find(request => request.service === "Banner"),
        tickets: card.serviceRequests.find(request => request.service === "Tickets")
    }

    const newObj = {
        "Nombre": card.name,
        "Tipo": card.type,
        "Estado": card.status,
        "Colaborador reemplazado": card.replacedName,
        "Área": card.area,
        "Puesto": card.position,
        "Fecha de ingreso": card.entryDate,
        "Fecha de creación": moment(card.createdAt).format('D [de] MMMM [de] YYYY, HH:mm'),
        "Creado por": card.createdBy,
        "Fecha de actualización": moment(card.updatedAt).format('D [de] MMMM [de] YYYY, HH:mm'),
        "Actualizado por": card.updatedBy,
        "Jefe": card.boss,
        "Amigo Anáhuac": card.anahuacFriend,
        "Meses de prueba": card.trialMonths,
        "Correo electrónico": card.email,
        "Sugerencia correo 1": !!services.email && !!services.email?.emailSuggestions ? services.email?.emailSuggestions.length > 0 ? services.email?.emailSuggestions[0] : "Sin sugerencia" : "Sin sugerencia",
        "Sugerencia correo 2": !!services.email && !!services.email?.emailSuggestions ? services.email?.emailSuggestions.length > 0 ? services.email?.emailSuggestions[1] : "Sin sugerencia" : "Sin sugerencia",
        "Computadora asignada": card.computer,
        "Estado computadora": services.computer?.status,
        "Número de extensión": !!services.tel ? card.extensionNumber : "N/A",
        "Tipo de extensión": !!services.tel ? card.isVirtualPhone ? "Virtual" : "Físico" : "N/A",
        "Ubicación física": card.physicalLocation,
        "Banner": !!services.banner ? "Si" : "No" ,
        "Soyleon": services.soyleon?.soyLeonModules || "No asignado",
        "Tickets": !!services.tickets ? "Sí" : "No",
        "Solicitar extensión": !!services.tel ? "Sí" : "No",
        "ID GlobalTalent": card.globalTalentId || "No asignado"
    }
    return newObj
}


function objsToExcel(objArray) {
    if (!objArray.length) objArray = [objArray]
    if (typeof objArray === "string") objArray = [JSON.parse(objArray)];

    const headers = Object.keys(objArray[0]).join("\t");
    const rows = objArray.map(obj => Object.values(obj).join("\t")).join("\n");
    const tableText = `${headers}\n${rows}`;
    return tableText
}

function cardsToExcel(cards) {
    if (!cards.length) cards = [cards]
    cards.forEach((card, index) => {
        cards[index] = formatCard(card)
    });
    return objsToExcel(cards)
}