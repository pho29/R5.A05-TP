package r5a05;

import java.util.List;

public record FeuilleMatchRequest(List<Selection> titulaires, List<Selection> remplacants) {

    public record Selection(Integer idJoueur, String poste) {
    }
}