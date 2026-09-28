package r5a05;

import java.util.List;

import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.DeleteMapping;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PathVariable;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.PutMapping;
import org.springframework.web.bind.annotation.RequestBody;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RestController;

@RestController
@RequestMapping("/matchs")
public class MatchController {

    private final MatchsRepository matchsRepository;

    public MatchController(MatchsRepository matchsRepository) {
        this.matchsRepository = matchsRepository;
    }

    @GetMapping
    public List<Matchs> getMatchs() {
        return matchsRepository.findAll();
    }

    @GetMapping("/{id}")
    public ResponseEntity<Matchs> getMatch(@PathVariable Integer id) {
        return matchsRepository.findById(id)
                .map(ResponseEntity::ok)
                .orElse(ResponseEntity.notFound().build());
    }

    @PostMapping
    public ResponseEntity<Matchs> creerMatch(@RequestBody Matchs match) {
        Matchs cree = matchsRepository.save(match);
        return ResponseEntity.status(HttpStatus.CREATED).body(cree);
    }

    @PutMapping("/{id}")
    public ResponseEntity<Matchs> modifierMatch(@PathVariable Integer id, @RequestBody Matchs donnees) {
        return matchsRepository.findById(id).map(match -> {
            match.setDateHeureMatch(donnees.getDateHeureMatch());
            match.setEquipeAdverse(donnees.getEquipeAdverse());
            match.setLieuMatch(donnees.getLieuMatch());
            match.setResultatMatch(donnees.getResultatMatch());
            match.setScoreEquipe(donnees.getScoreEquipe());
            match.setScoreAdverse(donnees.getScoreAdverse());
            match.setStatutMatch(donnees.getStatutMatch());
            match.setSchemaTactique(donnees.getSchemaTactique());
            match.setCommentairesMatch(donnees.getCommentairesMatch());
            return ResponseEntity.ok(matchsRepository.save(match));
        }).orElse(ResponseEntity.notFound().build());
    }

    @DeleteMapping("/{id}")
    public ResponseEntity<Void> supprimerMatch(@PathVariable Integer id) {
        if (!matchsRepository.existsById(id)) {
            return ResponseEntity.notFound().build();
        }
        matchsRepository.deleteById(id);
        return ResponseEntity.noContent().build();
    }
}