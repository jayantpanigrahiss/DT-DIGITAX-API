package com.digitax.service;

import java.util.List;

import com.digitax.model.Relation;

public interface RelationService {
  
 public Relation findUserByName(String name);
 
 public void saveRelation(Relation relation);
 
 public List<Relation> getRelation();
}