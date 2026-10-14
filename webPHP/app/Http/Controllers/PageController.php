<?php

namespace App\Http\Controllers;

class PageController extends Controller
{
    protected function pages(): array
    {
        return [
            'centre-aide' => [
                'icon'  => 'fa-circle-question',
                'title' => "Centre d'aide",
                'lead'  => "Trouvez rapidement une réponse à vos questions sur la réservation, le paiement et votre séjour.",
                'sections' => [
                    ['h' => 'Comment réserver un hôtel ?', 'b' => "Utilisez la barre de recherche pour choisir une ville, des dates et le nombre de voyageurs, puis comparez les hôtels disponibles et confirmez votre réservation en quelques clics."],
                    ['h' => 'Comment payer ma réservation ?', 'b' => "Hôtelio accepte Orange Money, MTN Mobile Money, Wave, la carte bancaire, le virement et le paiement en espèces à la réception selon l'hôtel choisi."],
                    ['h' => 'Puis-je annuler ou modifier ma réservation ?', 'b' => "Oui, rendez-vous dans « Mes réservations » depuis votre espace personnel. Les conditions d'annulation dépendent de la politique de chaque hôtel, indiquée avant la confirmation."],
                    ['h' => "Je n'ai pas reçu ma confirmation", 'b' => "Vérifiez vos courriers indésirables, puis consultez « Mes réservations » dans votre espace client — toutes vos réservations et factures y sont toujours disponibles."],
                    ['h' => 'Besoin d\'aide supplémentaire ?', 'b' => "Notre équipe reste disponible par email à contact@hotelio.ci pour toute question non couverte ici."],
                ],
            ],
            'securite' => [
                'icon'  => 'fa-shield-halved',
                'title' => 'Sécurité',
                'lead'  => "La sécurité de vos données et de vos paiements est une priorité pour Hôtelio.",
                'sections' => [
                    ['h' => 'Paiements protégés', 'b' => "Toutes les transactions (Mobile Money, carte bancaire, virement) transitent par des connexions chiffrées. Hôtelio ne stocke jamais vos identifiants de paiement complets."],
                    ['h' => 'Protection du compte', 'b' => "Utilisez un mot de passe unique et robuste, et activez la double authentification depuis les paramètres de votre profil pour renforcer la sécurité de votre compte."],
                    ['h' => 'Hôtels vérifiés', 'b' => "Chaque établissement partenaire fournit un justificatif de propriété, examiné par notre équipe avant validation, afin de garantir des annonces fiables."],
                    ['h' => 'Signaler un problème', 'b' => "Si vous constatez une activité suspecte sur votre compte ou une annonce douteuse, contactez-nous immédiatement à securite@hotelio.ci."],
                ],
            ],
            'conditions-utilisation' => [
                'icon'  => 'fa-file-contract',
                'title' => "Conditions d'utilisation",
                'lead'  => "En utilisant Hôtelio, vous acceptez les conditions suivantes.",
                'sections' => [
                    ['h' => '1. Objet', 'b' => "Hôtelio est une plateforme de mise en relation entre voyageurs et établissements hôteliers en Côte d'Ivoire, permettant la recherche, la réservation et la gestion de séjours."],
                    ['h' => '2. Compte utilisateur', 'b' => "Vous êtes responsable de la confidentialité de vos identifiants et de l'exactitude des informations fournies lors de votre inscription."],
                    ['h' => '3. Réservations et paiements', 'b' => "Toute réservation confirmée constitue un engagement contractuel entre le voyageur et l'établissement. Les tarifs affichés incluent les conditions précisées avant paiement."],
                    ['h' => '4. Annulations', 'b' => "Une réservation d'hôtel payée en ligne peut être annulée tant que le séjour n'a pas commencé : 5% du montant payé sont retenus au titre des frais d'annulation (répartis entre la plateforme et l'établissement), et 95% vous sont remboursés sur le moyen de paiement (mobile money ou compte bancaire) que vous avez enregistré. Une commande boutique payée en ligne peut être annulée dans les 48 heures suivant l'achat, selon les mêmes conditions. Passé ces délais, ou pour un paiement effectué directement en espèces/virement à l'hôtel ou au vendeur, l'annulation et un éventuel remboursement sont à convenir directement avec l'établissement."],
                    ['h' => '5. Comptes hôteliers', 'b' => "Les prestataires s'engagent à fournir des informations exactes sur leurs établissements et à honorer les réservations confirmées sur la plateforme."],
                    ['h' => '6. Responsabilité', 'b' => "Hôtelio agit en tant qu'intermédiaire et ne saurait être tenu responsable des litiges liés directement au séjour ou aux prestations fournies par l'hôtel."],
                    ['h' => '7. Modification des conditions', 'b' => "Ces conditions peuvent être mises à jour ; toute modification substantielle sera communiquée aux utilisateurs."],
                ],
            ],
            'confidentialite' => [
                'icon'  => 'fa-user-shield',
                'title' => 'Politique de confidentialité',
                'lead'  => "Cette page explique quelles données Hôtelio collecte et comment elles sont utilisées.",
                'sections' => [
                    ['h' => 'Données collectées', 'b' => "Nom, email, téléphone, informations de réservation et de paiement, ainsi que les données techniques de navigation nécessaires au fonctionnement du site."],
                    ['h' => 'Utilisation des données', 'b' => "Vos données servent à gérer vos réservations, vous contacter au sujet de votre séjour, améliorer nos services et respecter nos obligations légales."],
                    ['h' => 'Partage des données', 'b' => "Vos informations de réservation sont partagées uniquement avec l'établissement concerné, dans la limite nécessaire à la gestion de votre séjour. Hôtelio ne vend aucune donnée personnelle à des tiers."],
                    ['h' => 'Cookies', 'b' => "Le site utilise des cookies pour maintenir votre session, mémoriser vos préférences de langue et d'affichage, et mesurer l'usage du site."],
                    ['h' => 'Vos droits', 'b' => "Vous pouvez consulter, corriger ou demander la suppression de vos données personnelles à tout moment en nous contactant à confidentialite@hotelio.ci."],
                ],
            ],
            'ressources-hotes' => [
                'icon'  => 'fa-building-user',
                'title' => 'Ressources hôtes',
                'lead'  => "Tout ce qu'il faut savoir pour bien démarrer et gérer votre établissement sur Hôtelio.",
                'sections' => [
                    ['h' => 'Créer votre compte hôtelier', 'b' => "Inscrivez-vous en tant qu'hôtelier, fournissez votre justificatif de propriété et votre compte sera validé par un administrateur sous 24 à 48 h."],
                    ['h' => 'Ajouter votre établissement', 'b' => "Une fois validé, renseignez les informations de votre hôtel (photos, chambres, tarifs, services) depuis votre espace prestataire."],
                    ['h' => 'Gérer vos réservations', 'b' => "Suivez les arrivées, départs et paiements en temps réel depuis votre tableau de bord, et gérez la disponibilité de vos chambres au quotidien."],
                    ['h' => 'Optimiser votre visibilité', 'b' => "Des photos de qualité, une description complète et des réponses rapides aux avis clients améliorent votre classement dans les résultats de recherche."],
                ],
            ],
            'forum' => [
                'icon'  => 'fa-comments',
                'title' => 'Forum communauté',
                'lead'  => "Le forum communautaire Hôtelio est en cours de préparation.",
                'sections' => [
                    ['h' => 'Bientôt disponible', 'b' => "Nous préparons un espace d'échange entre voyageurs et hôteliers pour partager conseils, retours d'expérience et bonnes pratiques."],
                    ['h' => 'En attendant', 'b' => "Pour toute question, notre Centre d'aide et notre équipe support restent disponibles pour vous accompagner."],
                ],
            ],
            'assistance-hotes' => [
                'icon'  => 'fa-headset',
                'title' => 'Assistance hôtes',
                'lead'  => "Une équipe dédiée accompagne les établissements partenaires de Hôtelio au quotidien.",
                'sections' => [
                    ['h' => 'Support dédié', 'b' => "Notre équipe assiste les hôteliers pour la mise en ligne de leur établissement, la gestion des réservations et la résolution de tout problème technique."],
                    ['h' => 'Nous contacter', 'b' => "Écrivez-nous à hotes@hotelio.ci ou depuis votre espace prestataire — nous répondons sous 24 h ouvrées."],
                    ['h' => "Pas encore partenaire ?", 'b' => "Créez votre compte hôtelier pour rejoindre Hôtelio et commencer à recevoir des réservations."],
                ],
            ],
        ];
    }

    public function show(string $slug)
    {
        $pages = $this->pages();

        abort_if(!isset($pages[$slug]), 404);

        return view('legal.show', [
            'page' => $pages[$slug],
            'slug' => $slug,
        ]);
    }
}
