package r5a05;

import java.util.List;
import java.util.Map;

import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.RestController;

@RestController
public class JoueurController {

    private final JoueurRepository joueurRepository;
    private final MatchsRepository matchsRepository;

    public JoueurController(JoueurRepository joueurRepository,
                            MatchsRepository matchsRepository) {
        this.joueurRepository = joueurRepository;
        this.matchsRepository = matchsRepository;
    }

    @GetMapping("/joueurs")
    public Map<String, Object> getJoueurs() {
        return Map.of(
                "status_code", 200,
                "status_message", "Liste des joueurs",
                "data", joueurRepository.findAll());
    }

    @GetMapping("/matchs")
    public Map<String, Object> getMatchs() {
        return Map.of(
                "status_code", 200,
                "status_message", "Liste des matchs",
                "data", matchsRepository.findAll());
    }
}