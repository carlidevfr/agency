// On initialise les variables

let listPlanques;
let selectAddPlanque
let listAgents;
let selectAddMainAgent;
let addElementOtherAgents;
let listCibles;
let listContacts;
let selectAddContacts
let idSpe;


document.addEventListener('DOMContentLoaded', function () {
    // On récupère les données json
    listPlanques = JSON.parse(document.getElementById('listPlanques').getAttribute('data'));
    listAgents = JSON.parse(document.getElementById('listAgents').getAttribute('data'));
    listCibles = JSON.parse(document.getElementById('listCibles').getAttribute('data'));
    listContacts = JSON.parse(document.getElementById('listContacts').getAttribute('data'));
    listSpeciality = JSON.parse(document.getElementById('listSpeciality').getAttribute('data'));

    // Sélection du menu déroulant des planques
    selectAddPlanque = document.getElementById('addElementPlanque');

    // Sélection des options des contacts
    selectAddContacts = document.getElementById('addElementContact');

    // Sélection des options d'agent principal
    selectAddMainAgent = document.getElementById('addElementMainAgent');

    // Sélection des options d'agent supp
    addElementOtherAgents = document.getElementById('addElementOtherAgents');

});

function clearElementById(elementId)
// Vide la div concernée
{
    const element = document.getElementById(elementId);
    if (element) {
        element.innerHTML = '';
    }
}

function filterById(data, propertyName, value)
// Filtrer un json en fonction d'un élément
// Param 1 : Le json
// Param 2 : le nom de la propriété du json à vérifier
// Param 3 : La valeur que cette propriété doit avoir
// return : les entrées du json qui respectent la condition
{
    return data.filter(entry => entry[propertyName] == value);
}

function filterByNotListId(data, propertyName, values)
// Filtrer un json en fonction d'un élément
// Param 1 : Le json
// Param 2 : le nom de la propriété du json à vérifier
// Param 3 : La liste de valeurs que cette propriété ne doit pas avoir
// return : les entrées du json qui respectent la condition
{

    return data.filter(entry => !values.includes(entry[propertyName]));
}

function getAgentsBySpecialityId(agents, specialityId) {
    // Filtrer les agents ayant la spécialité avec l'ID spécifié
    //Param : json des agents et numéro de spécialité
    // retourne les agents qui ont la spécialité demandée
    return agents.filter(agent => agent.specialities.some(spec => spec.id == specialityId));
}

function selectActiveElement(element)
// Récupére le l'élément sélectionné dans le form
{
    return element.value
}

function getCheckedCibleIdCountries(listCibles)
// Fonction pour récupérer les identifiants des pays des cibles cochées et retourner une liste
// Param : Json des cibles
// return : tableau des idcountry des cibles cochées
{
    // initialisation des listes
    let checkedCibleIds = [];
    let countryCibles = [];

    // Sélectionne toutes les cases à cocher avec la classe 'cible-checkbox'
    document.querySelectorAll('.cible-checkbox').forEach(function (checkbox) {
        // Vérifiez si la case est cochée
        if (checkbox.checked) {
            // Récupérez la valeur (id de la cible) de la case à cocher
            let cibleId = checkbox.value;
            // Ajoutez l'id de la cible à la liste des identifiants des cibles cochées
            checkedCibleIds.push(cibleId);
        }
    });

    checkedCibleIds.forEach(idCible => {
        // Trouver la cible correspondante dans le JSON
        let cible = listCibles.find(c => c.id == idCible);
        // Si la cible est trouvée, ajouter son countryCible au tableau
        if (cible) {
            countryCibles.push(cible.countryCible);
        }
    });

    // Retournez la liste des identifiants pays des cibles cochées
    return countryCibles;
}

function getCheckedMainAgentsId()
// Fonction pour récupérer les identifiants des agents Main cochées et retourner une liste
// return : tableau des idagents des agents cochées
{
    // initialisation des listes
    let checkedAgentIds = [];

    // Sélectionne toutes les cases à cocher dans la div
    const divElement = document.getElementById("addElementMainAgent");

    // Initialisez un tableau pour stocker les valeurs des cases à cocher
    const values = [];

    // Parcourez tous les éléments enfants de la div
    divElement.childNodes.forEach(child => {
        // Vérifiez si l'élément est une case à cocher
        if (child.tagName === "INPUT" && child.type === "checkbox" && child.checked) {
            // Si c'est le cas, ajoutez la valeur de la case à cocher au tableau
            values.push(parseInt(child.value));
        }
    });

    // retourne les valeurs récupérées
    return values;
}

function sanitizeHtml(text)
// Fonction pour nettoyer les éléments qui seront mis dans l'html
{
    // Créez un élément HTML temporaire de type "div"
    const tempHtml = document.createElement('span');

    // Affectez le texte reçu en tant que contenu texte de l'élément "tempHtml"
    tempHtml.textContent = text;

    // Utilisez .innerHTML pour récupérer le contenu de "tempHtml"
    // Cela va "neutraliser" ou "échapper" tout code HTML potentiellement malveillant
    return tempHtml.innerHTML;
}

function displaySelect(filters, resDom)
// Fonction pour ajouter des options dans un select
// Param : json des options avec les clés 'id' et clé 'valeur' +  element du dom pour print le res
// Return : Print les options du filtre dans le dom
{

    try {
        // Sélection de l'élément conteneur
        let container = resDom;

        // Vérifier si l'élément conteneur existe
        if (!container) {
            throw new Error('L\'élément conteneur n\'existe pas.');
        }

        // Effacer les options actuelles du menu déroulant
        resDom.innerHTML = '';

        // Ajouter une option vide au début
        let emptyOption = document.createElement("option");
        emptyOption.value = '';
        emptyOption.text = '';
        resDom.appendChild(emptyOption);


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

function displayCheckbox(filters, resDom, inputName, onChangeFunction = null) {
    // Fonction pour ajouter des inputs dans un checkbox
    // Param : json des options avec les clés 'id' et clé 'valeur' +  element du dom pour print le res + nom de la balise name + onchange facultatif
    // Return : Print les checkbox du filtre dans le dom
    try {
        // Sélection de l'élément conteneur
        let container = resDom;

        // Vérifier si l'élément conteneur existe
        if (!container) {
            throw new Error('L\'élément conteneur n\'existe pas.');
        }

        // Effacer le contenu actuel du conteneur
        container.innerHTML = '';

        // Boucle à travers chaque filtre dans le tableau
        filters.forEach((item) => {
            // Créer une case à cocher pour chaque filtre
            let checkbox = document.createElement("input");
            checkbox.type = "checkbox";
            checkbox.name = inputName;
            checkbox.value = sanitizeHtml(item.id);
            if (onChangeFunction !== null) {
                checkbox.setAttribute('onchange', onChangeFunction);
            }
            container.appendChild(checkbox);

            // Ajouter un espace
            container.appendChild(document.createTextNode(' '));

            // Créer une étiquette pour la case à cocher
            let label = document.createElement("label");
            label.textContent = sanitizeHtml(item.valeur);
            container.appendChild(label);

            // Ajouter un saut de ligne pour séparer les cases à cocher
            container.appendChild(document.createElement("br"));
        });
    } catch (error) {
        console.error('Une erreur s\'est produite lors de l\'affichage des filtres:', error);
        // Gérer l'erreur ici (si nécessaire)
    }
}

function updatePlanques(element) {
    // Affiche les planques du pays de la mission dans le select
    let value = selectActiveElement(element)
    let planquesInCountry = filterById(listPlanques, 'planqueCountryId', value)
    displaySelect(planquesInCountry, selectAddPlanque)
}

function updateContacts(element)
// Affiche les contacts du pays de la mission dans le select
{
    let value = selectActiveElement(element)
    let contactsInCountry = filterById(listContacts, 'contactCountryId', value)
    displayCheckbox(contactsInCountry, selectAddContacts, 'addContacts[]')
}

function getIdSpe(element) {
    // récupère l'id de la spécialité requise pour la mission
    idSpe = selectActiveElement(element)
    // Vider la div addElementOtherAgents
    clearElementById('addElementOtherAgents');
}

function updateMainAgents(element)
// Fonction pour mettre à jour la liste des agents principaux (pays différent de cible et bonne spécialité de mission)
{
    // Vider la div addElementOtherAgents
    clearElementById('addElementOtherAgents');

    // On récupère les id pays des cibles cochées
    let cibleIds = getCheckedCibleIdCountries(listCibles);

    //On filtre les agents pour avoir ceux qui ne sont pas du même pays des cibles
    let agentsNotInCibleCountry = filterByNotListId(listAgents, 'agentCountryId', cibleIds)

    //On filtre les agents qui ont la spécialité requise
    let agentsWithSpe = getAgentsBySpecialityId(agentsNotInCibleCountry, idSpe)
    displayCheckbox(agentsWithSpe, selectAddMainAgent, 'addAgent[]', 'updateOthersAgents(this)')
}

function updateOthersAgents()
// Fonction pour mettre à jour la liste des agents secondaires
{
    // On récupère les id pays des cibles cochées
    let cibleIds = getCheckedCibleIdCountries(listCibles);

    //On filtre les agents pour avoir ceux qui ne sont pas du même pays des cibles
    let agentsNotInCibleCountry = filterByNotListId(listAgents, 'agentCountryId', cibleIds)

    // On récupère les id des agents principaux déjà cochées
    let agentMainsId = getCheckedMainAgentsId();

    // On récupère la liste sans les id des agents principaux déjà cochées
    let agentIds = filterByNotListId(agentsNotInCibleCountry, 'id', agentMainsId);
    displayCheckbox(agentIds, addElementOtherAgents, 'addAgent[]')
}
