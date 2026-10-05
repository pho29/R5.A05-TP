package r5a05;

import java.util.ArrayList;
import java.util.HashSet;
import java.util.List;
import java.util.Map;
import java.util.Set;
import java.util.function.Function;
import java.util.stream.Collectors;

import org.springframework.http.HttpStatus;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;
import org.springframework.web.server.ResponseStatusException;

@Service
public class FeuilleMatchService {

    public static final int NB_TITULAIRES = 11;
    public static final int NB_REMPLACANTS_MAX = 7;
    private static final String GARDIEN = "Gardien de but";
    private static final Set<String> POSTES = Set.of(
            "Gardien de but", "Défenseur central", "Arrière", "Milieu défensif",
            "Milieu relayeur", "Milieu offensif", "Ailier", "Avant-centre");

    private final MatchsRepository matchsRepository;
    private final JoueurRepository joueurRepository;
    private final ParticiperRepository participerRepository;

    public FeuilleMatchService(MatchsRepository matchsRepository, JoueurRepository joueurRepository,
                               ParticiperRepository participerRepository) {
        this.matchsRepository = matchsRepository;
        this.joueurRepository = joueurRepository;
        this.participerRepository = participerRepository;
    }

    @Transactional(readOnly = true)
    public FeuilleMatchResponse lire(Integer idMatch) {
        Matchs match = trouverMatch(idMatch);
        return construireReponse(match, participerRepository.findByMatch_IdMatch(idMatch));
    }

    @Transactional
    public FeuilleMatchResponse enregistrer(Integer idMatch, FeuilleMatchRequest demande) {
        Matchs match = trouverMatch(idMatch);
        List<FeuilleMatchRequest.Selection> titulaires =
                demande.titulaires() == null ? List.of() : demande.titulaires();
        List<FeuilleMatchRequest.Selection> remplacants =
                demande.remplacants() == null ? List.of() : demande.remplacants();

        // 1. Effectifs
        if (titulaires.size() != NB_TITULAIRES) {
            throw refus("Feuille incomplète : " + NB_TITULAIRES + " titulaires sont requis (reçus : "
                    + titulaires.size() + ")");
        }
        if (remplacants.size() > NB_REMPLACANTS_MAX) {
            throw refus("Trop de remplaçants : " + NB_REMPLACANTS_MAX + " au maximum (reçus : "
                    + remplacants.size() + ")");
        }

        List<FeuilleMatchRequest.Selection> toutes = new ArrayList<>(titulaires);
        toutes.addAll(remplacants);

        // 2. Chaque sélection a un joueur et un poste valide
        for (FeuilleMatchRequest.Selection s : toutes) {
            if (s.idJoueur() == null) {
                throw refus("Une sélection n'a pas d'idJoueur");
            }
            if (s.poste() == null || !POSTES.contains(s.poste())) {
                throw refus("Poste invalide pour le joueur " + s.idJoueur() + " : " + s.poste());
            }
        }

        // 3. Exactement un gardien parmi les titulaires
        long nbGardiens = titulaires.stream().filter(s -> GARDIEN.equals(s.poste())).count();
        if (nbGardiens != 1) {
            throw refus("Il faut exactement un gardien de but parmi les titulaires (reçus : " + nbGardiens + ")");
        }

        // 4. Pas de doublon
        Set<Integer> ids = new HashSet<>();
        for (FeuilleMatchRequest.Selection s : toutes) {
            if (!ids.add(s.idJoueur())) {
                throw refus("Le joueur " + s.idJoueur() + " est sélectionné plusieurs fois");
            }
        }

        // 5. Joueurs existants et actifs
        Map<Integer, Joueur> joueurs = joueurRepository.findAllById(ids).stream()
                .collect(Collectors.toMap(Joueur::getIdJoueur, Function.identity()));
        for (Integer id : ids) {
            Joueur j = joueurs.get(id);
            if (j == null) {
                throw refus("Joueur inconnu : " + id);
            }
            if (!"Actif".equals(j.getStatutJoueur())) {
                throw refus(j.getPrenomJoueur() + " " + j.getNomJoueur()
                        + " n'est pas sélectionnable (statut : " + j.getStatutJoueur() + ")");
            }
        }

        // 6. Tout est valide : on remplace l'ancienne feuille
        participerRepository.supprimerParMatch(idMatch);
        List<Participer> lignes = new ArrayList<>();
        for (FeuilleMatchRequest.Selection s : titulaires) {
            lignes.add(creer(match, joueurs.get(s.idJoueur()), "Titulaire", s.poste()));
        }
        for (FeuilleMatchRequest.Selection s : remplacants) {
            lignes.add(creer(match, joueurs.get(s.idJoueur()), "Remplaçant", s.poste()));
        }
        participerRepository.saveAll(lignes);

        if ("À venir".equals(match.getStatutMatch())) {
            match.setStatutMatch("Préparé");
            matchsRepository.save(match);
        }
        return construireReponse(match, lignes);
    }

    private Matchs trouverMatch(Integer idMatch) {
        return matchsRepository.findById(idMatch)
                .orElseThrow(() -> new ResponseStatusException(HttpStatus.NOT_FOUND,
                        "Match introuvable : " + idMatch));
    }

    private ResponseStatusException refus(String message) {
        return new ResponseStatusException(HttpStatus.BAD_REQUEST, message);
    }

    private Participer creer(Matchs match, Joueur joueur, String statut, String poste) {
        Participer p = new Participer();
        p.setMatch(match);
        p.setJoueur(joueur);
        p.setStatutParticipation(statut);
        p.setRoleJoueur(poste);
        return p;
    }

    private FeuilleMatchResponse construireReponse(Matchs match, List<Participer> lignes) {
        List<FeuilleMatchResponse.Ligne> titulaires = new ArrayList<>();
        List<FeuilleMatchResponse.Ligne> remplacants = new ArrayList<>();
        for (Participer p : lignes) {
            Joueur j = p.getJoueur();
            FeuilleMatchResponse.Ligne ligne = new FeuilleMatchResponse.Ligne(
                    j.getIdJoueur(), j.getNomJoueur(), j.getPrenomJoueur(), p.getRoleJoueur());
            if ("Titulaire".equals(p.getStatutParticipation())) {
                titulaires.add(ligne);
            } else {
                remplacants.add(ligne);
            }
        }
        return new FeuilleMatchResponse(match.getIdMatch(), match.getStatutMatch(), titulaires, remplacants);
    }
}