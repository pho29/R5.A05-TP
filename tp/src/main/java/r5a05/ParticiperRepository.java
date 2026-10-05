package r5a05;

import java.util.List;

import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Modifying;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;

public interface ParticiperRepository extends JpaRepository<Participer, Integer> {

    List<Participer> findByMatch_IdMatch(Integer idMatch);

    @Modifying(flushAutomatically = true)
    @Query(value = "DELETE FROM Participer WHERE id_match = :idMatch", nativeQuery = true)
    void supprimerParMatch(@Param("idMatch") Integer idMatch);
}