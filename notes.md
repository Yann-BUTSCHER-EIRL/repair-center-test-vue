- Début du test le 02/06/2026 à 11h10. 
  - L'environnement a été installé avec succès préalablement.

- Quand il y avait une référence unique pour un objet métier, j'ai pris la décision de laisser un "ID" technique pour simplifier le développement mais on pourrait choisir d'utiliser la référence comme clef primaire des tables concernées

- J'ai choisi de faire deux entités distincte LineLabour et LinePart pour les lignes mais on pourrait imaginer un système avec héritage, que je n'ai pas choisi par simplicité. Pour autant et afin de garantir une unicité dans la façon de traiter une ligne de devis, j'ai choisi d'implémenter une interface commune.

- Pour avoir plus de flexibilité sur le taux horaire des lignes "Main d'Oeuvre", j'ai choisi de pré-remplir avec une valeur enregistrée dans une table de référence (LabourType) mais d'avoir la possibilité de modifier le taux au besoin.
On aurait pu aussi choisir un taux qui dépend de plusieurs facteurs tel que décrit dans l'énoncé mais ça me semblait trop ambitieux pour le scope du test

- J'ai fais une pause de 12h30 à 12h45 pour avoir un peu de sucre dans l'organisme !

- J'ai voulu avancer les interfaces une fois que mon test unitaires marchaient mais j'ai oublié d'implémenter les API AVANT
    - J'ai laissé les infos du vehicule de côté par manque de temps mais on pourrait facilement ajouter ces informations sur le devis/en BDD
    - J'ai mis le template du devis dans le détail de l'order pour gagner du temps mais on peut imaginer une page/un composant à part, qu'on pourrait aussi imprimer via Ctrl+P et un peu de CSS.

- J'ai voulu générer une migration doctrine à partir de mes entity mais j'ai eu un petit soucis de type sur les clefs externes de Part (String d'un côté, int de l'autre). Rien d'insurmontable mais je n'ai juste pas eu le temps de corriger pour le faire marcher

- Au final je n'ai pas grand chose à présenter si ce n'est du code, mais je serai ravi d'expliquer ma démarche lors d'un debrief

- Fin du test le le 02/06/2026 à 13h41. Durée totale 2h16.