package r5a05;

import java.time.LocalDateTime;

import jakarta.persistence.Column;
import jakarta.persistence.Entity;
import jakarta.persistence.GeneratedValue;
import jakarta.persistence.GenerationType;
import jakarta.persistence.Id;
import jakarta.persistence.Table;

@Entity
@Table(name = "Matchs")
public class Matchs {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Column(name = "id_match")
    private Integer idMatch;

    @Column(name = "date_heure_match", nullable = false)
    private LocalDateTime dateHeureMatch;

    @Column(name = "equipe_adverse", nullable = false, length = 200)
    private String equipeAdverse;

    @Column(name = "lieu_match", nullable = false, length = 20)
    private String lieuMatch; // 'Domicile' ou 'Extérieur'

    @Column(name = "resultat_match", length = 20)
    private String resultatMatch = "À venir";

    @Column(name = "score_equipe")
    private Integer scoreEquipe = 0;

    @Column(name = "score_adverse")
    private Integer scoreAdverse = 0;

    @Column(name = "statut_match", length = 20)
    private String statutMatch = "À venir";

    @Column(name = "schema_tactique", length = 20)
    private String schemaTactique = "4-4-2";

    @Column(name = "commentaires_match", columnDefinition = "TEXT")
    private String commentairesMatch;

    @Column(name = "date_creation_match", insertable = false, updatable = false)
    private LocalDateTime dateCreationMatch;

    public Matchs() {
    }

    public Integer getIdMatch() {
        return idMatch;
    }

    public LocalDateTime getDateHeureMatch() {
        return dateHeureMatch;
    }

    public void setDateHeureMatch(LocalDateTime dateHeureMatch) {
        this.dateHeureMatch = dateHeureMatch;
    }

    public String getEquipeAdverse() {
        return equipeAdverse;
    }

    public void setEquipeAdverse(String equipeAdverse) {
        this.equipeAdverse = equipeAdverse;
    }

    public String getLieuMatch() {
        return lieuMatch;
    }

    public void setLieuMatch(String lieuMatch) {
        this.lieuMatch = lieuMatch;
    }

    public String getResultatMatch() {
        return resultatMatch;
    }

    public void setResultatMatch(String resultatMatch) {
        this.resultatMatch = resultatMatch;
    }

    public Integer getScoreEquipe() {
        return scoreEquipe;
    }

    public void setScoreEquipe(Integer scoreEquipe) {
        this.scoreEquipe = scoreEquipe;
    }

    public Integer getScoreAdverse() {
        return scoreAdverse;
    }

    public void setScoreAdverse(Integer scoreAdverse) {
        this.scoreAdverse = scoreAdverse;
    }

    public String getStatutMatch() {
        return statutMatch;
    }

    public void setStatutMatch(String statutMatch) {
        this.statutMatch = statutMatch;
    }

    public String getSchemaTactique() {
        return schemaTactique;
    }

    public void setSchemaTactique(String schemaTactique) {
        this.schemaTactique = schemaTactique;
    }

    public String getCommentairesMatch() {
        return commentairesMatch;
    }

    public void setCommentairesMatch(String commentairesMatch) {
        this.commentairesMatch = commentairesMatch;
    }

    public LocalDateTime getDateCreationMatch() {
        return dateCreationMatch;
    }
}