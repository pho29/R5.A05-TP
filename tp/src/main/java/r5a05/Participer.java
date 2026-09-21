package r5a05;

import java.math.BigDecimal;

import jakarta.persistence.Column;
import jakarta.persistence.Entity;
import jakarta.persistence.FetchType;
import jakarta.persistence.GeneratedValue;
import jakarta.persistence.GenerationType;
import jakarta.persistence.Id;
import jakarta.persistence.JoinColumn;
import jakarta.persistence.ManyToOne;
import jakarta.persistence.Table;

@Entity
@Table(name = "Participer")
public class Participer {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Column(name = "id_participation")
    private Integer idParticipation;

    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "id_joueur", nullable = false)
    private Joueur joueur;

    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "id_match", nullable = false)
    private Matchs match;

    @Column(name = "temps_joue_minutes")
    private Integer tempsJoueMinutes = 0;

    @Column(name = "points_marques")
    private Integer pointsMarques = 0;

    @Column(name = "passes_decisives")
    private Integer passesDecisives = 0;

    @Column(name = "tirs_cadres")
    private Integer tirsCadres = 0;

    @Column(name = "tirs_non_cadres")
    private Integer tirsNonCadres = 0;

    @Column(name = "fautes_commises")
    private Integer fautesCommises = 0;

    @Column(name = "note_joueur", precision = 3, scale = 1)
    private BigDecimal noteJoueur = BigDecimal.ZERO;

    @Column(name = "statut_participation", length = 20)
    private String statutParticipation = "Titulaire";

    @Column(name = "role_joueur", length = 30)
    private String roleJoueur = "Milieu relayeur";

    @Column(name = "commentaires_participation", columnDefinition = "TEXT")
    private String commentairesParticipation;

    public Participer() {
    }

    public Integer getIdParticipation() {
        return idParticipation;
    }

    public Joueur getJoueur() {
        return joueur;
    }

    public void setJoueur(Joueur joueur) {
        this.joueur = joueur;
    }

    public Matchs getMatch() {
        return match;
    }

    public void setMatch(Matchs match) {
        this.match = match;
    }

    public Integer getTempsJoueMinutes() {
        return tempsJoueMinutes;
    }

    public void setTempsJoueMinutes(Integer tempsJoueMinutes) {
        this.tempsJoueMinutes = tempsJoueMinutes;
    }

    public Integer getPointsMarques() {
        return pointsMarques;
    }

    public void setPointsMarques(Integer pointsMarques) {
        this.pointsMarques = pointsMarques;
    }

    public Integer getPassesDecisives() {
        return passesDecisives;
    }

    public void setPassesDecisives(Integer passesDecisives) {
        this.passesDecisives = passesDecisives;
    }

    public Integer getTirsCadres() {
        return tirsCadres;
    }

    public void setTirsCadres(Integer tirsCadres) {
        this.tirsCadres = tirsCadres;
    }

    public Integer getTirsNonCadres() {
        return tirsNonCadres;
    }

    public void setTirsNonCadres(Integer tirsNonCadres) {
        this.tirsNonCadres = tirsNonCadres;
    }

    public Integer getFautesCommises() {
        return fautesCommises;
    }

    public void setFautesCommises(Integer fautesCommises) {
        this.fautesCommises = fautesCommises;
    }

    public BigDecimal getNoteJoueur() {
        return noteJoueur;
    }

    public void setNoteJoueur(BigDecimal noteJoueur) {
        this.noteJoueur = noteJoueur;
    }

    public String getStatutParticipation() {
        return statutParticipation;
    }

    public void setStatutParticipation(String statutParticipation) {
        this.statutParticipation = statutParticipation;
    }

    public String getRoleJoueur() {
        return roleJoueur;
    }

    public void setRoleJoueur(String roleJoueur) {
        this.roleJoueur = roleJoueur;
    }

    public String getCommentairesParticipation() {
        return commentairesParticipation;
    }

    public void setCommentairesParticipation(String commentairesParticipation) {
        this.commentairesParticipation = commentairesParticipation;
    }
}