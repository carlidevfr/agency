const recupererToutesDonnees = document.getElementById('recuperer-toutes-donnees');
console.log(recupererToutesDonnees)

function displayMissions(missions) {
    try {
        // Sélection de l'élément conteneur
        const container = document.getElementById('missions-container');

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
                <h2>${mission.title}</h2>
                <p><strong>Code Name:</strong> ${mission.codeName}</p>
                <p><strong>Description:</strong> ${mission.description}</p>
                <p><strong>Begin Date:</strong> ${mission.beginDate}</p>
                <p><strong>End Date:</strong> ${mission.endDate}</p>
                <!-- Ajoutez d'autres informations de mission si nécessaire -->

                <!-- Vous pouvez également ajouter des styles CSS ici -->
            `;

            // Ajouter la div de mission au conteneur
            container.appendChild(missionDiv);
        });
    } catch (error) {
        console.error('Une erreur s\'est produite lors de l\'affichage des missions:', error);
        // Gérer l'erreur ici (si nécessaire)
    }
}

function fetchData() {
    // URL de l'API que vous souhaitez interroger
    const apiUrl = 'http://localhost/Agency/apiGetmissions';

    // Utilisation de fetch pour interroger l'API
    fetch(apiUrl)
        .then((response) => {
            if (response.ok) {
                return response.json()
            } else {
                console.error('Erreur de récupération des données:' + response.status)
            }
        })
        .then((data) => {
            console.log(data)
            displayMissions(data)
        })
        .catch((error) => { console.error('Erreur de récupération des données:' + error) });
}

recupererToutesDonnees.addEventListener('click', () => {
    fetchData()
});
