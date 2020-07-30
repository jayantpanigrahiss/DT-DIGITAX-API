package com.digitax.service.impl;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;

import com.digitax.model.Relation;
import com.digitax.repository.RelationRepository;
import com.digitax.service.RelationService;


import java.util.List;

@Service("RelationService")
public class RelationServiceImpl implements RelationService {
 
 @Autowired
 private RelationRepository relationRepository;
 
 

 @Override
 public Relation findUserByName(String name) {
  return relationRepository.findByName(name);
 }

 @Override
 public void saveRelation(Relation raltion) {
	 relationRepository.save(raltion);
 }

@Override
public List<Relation> getRelation() {
	List<Relation> relation = relationRepository.findAll();
    return relation;
	
}



}