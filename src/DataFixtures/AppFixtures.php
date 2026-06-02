<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use App\Entity\Part;
use App\Entity\RepairOrder;
use App\Entity\Quote;
use App\Entity\LabourType;
use App\Entity\LineLabour;
use App\Entity\LinePart;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $customers = [];
        $customerData = [
            ['name' => 'Jean Dupont',    'email' => 'jean.dupont@example.com',    'phone' => '0601020304'],
            ['name' => 'Marie Martin',   'email' => 'marie.martin@example.com',   'phone' => '0605060708'],
            ['name' => 'Paul Bernard',   'email' => 'paul.bernard@example.com',   'phone' => null],
            ['name' => 'Sophie Leroy',   'email' => 'sophie.leroy@example.com',   'phone' => '0611223344'],
            ['name' => 'Lucas Moreau',   'email' => null,                          'phone' => '0655443322'],
        ];

        foreach ($customerData as $cd) {
            $customer        = new Customer();
            $customer->name  = $cd['name'];
            $customer->email = $cd['email'];
            $customer->phone = $cd['phone'];
            $manager->persist($customer);
            $customers[] = $customer;
        }

        // Catalogue de pièces de l'atelier (cf. docs/METIER-CARROSSERIE.md).
        // C'est la matière première des futures lignes de devis : libellé, référence, prix de vente HT.
        $catalogue = [
            ['reference' => 'PRC-AV-001', 'label' => 'Pare-chocs avant',         'salePrice' => 180.00],
            ['reference' => 'PRC-AR-001', 'label' => 'Pare-chocs arrière',       'salePrice' => 165.00],
            ['reference' => 'CAP-001',    'label' => 'Capot',                    'salePrice' => 320.00],
            ['reference' => 'AILE-AVG',   'label' => 'Aile avant gauche',        'salePrice' => 145.00],
            ['reference' => 'AILE-AVD',   'label' => 'Aile avant droite',        'salePrice' => 145.00],
            ['reference' => 'PORTE-CDG',  'label' => 'Porte conducteur',         'salePrice' => 420.00],
            ['reference' => 'RETRO-D',    'label' => 'Rétroviseur droit',        'salePrice' => 135.00],
            ['reference' => 'RETRO-G',    'label' => 'Rétroviseur gauche',       'salePrice' => 135.00],
            ['reference' => 'OPT-AVD',    'label' => 'Optique avant droit',      'salePrice' => 210.00],
            ['reference' => 'OPT-AVG',    'label' => 'Optique avant gauche',     'salePrice' => 210.00],
            ['reference' => 'PB-AVD',     'label' => 'Pare-brise',               'salePrice' => 380.00],
            ['reference' => 'VIT-PCDG',   'label' => 'Vitre porte conducteur',   'salePrice' => 95.00],
            ['reference' => 'PEINT-1L',   'label' => 'Peinture (1L)',            'salePrice' => 45.00],
            ['reference' => 'VERNIS-1L',  'label' => 'Vernis (1L)',              'salePrice' => 38.00],
            ['reference' => 'APPRET-1L',  'label' => 'Apprêt de carrosserie',    'salePrice' => 28.50],
            ['reference' => 'MASTIC',     'label' => 'Mastic carrosserie',       'salePrice' => 18.00],
            ['reference' => 'ABRASIF-80', 'label' => 'Papier abrasif P80',       'salePrice' => 4.50],
            ['reference' => 'ABRASIF-400','label' => 'Papier abrasif P400',      'salePrice' => 5.20],
            ['reference' => 'RUBAN-MASK', 'label' => 'Ruban de masquage',        'salePrice' => 6.90],
            ['reference' => 'ANTICORR',   'label' => 'Traitement anticorrosion', 'salePrice' => 22.00],
        ];

        $parts = [];
        foreach ($catalogue as $pd) {
            $part            = new Part();
            $part->reference = $pd['reference'];
            $part->label     = $pd['label'];
            $part->salePrice = $pd['salePrice'];
            $manager->persist($part);
            $parts[] = $part;
        }

        // Quelques ordres de réparation d'exemple. Le devis (lignes + total) est à construire :
        // pour l'instant le total reste à 0.
        $ordersData = [
            ['customer' => $customers[0], 'status' => 'IN_PROGRESS',   'description' => 'Remplacement pare-chocs avant, retouche peinture capot'],
            ['customer' => $customers[1], 'status' => 'WAITING_PARTS', 'description' => 'Carrosserie porte conducteur enfoncée, vitre brisée'],
            ['customer' => $customers[2], 'status' => 'DONE',          'description' => 'Débosselage aile arrière gauche'],
            ['customer' => $customers[3], 'status' => 'PENDING',       'description' => 'Remplacement capot complet + traitement anticorrosion'],
            ['customer' => $customers[0], 'status' => 'DELIVERED',     'description' => 'Remplacement rétroviseur droit cassé'],
            ['customer' => $customers[4], 'status' => 'CANCELLED',     'description' => 'Réparation griffures carrosserie — annulé par le client'],
        ];

        $repairOrders = [];
        foreach ($ordersData as $od) {
            $repairOrder              = new RepairOrder();
            $repairOrder->reference   = 'OR-' . strtoupper(substr(md5(uniqid()), 0, 8));
            $repairOrder->customer    = $od['customer'];
            $repairOrder->status      = $od['status'];
            $repairOrder->description = $od['description'];
            $repairOrder->createdAt   = new \DateTime('-' . rand(1, 30) . ' days');
            $manager->persist($repairOrder);
            $repairOrders[] = $repairOrder;
        }

        $labourTypesData = [
            ['label' => 'Tôlerie', 'description' => 'Débosselage, redressage, remplacement d\'éléments de carrosserie', 'indicativeHourlyRate' => 65],
            ['label' => 'Peinture', 'description' => 'Préparation, apprêt, mise en peinture, vernis', 'indicativeHourlyRate' => 70],
            ['label' => 'Mécanique', 'description' => 'Dépose/repose mécanique, organes', 'indicativeHourlyRate' => 60],
        ];

        $labourTypes = [];
        foreach ($labourTypesData as $lt) {
            $labourType = new LabourType();
            $labourType->setLabel($lt['label']);
            $labourType->setDescription($lt['description']);
            $labourType->setIndicativeHourlyRate($lt['indicativeHourlyRate']);
            $manager->persist($labourType);
            $labourTypes[] = $labourType;
        }

        $quote = new Quote();
        $quote->setReference('DEV-00001');
        $quote->setCreatedAt(new DateTimeImmutable());
        $quote->setRepairOrder($repairOrders[0]);
        $manager->persist($quote);

        $linePart = new LinePart();
        $linePart->setPart($parts[12]);
        $linePart->setQuantity(1);
        $linePart->setTaxPercentage(20);
        $quote->addLinesPart($linePart);
        $manager->persist($linePart);
        $manager->persist($quote);

        $lineLabour = new LineLabour();
        $lineLabour->setLabourType($labourTypes[0]);
        $lineLabour->setTimeSpent(1.5);
        $lineLabour->setHourlyRate($labourTypes[0]->getIndicativeHourlyRate());
        $lineLabour->setQuote($quote);
        $lineLabour->setTaxPercentage(5.5);
        $quote->addLinesLabour($lineLabour);
        $manager->persist($lineLabour);
        $manager->persist($lineLabour);

        $manager->flush();
    }
}
