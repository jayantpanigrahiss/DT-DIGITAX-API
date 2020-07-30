package com.digitax.repository;

import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import com.digitax.model.Relation;

@Repository
public interface RelationRepository 
    extends JpaRepository<Relation, Long> { 
	
	Relation findByName(String name);

}


