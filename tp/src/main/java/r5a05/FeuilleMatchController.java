package r5a05;

import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PathVariable;
import org.springframework.web.bind.annotation.PutMapping;
import org.springframework.web.bind.annotation.RequestBody;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RestController;

@RestController
@RequestMapping("/matchs/{idMatch}/feuille")
public class FeuilleMatchController {

    private final FeuilleMatchService feuilleMatchService;

    public FeuilleMatchController(FeuilleMatchService feuilleMatchService) {
        this.feuilleMatchService = feuilleMatchService;
    }

    @GetMapping
    public FeuilleMatchResponse lire(@PathVariable Integer idMatch) {
        return feuilleMatchService.lire(idMatch);
    }

    @PutMapping
    public FeuilleMatchResponse enregistrer(@PathVariable Integer idMatch,
                                            @RequestBody FeuilleMatchRequest demande) {
        return feuilleMatchService.enregistrer(idMatch, demande);
    }
}