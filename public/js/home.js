const recupererToutesDonnees = document.getElementById('recuperer-toutes-donnees');
let missionsContainer = document.getElementById('missions-container');
const countryFilter = document.getElementById('country');
const typeFilter = document.getElementById('type');
const specialityFilter = document.getElementById('speciality');
const statusFilter = document.getElementById('status');
const agentFilter = document.getElementById('agent');
const filterForm = document.getElementById('filters-form');
const searchForm = document.getElementById('search-form');

function sanitizeHtml(text){
    // Créez un élément HTML temporaire de type "div"
    const tempHtml = document.createElement('span');
    
    // Affectez le texte reçu en tant que contenu texte de l'élément "tempHtml"
    tempHtml.textContent = text;
    
    // Utilisez .innerHTML pour récupérer le contenu de "tempHtml"
    // Cela va "neutraliser" ou "échapper" tout code HTML potentiellement malveillant
    return tempHtml.innerHTML;
}

function displayMissions(missions, resDom) {
    // Param : json des missions +  element du dom pour print le res

    try {
        // Sélection de l'élément conteneur
        let container = resDom;

        // Vérifier si l'élément conteneur existe
        if (!container) {
            throw new Error('L\'élément conteneur n\'existe pas.');
        }

        // Vider le container
        container.innerHTML = '';

        // Boucle à travers chaque mission dans le tableau
        missions.forEach((mission, index) => {
            // Créer une div pour chaque mission
            const missionDiv = document.createElement('div');
            missionDiv.classList.add('mission'); // Ajouter une classe CSS si nécessaire

            // Remplir la div avec les informations de la mission
            missionDiv.innerHTML = `
                <h2>${sanitizeHtml(mission.title)}</h2>
                <p><strong>Code Name:</strong> ${sanitizeHtml(mission.codeName)}</p>
                <p><strong>Description:</strong> ${sanitizeHtml(mission.description)}</p>
                <p><strong>Begin Date:</strong> ${sanitizeHtml(mission.beginDate)}</p>
                <p><strong>End Date:</strong> ${sanitizeHtml(mission.endDate)}</p>
                <a href="./mission?idMission=${mission.idMission}"> Voir la mission </a>
            `;

            // Ajouter la div de mission au conteneur
            container.appendChild(missionDiv);
        });
    } catch (error) {
        console.error('Une erreur s\'est produite lors de l\'affichage des missions:', error);
        // Gérer l'erreur ici (si nécessaire)
    }
}

function displayFilters(filters, resDom) {
    // Param : json des options avec les clés 'id' et clé 'valeur' +  element du dom pour print le res
    // Return : Print les options du filtre dans le dom

    try {
        // Sélection de l'élément conteneur
        let container = resDom;

        // Vérifier si l'élément conteneur existe
        if (!container) {
            throw new Error('L\'élément conteneur n\'existe pas.');
        }

        // Boucle à travers chaque mission dans le tableau
        filters.forEach((item) => {
            // Créer une option pour chaque résultat dont la valeur form est l'id
            let option = document.createElement("option");
            option.value = sanitizeHtml(item.id);
            option.text = sanitizeHtml(item.valeur);
            container.appendChild(option);

        });
    } catch (error) {
        console.error('Une erreur s\'est produite lors de l\'affichage des missions:', error);
        // Gérer l'erreur ici (si nécessaire)
    }
}
function fetchData(apiUrl, ResAction, resDom) {
    // Param : url à interroger + fonction de traitement du résultat + element du dom pour print le res

    fetch(apiUrl)    // Utilisation de fetch pour interroger l'API
        .then((response) => {
            if (response.ok) {
                return response.json()
            } else {
                console.error('Erreur de récupération des données:' + response.status)
            }
        })
        .then((data) => {
            ResAction(data, resDom)
        })
        .catch((error) => { console.error('Erreur de récupération des données:' + error) });
}

recupererToutesDonnees.addEventListener('click', () => {
    let missionUrl = './apiGetmissions';
    fetchData(missionUrl, displayMissions, missionsContainer)
});

window.addEventListener("load", () => {
    // Evènements au chargement de la page

    let countryUrl = './apigetcountry';
    fetchData(countryUrl, displayFilters, countryFilter)

    let typeUrl = './apigettype';
    fetchData(typeUrl, displayFilters, typeFilter)

    let specialityUrl = './apigetspeciality';
    fetchData(specialityUrl, displayFilters, specialityFilter)

    let statusUrl = './apigetstatus';
    fetchData(statusUrl, displayFilters, statusFilter)

    let agentUrl = './apigetagent';
    fetchData(agentUrl, displayFilters, agentFilter)

    let missionUrl = './apiGetmissions';
    fetchData(missionUrl, displayMissions, missionsContainer)
})

filterForm.addEventListener('submit', (event) => {
    // Evènements au lancement du filtre

    event.preventDefault();
    // Récupérer les données du formulaire
    var formData = new FormData(filterForm);

    // Effectuer la requête Fetch
    let missionUrl = './apigetselectedmissions?' + new URLSearchParams(formData).toString();
    fetchData(missionUrl, displayMissions, missionsContainer)
})

searchForm.addEventListener('submit', (event) => {
    // Evènements au lancement de la recherche

    event.preventDefault();
    // Récupérer les données du formulaire
    var formData = new FormData(searchForm);

    // Effectuer la requête Fetch
    let missionUrl = './apigetsearchmissions?' + new URLSearchParams(formData).toString();
    fetchData(missionUrl, displayMissions, missionsContainer)
})