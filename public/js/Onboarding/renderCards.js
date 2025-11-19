//renderear y filtrar tarjetas. las tarjetas son filtradas automaticamente de acuerdo al atributo data-filter de un input o un select
//la funcion updateModalFields esta en updateCardModal.js



const baseurl = "../.."

let roleColor = {
    "CH": "orange",
    "TI": "blue",
    "PC": "blue",
    "JI": "lightseagreen"
};
roleColor["Servicios de Tecnología"] = roleColor.TI
roleColor['4abfcf50-c745-4c73-ab28-d256f29f3693'] = roleColor.TI
roleColor['4b52f81e-e167-48fc-9f0c-d2512839d20b'] = roleColor.TI; // Onboarding TI encargado de Banner
roleColor['4fca4081-2cd7-4a98-ad75-0cfab2321bd3'] = roleColor.TI; // Onboarding TI encargado de Soy Leon
roleColor['ebaf4c76-a146-4f41-8833-8becdbf817a1'] = roleColor.TI; // Onboarding TI encargado de GLPI

roleColor['ec1407a8-5b4c-4910-b757-9d60a3651a86'] = roleColor.TI; // Onboarding TI encargado de email
roleColor['d05ee3c5-2376-4ad8-bd7d-393d9d36555d'] = roleColor.TI; // Onboarding TI encargado de Telefono
roleColor['1676d7ad-c1b2-4fbb-90c9-1ea3ebc348e5'] = roleColor.TI; // Onboarding TI encargado de Equipo de computo

roleColor["Capital Humano"] = roleColor.CH
roleColor['cd756d1a-9efc-424a-b66a-5f512945d27f'] = roleColor.CH
roleColor['257be0f8-c691-4336-a43e-2d592efdc172'] = roleColor.JI

let roleTooltip = {
    CH: "Capital Humano",
    'cd756d1a-9efc-424a-b66a-5f512945d27f': "Capital Humano",

    JI: "Jefe Inmediato",
    '257be0f8-c691-4336-a43e-2d592efdc172': "Jefe Inmediato",

    "TI": "Servicios de Tecnología",
    "PC": "Equipo de cómputo",

    '4b52f81e-e167-48fc-9f0c-d2512839d20b': "TI (encargado de Banner)", // Onboarding TI encargado de Banner
    '4fca4081-2cd7-4a98-ad75-0cfab2321bd3': "TI (encargado de Soy León)", // Onboarding TI encargado de Soy Leon
    'ebaf4c76-a146-4f41-8833-8becdbf817a1': "TI (encargado de GLPI)", // Onboarding TI encargado de GLPI

    'ec1407a8-5b4c-4910-b757-9d60a3651a86': "TI (encargado de correo electrónico)", // Onboarding TI encargado de email
    'd05ee3c5-2376-4ad8-bd7d-393d9d36555d': "TI (encargado de teléfono)", // Onboarding TI encargado de Telefono
    '1676d7ad-c1b2-4fbb-90c9-1ea3ebc348e5': "TI (encargado de equipo de cómputo)", // Onboarding TI encargado de Equipo de computo
}

const typeColor = {
    "Nuevo Ingreso": "success",
    "Nuevo ingreso": "success",
    "Promoción": "primary",
    "Reemplazo": "purple"
};

// rendering 
function getCardHTML(card) {

    let pendientesDivs = ''
    let pending = getPendingRoles(card)
    pending.forEach(role => {
        const avatar = `<div class="avatar fs-7" style="background:${roleColor[role] || 'gray'}" title="${roleTooltip[role]}">${role}</div>`
        pendientesDivs += avatar;
    });


    const progress = getProgress(card)

    let progressColor = progress < 100 ? "warning progress-bar-striped" : "info progress-bar-striped progress-bar-animated";
    if (card.globalTalentId && progress == 100 || card.status == "Finalizado") progressColor = "success"
    if (card.status == "Cancelado") progressColor = "danger"

    return `
    <div class="p-1 col-12 col-sm-6 col-md-4 col-xl-3">
        <div class="h-100 shadow rounded-xxl">
            <div class="onboardingCard h-100 py-2 px-2 bg-white rounded-xxl position-relative d-flex flex-column gap-1 justify-content-between"
                data-target="#updatecardModal"
                data-requestid="${card.requestId}"
                data-card='${JSON.stringify(card)}'>
                <div class="position-absolute t-0 r-0 m-1 onboardingCard-buttons d-flex flex-column">
                    <button class="editButton btn btn-transparent rounded-pill p-1 shadowless fs-5" title="Editar"
                        data-toggle="modal" data-backdrop="static" data-target="#updatecardModal">
                        <i class="far fa-${['Cancelado', 'Finalizado'].includes(card.status) ? 'eye' : 'edit'}"></i>
                    </button>

                    <button class="copyButton btn btn-transparent rounded-pill p-1 shadowless fs-5" title="Copiar">
                        <i class="far fa-copy"></i>
                    </button>
                </div>

                <div>
                    <div class="progress mb-2" style="height: 0.25rem;">
                        <div class="progress-bar bg-${progressColor}" role="progressbar" style="width: ${progress}%"
                            aria-valuenow="${progress}" aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>

                    <div class="d-flex gap-1 align-items-center overflow-hidden">
                        <div class="d-flex gap-1 align-items-center overflow-hidden">
                            <span class="badge badge-${typeColor[card.type] || 'dark'}" aria-label="smallCard-type">${card.type}</span>
                            <span class="badge badge-light" aria-label="smallCard-area">${card.area}</span>
                        </div> 
                        ${/*<div class="ml-auto w-fit">
                            ${card.globalTalentId ?
                                `ID: ${card.globalTalentId || ""}`
                            : ''}
                        </div> */""}
                    </div> 

                    <div aria-label="smallCard-name">
                        <span class="text-muted" aria-label="smallCard-requestId">#${card.requestId}</span>
                        ${card.name} 
                    </div>                   
                </div>
                <div class="d-flex w-100 justify-content-between">
                    <div class="small text-muted" aria-label="smallCard-requestId">${moment(card.entryDate, "YYYY-MM-DD").format("DD-MM-YY")}</div>
                    <div class="d-flex flex-row-reverse gap-1 align-items-center"> 
                    ${pendientesDivs ? 'pendientes' + pendientesDivs : ''} 
                    </div>
                </div>
            </div>
        </div>
    </div>`;
}

function renderCards(cards) {
    const container = document.getElementById("cardsContainer");

    if (!cards.length) {
        container.innerHTML = '<div class="mx-auto">No se encontraron solicitudes.</div>'
        return
    }
    container.innerHTML = "";

    cards.forEach(card => {
        container.insertAdjacentHTML("beforeend", getCardHTML(card));
    });
    document.querySelectorAll(".onboardingCard").forEach(card => {
        card.addEventListener("click", function () {
            const cardData = JSON.parse(this.getAttribute("data-card"));
            updateModalFields(cardData);
        });
    });

    updatePagination()
}

function getProgress(card) {
    const totalFields = 1 + card.serviceRequests.length; //ubicacionfisica + servicios adicionales
    let completedFields = 0;

    //if (card.anahuacFriend) completedFields++;
    if (card.physicalLocation) completedFields++;

    card.serviceRequests.forEach(({ service, status }) => {
        if (service === "Equipo de cómputo") {
            if (status === "Completado") completedFields += 0.5;
            else if (status === "Agendado") completedFields += 0.75;
            else if (status === "Entregado" || status === "Cancelado") completedFields++;
        }
        else if (status === "Completado" || status === "Cancelado") completedFields++;

    });

    return Math.floor((completedFields / totalFields) * 100);
}

function getPendingRoles(card) {
    const pending = [];

    if ((card.needsAnahuacFriend && !card.anahuacFriend) || !card.globalTalentId && card.status === "Completado") pending.push("CH"); 
    if (card.serviceRequests.some(sr => !(sr.status == "Completado" || sr.status == "Entregado" || sr.status == "Cancelado"))) pending.push("TI");
    if (!card.physicalLocation) pending.push("JI");
    if (card.serviceRequests.some(sr =>  sr.status == "Completado" && sr.service == "Equipo de cómputo")) pending.push("PC");

    return pending;
}

//filter cards 

let filters = {};

function filterCards() {
    filters = {};
    document.querySelectorAll("[data-filter]").forEach(element => {
        const key = element.dataset.filter;
        const value = element.value.trim();
        if (value) filters[key] = value;
    });

    if (filters.entryDate) {
        const index = filters.entryDate;
        if (dateRanges[index]) {
            filters.firstDate = dateRanges[index].start;
            filters.lastDate = dateRanges[index].end;
            delete filters.entryDate;
        }
    }

    fetchCards();
}


const dateRanges = [
    {
        name: "Este mes",
        start: moment().startOf("month").format("YYYY-MM-DD"),
        end: moment().endOf("month").format("YYYY-MM-DD")
    },
    {
        name: "El mes siguiente",
        start: moment().add(1, "month").startOf("month").format("YYYY-MM-DD"),
        end: moment().add(1, "month").endOf("month").format("YYYY-MM-DD")
    },
    {
        name: "El mes pasado",
        start: moment().subtract(1, "month").startOf("month").format("YYYY-MM-DD"),
        end: moment().subtract(1, "month").endOf("month").format("YYYY-MM-DD")
    },
    {
        name: "Este trimestre",
        start: moment().startOf("quarter").format("YYYY-MM-DD"),
        end: moment().endOf("quarter").format("YYYY-MM-DD")
    },
    {
        name: "El trimestre pasado",
        start: moment().subtract(1, "quarter").startOf("quarter").format("YYYY-MM-DD"),
        end: moment().subtract(1, "quarter").endOf("quarter").format("YYYY-MM-DD")
    },
    {
        name: "Hace un año",
        start: moment().subtract(1, "year").startOf("month").format("YYYY-MM-DD"),
        end: moment().subtract(1, "year").endOf("month").format("YYYY-MM-DD")
    },
    {
        name: "Más de hace 1 año",
        start: moment(0).format("YYYY-MM-DD"),
        end: moment().subtract(1, "year").startOf("year").format("YYYY-MM-DD")
    }
];


dateRanges.forEach((range, index) => {
    const option = $('<option>', {
        value: index,
        text: range.name
    });

    const $selector = $('[data-filter=entryDate]');
    if ($selector.length) $selector.append(option);
});

function debounce(func, delay) {
    let timer;
    return function (...args) {
        clearTimeout(timer);
        timer = setTimeout(() => func.apply(this, args), delay);
    };
}

const debouncedFilterCards = debounce(filterCards, 500);

function addFilterEventListeners() {
    $('[data-filter]').on('input change', debouncedFilterCards);
}

// fetching

let currentPage = 1;
let lastPage = 12;

function fetchCards() {
    const queryString = Object.keys(filters).length > 0 ? `&${$.param(filters)}` : "";
    const url = `${baseurl}/api/ob-gestionar-solicitud/consultar?perPage=12&page=${currentPage}${queryString}&orderBy=["requestId-desc","entryDate-desc"]${fetchCardsUrlParam}`;
    fetch(baseurl+"/api/generar-jwt", {
        method: 'POST'
    }).then(response => response.json())
    .then(data => {
        fetch(url, {
            
            headers: {Authorization: `Bearer ${data.token}`}
          })
            .then(response => response.json())
            .then(data => {
                lastPage = data.lastPage;
                renderCards(data.result);
            })
            .catch(error => {
                renderCards([]);
                const container = document.getElementById("cardsContainer");
                container.innerHTML = '<div class="mx-auto">Hubo un problema obteniendo las solicitudes.</div>'
            }
        );
    });
    
}

function updateCurrentPage(page) {
    page = Math.max(1, Math.min(page, lastPage))
    if (currentPage == page) return
    currentPage = page;
    fetchCards();
}

function updatePagination() {
    const length = $(".page-number").length
    const half = Math.ceil(length / 2);

    $(".page-number").each(function () {
        const index = $(this).index();

        let displayNumber = index
        if (currentPage > half && currentPage <= lastPage - half) {
            displayNumber = currentPage + index - half
        }
        else if (currentPage > lastPage - half) {
            displayNumber = lastPage + index - length
        }

        $(this).attr('data-page', displayNumber);
        $(this).find("a").text(displayNumber);

        $(this).toggleClass("active", $(this).attr("data-page") == currentPage);
        $(this).toggleClass("d-none", $(this).attr("data-page") < 1 || $(this).attr("data-page") > lastPage);
    });
}


$(".prev").click(function () {
    updateCurrentPage(currentPage - 1);
});

$(".next").click(function () {
    updateCurrentPage(currentPage + 1);
});

$(".page-number").click(function () {
    //updateCurrentPage($(this).data("page"));
});

function fetchOptions(selector, tableName, ar_uID = "") {
    const $selectElement = $(selector);
    $selectElement.html('<option selected value="">Seleccione...</option>');

    makeAjaxCallWithJWT({
        baseUrl: baseurl,
        url: `${baseurl}/api/ob-gestionar-catalogo/consultar`,
        method: "POST",
        contentType: "application/json",
        data: JSON.stringify({
            "catalogsRequests": [{
                "tableName": tableName,
                "sort": "ASC",
                "sortBy": "nombreArea",
                "filter": ""
            }],
            "ar_uID": ar_uID
        }),
        success: function (data) {

            let sortedArray = data.result[tableName]?.sort((a, b) => a.name.localeCompare(b.name));

            sortedArray?.forEach(el => {
                const $option = $('<option>', {
                    //'data-json': JSON.stringify(el),
                    //'value': el.id || el.uID,
                    'text': el.name
                });
                $selectElement.append($option);
            });
        },
        error: function (xhr, status, error) {
        }
    });
}


$(document).ready(function () {
    fetchOptions('[data-filter="areaName"]', "areas");

    addFilterEventListeners();
});