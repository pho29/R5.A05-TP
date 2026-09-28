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
@RequestMapping("/joueurs")
public class JoueurController {

    private final JoueurRepository joueurRepository;

    public JoueurController(JoueurRepository joueurRepository) {
        this.joueurRepository = joueurRepository;
    }

    @GetMapping
    public List<Joueur> getJoueurs() {
        return joueurRepository.findAll();
    }

    @GetMapping("/{id}")
    public ResponseEntity<Joueur> getJoueur(@PathVariable Integer id) {
        return joueurRepository.findById(id)
                .map(ResponseEntity::ok)
                .orElse(ResponseEntity.notFound().build());
    }

    @PostMapping
    public ResponseEntity<Joueur> creerJoueur(@RequestBody Joueur joueur) {
        Joueur cree = joueurRepository.save(joueur);
        return ResponseEntity.status(HttpStatus.CREATED).body(cree);
    }

    @PutMapping("/{id}")
    public ResponseEntity<Joueur> modifierJoueur(@PathVariable Integer id, @RequestBody Joueur donnees) {
        return joueurRepository.findById(id).map(joueur -> {
            joueur.setNumeroLicence(donnees.getNumeroLicence());
            joueur.setNomJoueur(donnees.getNomJoueur());
            joueur.setPrenomJoueur(donnees.getPrenomJoueur());
            joueur.setDateNaissance(donnees.getDateNaissance());
            joueur.setTailleCm(donnees.getTailleCm());
            joueur.setPoidsKg(donnees.getPoidsKg());
            joueur.setStatutJoueur(donnees.getStatutJoueur());
            joueur.setCommentairesJoueur(donnees.getCommentairesJoueur());
            return ResponseEntity.ok(joueurRepository.save(joueur));
        }).orElse(ResponseEntity.notFound().build());
    }

    @DeleteMapping("/{id}")
    public ResponseEntity<Void> supprimerJoueur(@PathVariable Integer id) {
        if (!joueurRepository.existsById(id)) {
            return ResponseEntity.notFound().build();
        }
        joueurRepository.deleteById(id);
        return ResponseEntity.noContent().build();
    }
}