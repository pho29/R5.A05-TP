package r5a05;

import java.util.List;

public record FeuilleMatchResponse(Integer idMatch, String statutMatch,
                                   List<Ligne> titulaires, List<Ligne> remplacants) {

    public record Ligne(Integer idJoueur, String nomJoueur, String prenomJoueur, String poste) {
    }
}