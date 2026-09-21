package r5a05;

import java.math.BigDecimal;
import java.time.LocalDate;
import java.time.LocalDateTime;

import jakarta.persistence.Column;
import jakarta.persistence.Entity;
import jakarta.persistence.GeneratedValue;
import jakarta.persistence.GenerationType;
import jakarta.persistence.Id;
import jakarta.persistence.Table;

@Entity
@Table(name = "Joueur")
public class Joueur {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Column(name = "id_joueur")
    private Integer idJoueur;

    @Column(name = "numero_licence", nullable = false, unique = true, length = 50)
    private String numeroLicence;

    @Column(name = "nom_joueur", nullable = false, length = 100)
    private String nomJoueur;

    @Column(name = "prenom_joueur", nullable = false, length = 100)
    private String prenomJoueur;

    @Column(name = "date_naissance", nullable = false)
    private LocalDate dateNaissance;

    @Column(name = "taille_cm", precision = 5, scale = 2, nullable = false)
    private BigDecimal tailleCm;

    @Column(name = "poids_kg", precision = 5, scale = 2, nullable = false)
    private BigDecimal poidsKg;

    // Correspond à l'ENUM('Actif','Blessé','Suspendu','Absent') côté SQL
    @Column(name = "statut_joueur", length = 20)
    private String statutJoueur = "Actif";

    @Column(name = "commentaires_joueur", columnDefinition = "TEXT")
    private String commentairesJoueur;

    // Colonne gérée par la BDD (DEFAULT CURRENT_TIMESTAMP), on ne l'écrit jamais depuis Java
    @Column(name = "date_ajout", insertable = false, updatable = false)
    private LocalDateTime dateAjout;

    public Joueur() {
    }

    // Getters et setters

    public Integer getIdJoueur() {
        return idJoueur;
    }

    public String getNumeroLicence() {
        return numeroLicence;
    }

    public void setNumeroLicence(String numeroLicence) {
        this.numeroLicence = numeroLicence;
    }

    public String getNomJoueur() {
        return nomJoueur;
    }

    public void setNomJoueur(String nomJoueur) {
        this.nomJoueur = nomJoueur;
    }

    public String getPrenomJoueur() {
        return prenomJoueur;
    }

    public void setPrenomJoueur(String prenomJoueur) {
        this.prenomJoueur = prenomJoueur;
    }

    public LocalDate getDateNaissance() {
        return dateNaissance;
    }

    public void setDateNaissance(LocalDate dateNaissance) {
        this.dateNaissance = dateNaissance;
    }

    public BigDecimal getTailleCm() {
        return tailleCm;
    }

    public void setTailleCm(BigDecimal tailleCm) {
        this.tailleCm = tailleCm;
    }

    public BigDecimal getPoidsKg() {
        return poidsKg;
    }

    public void setPoidsKg(BigDecimal poidsKg) {
        this.poidsKg = poidsKg;
    }

    public String getStatutJoueur() {
        return statutJoueur;
    }

    public void setStatutJoueur(String statutJoueur) {
        this.statutJoueur = statutJoueur;
    }

    public String getCommentairesJoueur() {
        return commentairesJoueur;
    }

    public void setCommentairesJoueur(String commentairesJoueur) {
        this.commentairesJoueur = commentairesJoueur;
    }

    public LocalDateTime getDateAjout() {
        return dateAjout;
    }
}